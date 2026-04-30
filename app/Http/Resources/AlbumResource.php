<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'release_year' => $this->release_year,
            'cover_art' => $this->cover_art ? url('storage/'.$this->cover_art) : null,
        ];
    }
}
