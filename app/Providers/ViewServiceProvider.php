<?php

namespace App\Providers;

use App\View\Components\AdSlot;
use App\View\Components\GenealogyGraph;
use App\View\Components\MetaTags;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::component('ad-slot', AdSlot::class);
        Blade::component('meta-tags', MetaTags::class);
        Blade::component('genealogy-graph', GenealogyGraph::class);
    }
}
