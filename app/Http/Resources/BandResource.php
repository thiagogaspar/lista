<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'formed_year' => $this->formed_year,
            'dissolved_year' => $this->dissolved_year,
            'origin' => $this->origin,
            'is_active' => $this->is_active,
            'label' => LabelResource::make($this->whenLoaded('label')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'artists_count' => $this->artists_count,
            'albums' => AlbumResource::collection($this->whenLoaded('albums')),
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
