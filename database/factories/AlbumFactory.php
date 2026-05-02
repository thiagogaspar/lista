<?php

namespace Database\Factories;

use App\Models\Band;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AlbumFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'band_id' => Band::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'release_year' => fake()->year(),
            'description' => fake()->text(100),
            'tracklist' => fake()->randomElements(['Intro', 'First Track', 'Middle Song', 'Ballad', 'Finale'], 5),
        ];
    }
}
