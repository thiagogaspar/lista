<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MetaTags extends Component
{
    public function __construct(
        public string $title,
        public string $description = '',
        public string $type = 'website',
        public ?string $image = null,
        public ?string $schema = null,
        public ?string $canonical = null,
    ) {}

    public function render()
    {
        return view('components.meta-tags');
    }

    public function fullTitle(): string
    {
        $app = config('app.name', 'LISTA');

        return $this->title !== $app ? "{$this->title} — {$app}" : $app;
    }
}
