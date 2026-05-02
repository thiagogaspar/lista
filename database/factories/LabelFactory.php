<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LabelFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company().' Records';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'country' => fake()->country(),
            'founded_year' => fake()->year(),
            'website' => fake()->url(),
            'description' => fake()->text(150),
        ];
    }
}
