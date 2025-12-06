<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'email',
        'telefone',
        'nome_responsavel',
        'telefone_responsavel',
        'observacoes',
    ];
    public function imports(): HasMany
    {
        return $this->hasMany(Import::class);
    }
}
