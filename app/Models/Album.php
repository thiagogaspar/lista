<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Album extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'band_id', 'title', 'slug', 'release_year',
        'cover_art', 'description', 'tracklist',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'tracklist' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Album $album) {
            if (empty($album->slug)) {
                $album->slug = Str::slug($album->title);
            }
        });
    }

    public function band(): BelongsTo
    {
        return $this->belongsTo(Band::class);
    }
}
