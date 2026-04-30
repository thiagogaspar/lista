<?php

namespace App\Http\Controllers;

use App\Models\EditSuggestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load([
            'favorites.favoriteable',
            'comments' => fn ($q) => $q->with('commentable')->latest(),
        ]);

        $suggestions = EditSuggestion::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('profile.index', compact('user', 'suggestions'));
    }

    public function show(User $user): View
    {
        $user->load([
            'favorites.favoriteable',
            'comments' => fn ($q) => $q->where('is_approved', true)->with('commentable')->latest(),
        ]);

        return view('profile.show', compact('user'));
    }
}
