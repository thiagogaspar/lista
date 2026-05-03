<?php

namespace App\Services;

use App\Models\Band;
use App\Models\BandRelationship;
use App\Models\Genre;
use App\Models\Label;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BandService
{
    public function getPaginated(array $filters = [], int $perPage = 20, string $sort = 'name', string $dir = 'asc'): LengthAwarePaginator
    {
        $allowedSorts = ['name', 'formed_year', 'created_at', 'origin', 'genre'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'name';
        $dir = in_array(strtolower($dir), ['asc', 'desc']) ? $dir : 'asc';

        return Band::query()
            ->when($filters['genre'] ?? null, fn ($q, $v) => $q->whereRelation('genres', 'slug', $v))
            ->when($filters['label'] ?? null, fn ($q, $v) => $q->whereRelation('label', 'slug', $v))
            ->when($filters['year'] ?? null, fn ($q, $v) => $q->where('formed_year', '<=', $v)->where(fn ($sq) => $sq->whereNull('dissolved_year')->orWhere('dissolved_year', '>=', $v)))
            ->when($filters['origin'] ?? null, fn ($q, $v) => $q->where('origin', $v))
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->with('genres', 'label')
            ->withCount('artists')
            ->orderBy($sort, $dir)
            ->paginate($perPage);
    }

    public function getBySlug(string $slug): Band
    {
        return Band::where('slug', $slug)
            ->with(['genres', 'label', 'approvedTags', 'albums', 'artists' => fn ($q) => $q->orderByPivot('joined_year')])
            ->firstOrFail();
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Band::where('is_active', true)->with('genres', 'label')->inRandomOrder()->limit($limit)->get();
    }

    public function getGenres(): array
    {
        return Genre::orderBy('name')->pluck('name', 'slug')->all();
    }

    public function getLabels(): array
    {
        return Label::orderBy('name')->pluck('name', 'slug')->all();
    }

    public function getOrigins(): array
    {
        return Band::whereNotNull('origin')->select('origin')->distinct()->orderBy('origin')->pluck('origin')->all();
    }

    public function getRelated(Band $band): array
    {
        return BandRelationship::where('parent_band_id', $band->id)
            ->orWhere('child_band_id', $band->id)
            ->with(['parentBand', 'childBand'])
            ->orderBy('year')->get()->all();
    }

    public function flushCache(Band $band): void
    {
        Cache::forget('home.stats');
    }
}
