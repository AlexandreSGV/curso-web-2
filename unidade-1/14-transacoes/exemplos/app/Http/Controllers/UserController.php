<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'bibliotecario', 'cliente'])],
        ]);

        // Atribuição explícita nesta action administrativa: role continua fora de Fillable.
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Se alterar o próprio papel, o usuário passa a seguir as novas permissões.
        if ($user->is($request->user()) && $user->role !== 'admin') {
            return redirect()->route('books.index')->with('success', 'Usuário atualizado.');
        }

        return redirect()->route('users.show', $user)->with('success', 'Usuário atualizado.');
    }
}
