<?php

namespace App\Http\Controllers;

use App\Services\GenealogyService;

class ApiGenealogyController extends Controller
{
    public function __invoke(GenealogyService $genealogy)
    {
        return response()->json($genealogy->getFullGraph());
    }
}
