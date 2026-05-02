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
        $sort = $request->string('sort', 'name')->toString();
        $dir = $request->string('dir', 'asc')->toString();
        $sort = in_array($sort, $allowedSorts) ? $sort : 'name';
        $dir = in_array($dir, ['asc', 'desc']) ? $dir : 'asc';

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
