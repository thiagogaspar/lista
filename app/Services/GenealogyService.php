<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;

class GenealogyService
{
    public function getBandGraph(Band $band): array
    {
        $nodes = [];
        $edges = [];
        $processedBands = [];
        $processedArtists = [];

        $this->addBandNode($band, $nodes, $processedBands);

        foreach ($band->artists as $artist) {
            $this->addArtistNode($artist, $nodes, $processedArtists);
            $edges[] = [
                'from' => "band_{$band->id}",
                'to' => "artist_{$artist->id}",
                'label' => $artist->pivot->role ?? 'member',
                'arrows' => 'to',
                'dashes' => true,
            ];

            foreach ($artist->bands as $otherBand) {
                if ($otherBand->id === $band->id) {
                    continue;
                }
                $this->addBandNode($otherBand, $nodes, $processedBands);
                $edges[] = [
                    'from' => "artist_{$artist->id}",
                    'to' => "band_{$otherBand->id}",
                    'label' => $otherBand->pivot->role ?? 'member',
                    'arrows' => 'to',
                    'dashes' => true,
                ];
            }
        }

        $relationships = BandRelationship::where('parent_band_id', $band->id)
            ->orWhere('child_band_id', $band->id)
            ->with(['parentBand', 'childBand'])
            ->get();

        foreach ($relationships as $rel) {
            $this->addBandNode($rel->parentBand, $nodes, $processedBands);
            $this->addBandNode($rel->childBand, $nodes, $processedBands);
            $edges[] = [
                'from' => "band_{$rel->parent_band_id}",
                'to' => "band_{$rel->child_band_id}",
                'label' => BandRelationship::types()[$rel->type] ?? $rel->type,
                'arrows' => 'to',
                'color' => ['color' => '#f59e0b'],
            ];
        }

        return ['nodes' => array_values($nodes), 'edges' => $edges];
    }

    public function getFullGraph(): array
    {
        $nodes = [];
        $edges = [];
        $processedBands = [];
        $processedArtists = [];

        $maxNodes = (int) config('lista.genealogy_max_nodes', 500);

        $bands = Band::with('genres')->withCount('artists')->limit($maxNodes)->get();
        foreach ($bands as $band) {
            $this->addBandNode($band, $nodes, $processedBands);
        }

        $bandIds = $bands->pluck('id');
        $maxMemberships = (int) config('lista.genealogy_max_memberships', 2500);
        $memberships = BandArtist::with(['band.genres', 'artist'])
            ->whereIn('band_id', $bandIds)
            ->limit($maxMemberships)
            ->get();
        foreach ($memberships as $m) {
            $this->addArtistNode($m->artist, $nodes, $processedArtists);
            $edges[] = [
                'from' => "band_{$m->band_id}",
                'to' => "artist_{$m->artist_id}",
                'label' => $m->role ?? 'member',
                'arrows' => 'to',
                'dashes' => true,
                'color' => ['color' => '#a8a29e'],
                'width' => 1.5,
            ];
        }

        $relationships = BandRelationship::with(['parentBand.genres', 'childBand.genres'])
            ->whereIn('parent_band_id', $bandIds)
            ->whereIn('child_band_id', $bandIds)
            ->limit($maxNodes * 2)
            ->get();
        foreach ($relationships as $rel) {
            $this->addBandNode($rel->parentBand, $nodes, $processedBands);
            $this->addBandNode($rel->childBand, $nodes, $processedBands);
            $edges[] = [
                'from' => "band_{$rel->parent_band_id}",
                'to' => "band_{$rel->child_band_id}",
                'label' => BandRelationship::types()[$rel->type] ?? $rel->type,
                'arrows' => 'to',
                'color' => ['color' => '#f59e0b'],
                'width' => 3,
            ];
        }

        return ['nodes' => array_values($nodes), 'edges' => $edges];
    }

    private function addBandNode(Band $band, array &$nodes, array &$processed): void
    {
        if (isset($processed[$band->id])) {
            return;
        }
        $processed[$band->id] = true;
        $genres = $band->genres->pluck('name')->implode(', ');
        $years = $band->formed_year ? $band->formed_year.($band->dissolved_year ? "–{$band->dissolved_year}" : '–present') : '';
        $origin = $band->origin ?? '';
        $title = '<strong>'.e($band->name).'</strong>';
        if ($years) {
            $title .= '<br>'.e($years);
        }
        if ($genres) {
            $title .= '<br>'.e($genres);
        }
        if ($origin) {
            $title .= '<br>'.e($origin);
        }

        $nodes["band_{$band->id}"] = [
            'id' => "band_{$band->id}",
            'label' => $band->name,
            'group' => 'band',
            'shape' => 'box',
            'url' => route('bands.show', $band),
            'title' => $title,
            'genre' => $band->genres->first()?->slug ?? 'unknown',
            'genreName' => $band->genres->first()?->name ?? 'Unknown',
            'formed_year' => $band->formed_year,
            'dissolved_year' => $band->dissolved_year,
            'origin' => $band->origin,
            'color' => ['background' => '#22c55e', 'border' => '#16a34a'],
            'font' => ['color' => '#ffffff', 'size' => 14],
            'borderWidth' => 2,
            'mass' => 2,
            'artists_count' => $band->artists_count ?? 0,
        ];
    }

    private function addArtistNode(Artist $artist, array &$nodes, array &$processed): void
    {
        if (isset($processed[$artist->id])) {
            return;
        }
        $processed[$artist->id] = true;
        $years = $artist->birth_date?->format('Y').($artist->death_date?->format('Y') ? '–'.$artist->death_date->format('Y') : '');
        $title = '<strong>'.e($artist->name).'</strong>';
        if ($years) {
            $title .= '<br>Life: '.e($years);
        }
        if ($artist->origin) {
            $title .= '<br>'.e($artist->origin);
        }

        $nodes["artist_{$artist->id}"] = [
            'id' => "artist_{$artist->id}",
            'label' => $artist->name,
            'group' => 'artist',
            'shape' => 'dot',
            'size' => 12,
            'url' => route('artists.show', $artist),
            'title' => $title,
            'color' => ['background' => '#a855f7', 'border' => '#9333ea'],
            'font' => ['color' => '#6b21a8', 'size' => 12],
            'mass' => 1,
        ];
    }
}
