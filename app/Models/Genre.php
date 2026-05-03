<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class, 'band_genre');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
