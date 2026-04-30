<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdSlot extends Component
{
    public function __construct(
        public string $position = 'sidebar',
        public string $format = 'auto',
    ) {}

    public function render()
    {
        if (! config('lista.ads.enabled', false)) {
            return '<!-- ads disabled -->';
        }

        return view('components.ad-slot', [
            'client' => config('lista.ads.client'),
            'slotId' => config("lista.ads.slots.{$this->position}"),
        ]);
    }
}
