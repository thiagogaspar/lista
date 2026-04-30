<?php

namespace App\Observers;

use App\Models\Artist;
use App\Services\ArtistService;

class ArtistObserver
{
    public function saved(Artist $artist): void
    {
        app(ArtistService::class)->flushCache($artist);
    }

    public function deleted(Artist $artist): void
    {
        app(ArtistService::class)->flushCache($artist);
    }
}
