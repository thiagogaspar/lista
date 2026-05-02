<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Label extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'country', 'founded_year',
        'website', 'logo', 'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Label $label) {
            if (empty($label->slug)) {
                $label->slug = Str::slug($label->name);
            }
        });
    }

    public function bands(): HasMany
    {
        return $this->hasMany(Band::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
