<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Band;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleBand(string $slug, Request $request): JsonResponse
    {
        $band = Band::whereSlug($slug)->firstOrFail();

        return $this->toggle($band, $request);
    }

    public function toggleArtist(string $slug, Request $request): JsonResponse
    {
        $artist = Artist::whereSlug($slug)->firstOrFail();

        return $this->toggle($artist, $request);
    }

    private function toggle($model, Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $favorite = $user->favorites()
            ->where('favoriteable_type', $model->getMorphClass())
            ->where('favoriteable_id', $model->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $favorited = false;
        } else {
            $user->favorites()->create([
                'favoriteable_type' => $model->getMorphClass(),
                'favoriteable_id' => $model->id,
            ]);
            $favorited = true;
        }

        return response()->json([
            'favorited' => $favorited,
            'count' => $model->favorites()->count(),
        ]);
    }
}
