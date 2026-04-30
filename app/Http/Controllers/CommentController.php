<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Band;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'commentable_type' => 'required|in:band,artist',
            'commentable_id' => 'required|integer',
            'body' => 'required|string|max:1000',
        ]);

        $modelClass = $data['commentable_type'] === 'band' ? Band::class : Artist::class;
        $model = $modelClass::findOrFail($data['commentable_id']);

        $model->comments()->create([
            'user_id' => auth()->id(),
            'body' => $data['body'],
            'is_approved' => false,
        ]);

        return back()->with('success', 'Comment submitted for review.');
    }
}
