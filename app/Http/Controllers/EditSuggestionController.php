<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Band;
use Illuminate\Http\Request;

class EditSuggestionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'suggestable_type' => 'required|in:band,artist',
            'suggestable_id' => 'required|integer',
            'field' => 'required|string|max:100',
            'suggested_value' => 'required|string|max:5000',
        ]);

        $modelClass = $data['suggestable_type'] === 'band' ? Band::class : Artist::class;
        $model = $modelClass::findOrFail($data['suggestable_id']);

        $model->suggestions()->create([
            'user_id' => $request->user()->id,
            'field' => $data['field'],
            'current_value' => $model->{$data['field']} ?? '',
            'suggested_value' => $data['suggested_value'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Suggestion submitted!');
    }
}
