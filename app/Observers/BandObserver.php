<?php

namespace App\Observers;

use App\Models\Band;
use App\Services\BandService;

class BandObserver
{
    public function saved(Band $band): void
    {
        app(BandService::class)->flushCache($band);
    }

    public function deleted(Band $band): void
    {
        app(BandService::class)->flushCache($band);
    }
}
