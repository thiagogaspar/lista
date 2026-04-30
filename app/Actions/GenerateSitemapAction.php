<?php

namespace App\Actions;

use App\Models\Artist;
use App\Models\Band;

class GenerateSitemapAction
{
    public function handle(): string
    {
        $urls = [];

        foreach (Band::where('is_active', true)->get(['slug', 'updated_at']) as $band) {
            $urls[] = $this->entry(route('bands.show', $band), $band->updated_at, '0.9', 'daily');
        }
        foreach (Artist::where('is_active', true)->get(['slug', 'updated_at']) as $artist) {
            $urls[] = $this->entry(route('artists.show', $artist), $artist->updated_at, '0.7', 'weekly');
        }

        foreach ([
            ['route' => 'home', 'priority' => '0.8', 'freq' => 'daily'],
            ['route' => 'bands.index', 'priority' => '0.6', 'freq' => 'daily'],
            ['route' => 'artists.index', 'priority' => '0.5', 'freq' => 'weekly'],
            ['route' => 'genealogy', 'priority' => '0.4', 'freq' => 'weekly'],
        ] as $s) {
            $urls[] = $this->entry(route($s['route']), now(), $s['priority'], $s['freq']);
        }

        return view('sitemap', ['urls' => $urls])->render();
    }

    private function entry(string $loc, $lastmod, string $priority, string $changefreq): array
    {
        return ['loc' => $loc, 'lastmod' => $lastmod->toDateString(), 'priority' => $priority, 'changefreq' => $changefreq];
    }
}
