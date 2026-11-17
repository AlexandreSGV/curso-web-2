@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
    <a href="{{ route('categories.create') }}" class="inline-block rounded bg-blue-700 px-4 py-2 text-white">Cadastrar categoria</a>

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
                @forelse ($categories as $category)
                    <tr>
                        <td class="p-3">{{ $category->name }}</td>
                        <td class="p-3">{{ $category->books_count }}</td>
                        <td class="p-3"><a href="{{ route('categories.show', $category) }}" class="text-blue-700 underline">Ver dados</a></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-3">Nenhum registro cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $categories->links('partials.pagination') }}
@endsection
