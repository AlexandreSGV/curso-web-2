<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'professor@example.com'],
            ['name' => 'Conta de aula', 'password' => Hash::make('senha-aula-123')],
        );

        $professor = Professor::firstOrCreate(['nome' => 'Marina Costa']);
        $web1 = Turma::firstOrCreate(['nome' => 'Web 1', 'professor_id' => $professor->id]);
        $web2 = Turma::firstOrCreate(['nome' => 'Web 2', 'professor_id' => $professor->id]);

        $ana = Aluno::firstOrCreate(['nome' => 'Ana Lima']);
        $bruno = Aluno::firstOrCreate(['nome' => 'Bruno Silva']);
        Aluno::firstOrCreate(['nome' => 'Carla Souza']);

        $web1->alunos()->syncWithoutDetaching([$ana->id, $bruno->id]);
        $web2->alunos()->syncWithoutDetaching([$ana->id]);
    }
}
