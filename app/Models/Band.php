<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Band extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'bio', 'photo', 'hero_image', 'gallery',
        'formed_year', 'dissolved_year', 'origin', 'genre', 'label_id', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'gallery' => 'array',
        ];
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'band_artist')
            ->withPivot(['role', 'joined_year', 'left_year', 'is_current'])
            ->withTimestamps()
            ->orderByPivot('joined_year');
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class)->orderBy('release_year');
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

    public function currentArtists(): BelongsToMany
    {
        return $this->artists()->wherePivot('is_current', true);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'band_genre');
    }

    public function relatedFrom(): HasMany
    {
        return $this->hasMany(BandRelationship::class, 'child_band_id');
    }

    public function relatedTo(): HasMany
    {
        return $this->hasMany(BandRelationship::class, 'parent_band_id');
    }

    public function allRelationships()
    {
        return BandRelationship::where('parent_band_id', $this->id)
            ->orWhere('child_band_id', $this->id)
            ->with(['parentBand', 'childBand']);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
