<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookDetail;
use App\Models\Category;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categoryNames = [
            'Biografia',
            'Ciência',
            'Fantasia',
            'História',
            'Romance',
            'Tecnologia',
        ];

        $categories = [];

        // Cria as categorias ou reaproveita as que já existem.
        foreach ($categoryNames as $name) {
            $categories[] = Category::firstOrCreate(['name' => $name]);
        }

        $authors = Author::factory()->count(8)->create();

        foreach ($authors as $author) {
            $books = Book::factory()
                ->count(5)
                ->for($author, 'author')
                ->has(BookDetail::factory(), 'detail')
                ->create();

            foreach ($books as $book) {
                // Sorteia de 1 a 3 categorias, sem repetir no mesmo livro.
                $quantity = random_int(1, 3);
                shuffle($categories);
                $selectedCategories = array_slice($categories, 0, $quantity);

                foreach ($selectedCategories as $category) {
                    $book->categories()->attach($category->id);
                }
            }
        }
    }
}
