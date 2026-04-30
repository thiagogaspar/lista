<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GenealogyGraph extends Component
{
    public function __construct(
        public array $graph,
        public string $containerId = 'genealogy-graph',
    ) {}

    public function render()
    {
        return view('components.genealogy-graph');
    }
}
