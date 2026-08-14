<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Book::factory()
            ->count(6)
            ->sequence(
                [
                    'title' => 'Fundamentos da Web',
                    'isbn' => '9790000000001',
                    'published_year' => 2018,
                ],
                [
                    'title' => 'Laravel Passo a Passo',
                    'isbn' => '9790000000018',
                    'published_year' => 2021,
                ],
                [
                    'title' => 'Banco de Dados Essencial',
                    'isbn' => '9790000000025',
                    'published_year' => 2019,
                ],
                [
                    'title' => 'APIs para Iniciantes',
                    'isbn' => '9790000000032',
                    'published_year' => 2024,
                ],
                [
                    'title' => 'Código Limpo na Prática',
                    'isbn' => '9790000000049',
                    'published_year' => 2017,
                ],
                [
                    'title' => 'Desenvolvimento Web Moderno',
                    'isbn' => '9790000000056',
                    'published_year' => 2026,
                ],
            )
            ->create();
    }
}
