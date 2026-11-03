@extends('layouts.app')

@section('title', 'Autores')

@section('content')
    <a href="{{ route('authors.create') }}" class="inline-block rounded bg-blue-700 px-4 py-2 text-white">Cadastrar autor</a>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-100">
                <tr>
                    <th scope="col" class="p-3">Nome</th>
                    <th scope="col" class="p-3">Livros</th>
                    <th scope="col" class="p-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($authors as $author)
                    <tr>
                        <td class="p-3">{{ $author->name }}</td>
                        <td class="p-3">{{ $author->books_count }}</td>
                        <td class="p-3"><a href="{{ route('authors.show', $author) }}" class="text-blue-700 underline">Ver dados</a></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-3">Nenhum registro cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $authors->links('partials.pagination') }}
@endsection
