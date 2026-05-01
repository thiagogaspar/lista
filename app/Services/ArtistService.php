<?php

namespace App\Services;

use App\Models\Artist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ArtistService
{
    public function getPaginated(array $filters = [], int $perPage = 20, string $sort = 'name', string $dir = 'asc'): LengthAwarePaginator
    {
        $allowedSorts = ['name', 'created_at', 'origin'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'name';
        $dir = in_array(strtolower($dir), ['asc', 'desc']) ? $dir : 'asc';

        return Artist::query()
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->with('tags')
            ->orderBy($sort, $dir)
            ->paginate($perPage);
    }

    public function getBySlug(string $slug): Artist
    {
        return Artist::where('slug', $slug)
            ->with(['bands' => fn ($q) => $q->orderByPivot('joined_year'), 'approvedTags'])
            ->firstOrFail();
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return Artist::where('is_active', true)->inRandomOrder()->limit($limit)->get();
    }

    public function flushCache(Artist $artist): void
    {
        Cache::forget('home.stats');
    }
}
