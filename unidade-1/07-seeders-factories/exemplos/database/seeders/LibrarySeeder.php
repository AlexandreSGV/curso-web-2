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
        $categories = collect([
            'Biografia',
            'Ciência',
            'Fantasia',
            'História',
            'Romance',
            'Tecnologia',
        ])->map(
            fn (string $name): Category =>
                Category::firstOrCreate(['name' => $name])
        );

        Author::factory()
            ->count(8)
            ->create()
            ->each(function (Author $author) use ($categories): void {
                Book::factory()
                    ->count(5)
                    ->for($author, 'author')
                    ->has(BookDetail::factory(), 'detail')
                    ->create()
                    ->each(function (Book $book) use ($categories): void {
                        $selectedCategories = $categories
                            ->random(random_int(1, 3))
                            ->values();

                        $pivotAttributes = $selectedCategories
                            ->mapWithKeys(
                                fn (Category $category, int $index): array => [
                                    $category->id => [
                                        'featured' => $index === 0,
                                        'position' => $index + 1,
                                    ],
                                ]
                            )
                            ->all();

                        $book->categories()->attach($pivotAttributes);
                    });
            });
    }
}

