<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;

class ArtistController extends Controller
{
    public function index()
    {
        return ArtistResource::collection(
            Artist::with(['tags'])->paginate(20)
        );
    }

    public function show(string $slug)
    {
        return new ArtistResource(
            Artist::where('slug', $slug)
                ->with(['bands' => fn ($q) => $q->orderByPivot('joined_year'), 'tags'])
                ->firstOrFail()
        );
    }
}
