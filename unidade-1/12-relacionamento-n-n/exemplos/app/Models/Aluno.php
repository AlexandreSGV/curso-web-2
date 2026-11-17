<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nome'])]
class Aluno extends Model
{
    protected $table = 'alunos';

    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'matriculas', 'aluno_id', 'turma_id');
    }
}
