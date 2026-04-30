<?php

namespace App\Http\Controllers;

use App\Actions\GenerateSitemapAction;

class SitemapController extends Controller
{
    public function __invoke(GenerateSitemapAction $action)
    {
        return response($action->handle(), 200)
            ->header('Content-Type', 'application/xml');
    }
}
