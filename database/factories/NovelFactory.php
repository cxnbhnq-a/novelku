<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NovelFactory extends Factory
{
    public function definition(): array
{
    $title = ucwords(fake()->words(rand(3, 5), true));
    return [
        'title' => $title,
        'slug' => str($title)->slug(), // Tambahin slug otomatis dari judul
        'synopsis' => fake()->paragraphs(3, true), // Ganti dari description ke synopsis
        'status' => fake()->randomElement(['draft', 'ongoing', 'completed']), // Sesuaikan pilihan enum lu
    ];
    }
}