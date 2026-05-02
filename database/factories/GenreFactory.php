<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GenreFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Rock', 'Metal', 'Punk', 'Indie', 'Electronic', 'Hip Hop', 'Jazz', 'Blues', 'Folk', 'Pop']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }
}
