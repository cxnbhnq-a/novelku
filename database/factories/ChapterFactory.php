<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    public function definition(): array
    {
        // Bikin 5-10 paragraf acak yang dibungkus tag <p>
        $paragraphs = fake()->paragraphs(rand(5, 10));
        $htmlContent = '<p>' . implode('</p><p>', $paragraphs) . '</p>';

        return [
            'title' => ucwords(fake()->words(rand(2, 4), true)),
            'content' => $htmlContent,
        ];
    }
}