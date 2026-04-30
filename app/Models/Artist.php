<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artist extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'bio', 'photo', 'hero_image', 'gallery',
        'birth_date', 'death_date', 'origin', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'death_date' => 'date',
            'is_active' => 'boolean',
            'gallery' => 'array',
        ];
    }

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class, 'band_artist')
            ->withPivot(['role', 'joined_year', 'left_year', 'is_current'])
            ->withTimestamps()
            ->orderByPivot('joined_year');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function suggestions(): MorphMany
    {
        return $this->morphMany(EditSuggestion::class, 'suggestable');
    }

    public function currentBands(): BelongsToMany
    {
        return $this->bands()->wherePivot('is_current', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
