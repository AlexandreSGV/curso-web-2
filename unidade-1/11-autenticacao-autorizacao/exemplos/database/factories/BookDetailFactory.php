<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookDetail>
 */
class BookDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'pages' => fake()->numberBetween(80, 900),
            'summary' => fake()->paragraph(3),
        ];
    }
}

