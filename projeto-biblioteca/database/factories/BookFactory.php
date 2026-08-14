<?php

namespace Database\Factories;

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
            'title' => fake()->randomElement([
                'Introdução à Programação Web',
                'Fundamentos de Banco de Dados',
                'Laravel na Prática',
                'Aplicações Web Modernas',
                'Desenvolvimento com PHP',
            ]),
            'isbn' => fake()->unique()->isbn13(),
            'published_year' => fake()->numberBetween(1990, 2026),
        ];
    }
}
