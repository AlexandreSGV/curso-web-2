@extends('layouts.app')

@section('title', 'Livros')

@section('content')
    <a href="{{ route('books.create') }}" class="inline-block rounded bg-blue-700 px-4 py-2 text-white">Cadastrar livro</a>

    <form method="GET" action="{{ route('books.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="grid gap-1">
            <label for="title">Buscar pelo título</label>
            <input id="title" name="title" value="{{ request('title') }}" maxlength="255"
                class="rounded border border-slate-300 p-2">
        </div>
        <button type="submit" class="rounded bg-slate-800 px-4 py-2 text-white">Buscar</button>
        <a href="{{ route('books.index') }}" class="py-2 text-blue-700 underline">Limpar</a>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-100">
                <tr>
                    <th scope="col" class="p-3">Título</th>
                    <th scope="col" class="p-3">Autor</th>
                    <th scope="col" class="p-3">Categorias</th>
                    <th scope="col" class="p-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($books as $book)
                    <tr>
                        <td class="p-3">{{ $book->title }}</td>
                        <td class="p-3">{{ $book->author?->name ?? 'Sem autor' }}</td>
                        <td class="p-3">
                            @forelse ($book->categories as $category)
                                <span class="mb-1 inline-block rounded bg-slate-100 px-2 py-1 text-sm">{{ $category->name }}</span>
                            @empty
                                Sem categorias
                            @endforelse
                        </td>
                        <td class="p-3">
                            <a href="{{ route('books.show', $book) }}" class="text-blue-700 underline">Ver livro</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-3">Nenhum livro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $books->links('partials.pagination') }}
@endsection
