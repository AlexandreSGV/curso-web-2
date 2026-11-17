<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome'])]
class Professor extends Model
{
    protected $table = 'professores';

    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class, 'professor_id');
    }
}
