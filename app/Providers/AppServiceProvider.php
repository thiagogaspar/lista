<?php

namespace App\Providers;

use App\Models\Artist;
use App\Models\Band;
use App\Observers\ArtistObserver;
use App\Observers\BandObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Band::observe(BandObserver::class);
        Artist::observe(ArtistObserver::class);
    }
}
