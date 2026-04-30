<?php

namespace App\Http\Controllers;

use App\Services\BandService;
use App\Services\GenealogyService;
use Illuminate\Http\Request;

class BandController extends Controller
{
    public function index(Request $request, BandService $service)
    {
        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'asc');

        return view('bands.index', [
            'bands' => $service->getPaginated($request->only(['genre', 'year', 'search', 'label', 'origin']), sort: $sort, dir: $dir),
            'genres' => $service->getGenres(),
            'labels' => $service->getLabels(),
            'origins' => $service->getOrigins(),
            'filters' => $request->only(['genre', 'year', 'search', 'origin']),
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function show(string $slug, BandService $service, GenealogyService $genealogy)
    {
        $band = $service->getBySlug($slug);
        $graph = $genealogy->getBandGraph($band);

        return view('bands.show', [
            'band' => $band,
            'graph' => $graph,
            'related' => $service->getRelated($band),
        ]);
    }
}
