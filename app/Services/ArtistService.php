<?php

namespace App\Services;

use App\Models\Artist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ArtistService
{
    public function getPaginated(array $filters = [], int $perPage = 20, string $sort = 'name', string $dir = 'asc'): LengthAwarePaginator
    {
        return Artist::query()
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->orderBy($sort, $dir)
            ->paginate($perPage);
    }

    public function getBySlug(string $slug): Artist
    {
        return Artist::where('slug', $slug)
            ->with(['bands' => fn ($q) => $q->orderByPivot('joined_year'), 'tags'])
            ->firstOrFail();
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return Artist::where('is_active', true)->inRandomOrder()->limit($limit)->get();
    }

    public function flushCache(Artist $artist): void {}
}
