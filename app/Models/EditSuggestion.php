<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EditSuggestion extends Model
{
    protected $fillable = [
        'user_id', 'suggestable_type', 'suggestable_id',
        'field', 'current_value', 'suggested_value',
        'status', 'moderated_by', 'moderated_at',
    ];

    public function suggestable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
