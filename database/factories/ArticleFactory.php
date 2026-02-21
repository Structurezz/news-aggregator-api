<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'url' => $this->faker->unique()->url,
            'source_name' => $this->faker->company,
            'published_at' => now(),
            'description' => $this->faker->paragraph,
        ];
    }
}
