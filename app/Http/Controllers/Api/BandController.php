<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BandResource;
use App\Models\Band;

class BandController extends Controller
{
    public function index()
    {
        return BandResource::collection(
            Band::with(['genres', 'label', 'tags'])->withCount('artists')->paginate(20)
        );
    }

    public function show(string $slug)
    {
        return new BandResource(
            Band::where('slug', $slug)
                ->with(['genres', 'label', 'tags', 'albums', 'artists' => fn ($q) => $q->orderByPivot('joined_year')])
                ->firstOrFail()
        );
    }
}
