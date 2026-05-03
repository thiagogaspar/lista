<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Values\SeoData;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LabelController extends Controller
{
    public function index(): View
    {
        $labels = Label::withCount('bands')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($l) => strtoupper(mb_substr($l->name, 0, 1)));

        $alphabet = range('A', 'Z');

        return view('labels.index', compact('labels', 'alphabet'));
    }

    public function show(string $slug): View
    {
        $label = Label::whereSlug($slug)
            ->with(['bands' => fn ($q) => $q->with('genres')->withCount('artists')])
            ->firstOrFail();

        $seo = new SeoData(
            title: $label->name,
            description: Str::limit(strip_tags($label->description ?? $label->name.' — record label.'), 160),
            type: 'organization',
            image: $label->logo ? img_url($label->logo) : null,
            canonical: route('labels.show', $label),
        );

        return view('labels.show', compact('label', 'seo'));
    }
}
