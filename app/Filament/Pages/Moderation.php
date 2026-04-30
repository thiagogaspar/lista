<?php

namespace App\Filament\Pages;

use App\Models\Comment;
use App\Models\EditSuggestion;
use App\Models\Tag;
use Filament\Pages\Page;

class Moderation extends Page
{
    protected string $view = 'filament.pages.moderation';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public function getViewData(): array
    {
        return [
            'pendingComments' => Comment::where('is_approved', false)->with('commentable')->latest()->limit(10)->get(),
            'pendingTags' => Tag::where('is_approved', false)->latest()->limit(10)->get(),
            'pendingSuggestions' => EditSuggestion::where('status', 'pending')->with('suggestable')->latest()->limit(10)->get(),
        ];
    }
}
