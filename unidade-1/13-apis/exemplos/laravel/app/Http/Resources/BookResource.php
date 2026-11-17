<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'published_year' => $this->published_year,
            'author_id' => $this->author_id,
            'author_name' => $this->author->name,
            'cover_url' => $this->cover === null
                ? asset('images/cover-default.svg')
                : asset('storage/' . $this->cover),
        ];
    }
}
