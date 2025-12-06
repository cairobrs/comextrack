<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = [
        'import_id',
        'tipo_evento',
        'descricao',
        'user_id',
        'entidade_tipo',
        'entidade_id',
        'dados_anteriores',
        'dados_novos',
        'automatico',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
        'automatico' => 'boolean',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function tiposEvento(): array
    {
        return [
            'status_processo_alterado' => 'Status do processo alterado',
            'status_documento_alterado' => 'Status de documento alterado',
            'status_custo_alterado' => 'Status de custo alterado',
        ];
    }
}
