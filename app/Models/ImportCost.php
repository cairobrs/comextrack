<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportCost extends Model
{
    public const CATEGORIA_PADRAO = 'padrao';

    public const CATEGORIA_ADICIONAL = 'adicional';

    public const TIPO_ADICIONAL = 'adicional';

    public const TIPOS_PADRAO = [
        'frete_internacional',
        'marinha_mercante',
        'armazenagem_porto',
        'frete_rodoviario',
    ];

    protected $fillable = [
        'import_id',
        'categoria',
        'tipo_custo',
        'nome',
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

    public function isPadrao(): bool
    {
        return $this->categoria === self::CATEGORIA_PADRAO;
    }

    public function isAdicional(): bool
    {
        return $this->categoria === self::CATEGORIA_ADICIONAL;
    }

    public function getTipoCustoLabelAttribute(): string
    {
        if ($this->isAdicional()) {
            return $this->nome ?? 'Despesa adicional';
        }

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
