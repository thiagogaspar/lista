<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Services\BandService;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __invoke(string $slug, Request $request, BandService $service)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();

        $allowedSorts = ['name', 'formed_year', 'created_at', 'origin', 'genre'];
        $sort = in_array($request->get('sort', 'name'), $allowedSorts) ? $request->get('sort') : 'name';
        $dir = in_array(strtolower($request->get('dir', 'asc')), ['asc', 'desc']) ? $request->get('dir') : 'asc';

        $bands = $service->getPaginated(
            filters: ['genre' => $slug] + $request->only(['year', 'origin', 'search', 'label']),
            sort: $sort,
            dir: $dir,
        );

        return view('bands.index', [
            'bands' => $bands,
            'genres' => $service->getGenres(),
            'labels' => $service->getLabels(),
            'origins' => $service->getOrigins(),
            'filters' => ['genre' => $slug],
            'sort' => $sort,
            'dir' => $dir,
            'genreName' => $genre->name,
        ]);
    }
}
