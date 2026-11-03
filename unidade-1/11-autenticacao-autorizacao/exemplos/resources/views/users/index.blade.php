@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-100">
                <tr>
                    <th scope="col" class="p-3">Nome</th>
                    <th scope="col" class="p-3">E-mail</th>
                    <th scope="col" class="p-3">Papel</th>
                    <th scope="col" class="p-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($users as $user)
                    <tr>
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">{{ $user->role }}</td>
                        <td class="space-x-3 p-3">
                            @can('view', $user)
                                <a href="{{ route('users.show', $user) }}" class="text-blue-700 underline">Detalhes</a>
                            @endcan
                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-700 underline">Editar</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-3">Nenhum usuário encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links('partials.pagination') }}
@endsection
