<?php

namespace App\Values;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

class SeoData implements Htmlable, Stringable
{
    public function __construct(
        public string $title,
        public string $description = '',
        public string $type = 'website',
        public ?string $image = null,
        public ?string $canonical = null,
        public ?string $schema = null,
    ) {}

    public function fullTitle(): string
    {
        $app = config('app.name', 'LISTA');

        return $this->title !== $app ? "{$this->title} — {$app}" : $app;
    }

    public function metaTags(): string
    {
        $tags = '<title>'.e($this->fullTitle()).'</title>'."\n";
        $tags .= '<meta name="description" content="'.e($this->description).'">'."\n";
        $tags .= '<meta property="og:title" content="'.e($this->fullTitle()).'">'."\n";
        $tags .= '<meta property="og:description" content="'.e($this->description).'">'."\n";
        $tags .= '<meta property="og:type" content="'.e($this->type).'">'."\n";

        if ($this->canonical) {
            $tags .= '<link rel="canonical" href="'.e($this->canonical).'">'."\n";
            $tags .= '<meta property="og:url" content="'.e($this->canonical).'">'."\n";
        }

        if ($this->image) {
            $tags .= '<meta property="og:image" content="'.e($this->image).'">'."\n";
        }

        if ($this->schema) {
            $tags .= '<script type="application/ld+json">'.$this->schema."</script>\n";
        }

        return $tags;
    }

    public function toHtml(): string
    {
        return $this->metaTags();
    }

    public function __toString(): string
    {
        return $this->metaTags();
    }
}
