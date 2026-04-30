<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BandRelationship extends Model
{
    protected $fillable = [
        'parent_band_id', 'child_band_id',
        'type', 'description', 'year',
    ];

    public function parentBand(): BelongsTo
    {
        return $this->belongsTo(Band::class, 'parent_band_id');
    }

    public function childBand(): BelongsTo
    {
        return $this->belongsTo(Band::class, 'child_band_id');
    }

    public static function types(): array
    {
        return [
            'split_into' => 'Split Into',
            'evolved_into' => 'Evolved Into',
            'members_formed' => 'Members Formed',
            'side_project' => 'Side Project',
            'merged_into' => 'Merged Into',
            'rebranded_as' => 'Rebranded As',
        ];
    }
}
