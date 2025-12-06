<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportCost extends Model
{
    protected $fillable = [
        'import_id',
        'tipo_custo',
        'valor',
        'moeda',
        'status_pagamento',
        'data_vencimento',
        'data_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'float',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function getTipoCustoLabelAttribute(): string
    {
        return match ($this->tipo_custo) {
            'frete_internacional' => 'Frete Internacional',
            'marinha_mercante' => 'Marinha Mercante',
            'armazenagem_porto' => 'Armazenagem do Porto',
            'frete_rodoviario' => 'Frete Rodoviário',
            default => $this->tipo_custo,
        };
    }

    public function getStatusPagamentoLabelAttribute(): string
    {
        return match ($this->status_pagamento) {
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            default => $this->status_pagamento,
        };
    }

    // Retorna o valor convertido para reais usando a taxa de câmbio do processo
    public function getValorEmReaisAttribute(): ?float
    {
        if ($this->valor === null) {
            return null;
        }

        if ($this->moeda === 'BRL') {
            return $this->valor;
        }

        $import = $this->import;
        $taxa = $import?->taxa_cambio ?? 1;
        return $this->valor * $taxa;
    }

    public static function statusPagamentoOptions(): array
    {
        return [
            'pendente' => 'Pendente',
            'pago' => 'Pago',
        ];
    }

    public static function tiposCustoOptions(): array
    {
        return [
            'frete_internacional' => 'Frete Internacional',
            'marinha_mercante' => 'Marinha Mercante',
            'armazenagem_porto' => 'Armazenagem do Porto',
            'frete_rodoviario' => 'Frete Rodoviário',
        ];
    }
}
