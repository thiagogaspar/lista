<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Services\GenealogyService;

class ApiBandGraphController extends Controller
{
    public function __invoke(string $slug, GenealogyService $genealogy)
    {
        $band = Band::whereSlug($slug)->firstOrFail();

        return response()->json($genealogy->getBandGraph($band));
    }
}
