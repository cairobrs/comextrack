<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportDocument extends Model
{
    protected $fillable = [
        'import_id',
        'tipo_documento',
        'arquivo',
        'status',
        'observacoes',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aguardando_recebimento' => 'Aguardando recebimento',
            'aguardando_correcoes' => 'Aguardando correções',
            'recebido_ok' => 'Recebido OK',
            'nao_aplicavel' => 'Não aplicável',
            default => 'Não informado',
        };
    }

    public static function statusOptions(): array
    {
        return [
            'aguardando_recebimento' => 'Aguardando recebimento',
            'aguardando_correcoes' => 'Aguardando correções',
            'recebido_ok' => 'Recebido OK',
            'nao_aplicavel' => 'Não aplicável',
        ];
    }
}
