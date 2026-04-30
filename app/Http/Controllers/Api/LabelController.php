<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LabelResource;
use App\Models\Label;

class LabelController extends Controller
{
    public function index()
    {
        return LabelResource::collection(Label::withCount('bands')->get());
    }
}
