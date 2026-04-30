<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'death_date' => $this->death_date?->format('Y-m-d'),
            'origin' => $this->origin,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'bands' => $this->whenLoaded('bands'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
