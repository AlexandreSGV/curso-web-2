<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccessSeeder extends Seeder
{
    public function run(): void
    {
        // Contas locais para comparar os três papéis durante a aula.
        $accounts = [
            ['name' => 'Administrador', 'email' => 'admin@example.com', 'role' => 'admin'],
            ['name' => 'Bibliotecário', 'email' => 'bibliotecario@example.com', 'role' => 'bibliotecario'],
            ['name' => 'Cliente', 'email' => 'cliente@example.com', 'role' => 'cliente'],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrNew(['email' => $account['email']]);
            $user->name = $account['name'];
            $user->password = Hash::make('senha-aula-123');
            $user->role = $account['role'];
            $user->save();
        }
    }
}
