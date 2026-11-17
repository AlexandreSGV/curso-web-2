<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nome', 'professor_id'])]
class Turma extends Model
{
    protected $table = 'turmas';

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(Aluno::class, 'matriculas', 'turma_id', 'aluno_id');
    }
}
