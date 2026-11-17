@extends('layouts.app')

@section('title', 'Associar categoria ao livro')

@section('content')
    <p>Livro: <strong>{{ $book->title }}</strong></p>
    <form method="POST" action="{{ route('books.categories.store', $book) }}" class="space-y-4">
        @csrf
        <div class="grid gap-1">
            <label for="category_id" class="font-medium">Categoria</label>
            <select id="category_id" name="category_id" required class="w-full rounded border border-slate-300 p-2">
                <option value="">Selecione uma categoria</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @if ($categories->isEmpty())
                <p>Nenhuma categoria cadastrada.</p>
            @endif
        </div>

        <button type="submit" @disabled($categories->isEmpty())
            class="rounded bg-blue-700 px-4 py-2 text-white disabled:opacity-50">Associar</button>
        <a href="{{ route('books.show', $book) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
