<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->words(
                fake()->numberBetween(2, 5),
                true
            )),
            'isbn' => fake()->unique()->numerify('#############'),
            'published_year' => fake()
                ->optional(0.85)
                ->numberBetween(1850, now()->year),
            'author_id' => Author::factory(),
        ];
    }
}

