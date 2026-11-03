<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['title', 'isbn', 'published_year', 'author_id'])]
class Book extends Model
{
    use HasFactory;

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(BookDetail::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot('featured', 'position')
            ->withTimestamps();
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => trim($value),
        );
    }

    protected function isbn(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => str_replace(['-', ' '], '', $value),
        );
    }

    public function publicationAge(int $referenceYear): ?int
    {
        if ($this->published_year === null) {
            return null;
        }

        return $referenceYear - $this->published_year;
    }

    public function isClassic(int $referenceYear, int $minimumAge = 50): bool
    {
        $age = $this->publicationAge($referenceYear);

        return $age !== null && $age >= $minimumAge;
    }
}
