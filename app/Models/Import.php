<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Import - Representa um processo de importação.
 * 
 * Ao criar um processo, são criados automaticamente 4 documentos padrão e 4 custos padrão.
 * O status é atualizado automaticamente por observers baseado em documentos e custos.
 */
class Import extends Model
{
    /** @use HasFactory<\Database\Factories\ImportFactory> */
    use HasFactory;

    protected $fillable = [
        'numero_processo',
        'client_id',
        'responsavel_interno_id',
        'modal',
        'ncm_principal',
        'descricao_mercadoria',
        'pais_origem',
        'porto_origem',
        'porto_destino',
        'valor_fatura',
        'moeda',
        'taxa_cambio',
        'data_abertura',
        'data_prevista_chegada',
        'status_atual',
        'observacoes',
    ];

    protected $casts = [
        'data_abertura' => 'date',
        'data_prevista_chegada' => 'date',
        'valor_fatura' => 'float',
        'taxa_cambio' => 'float',
    ];

    protected static function booted()
    {
        static::created(function (Import $import) {
            $documentTypes = ['BL', 'Mercante', 'Invoice', 'Packing List'];
            foreach ($documentTypes as $tipo) {
                $import->documents()->create([
                    'tipo_documento' => $tipo,
                    'status' => 'aguardando_recebimento',
                    'observacoes' => null,
                    'arquivo' => null,
                ]);
            }

            $costTypes = [
                'frete_internacional',
                'marinha_mercante',
                'armazenagem_porto',
                'frete_rodoviario',
            ];
            foreach ($costTypes as $tipo) {
                $import->costs()->create([
                    'tipo_custo' => $tipo,
                    'valor' => null,
                    'moeda' => 'USD',
                    'status_pagamento' => 'pendente',
                    'data_vencimento' => null,
                    'data_pagamento' => null,
                    'observacoes' => null,
                ]);
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function responsavelInterno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_interno_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ImportLog::class)->orderBy('created_at', 'desc');
    }

    public function importSteps(): HasMany
    {
        return $this->hasMany(ImportStep::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ImportStep::class)->orderBy('data_prevista')->orderBy('id');
    }

    public function importDocuments(): HasMany
    {
        return $this->hasMany(ImportDocument::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ImportDocument::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(ImportCost::class);
    }

    // Retorna o valor da fatura convertido para reais usando taxa de câmbio informada
    public function getValorFaturaEmReaisAttribute(): ?float
    {
        if ($this->valor_fatura === null) {
            return null;
        }

        if ($this->moeda === 'BRL') {
            return $this->valor_fatura;
        }

        $taxa = $this->taxa_cambio ?? 1;
        return $this->valor_fatura * $taxa;
    }

    // Indica se é processo de alto valor (acima de R$ 500.000)
    public function getIsHighValueAttribute(): bool
    {
        $valorEmReais = $this->valor_fatura_em_reais;

        if ($valorEmReais === null) {
            return false;
        }

        return $valorEmReais > 500000;
    }

    public function scopeAbertas($query)
    {
        return $query->whereNotIn('status_atual', ['concluido', 'cancelado']);
    }

    public function scopeComDocumentosEssenciaisPendentes($query)
    {
        return $query->whereHas('documents', function($q) {
            $q->whereIn('tipo_documento', ['Invoice', 'Packing List', 'BL', 'Mercante'])
              ->where('status', '!=', 'recebido_ok');
        });
    }

    public function scopeComPagamentosObrigatoriosPendentes($query)
    {
        return $query->whereHas('costs', function($q) {
            $q->whereIn('tipo_custo', ['frete_internacional', 'marinha_mercante', 'armazenagem_porto'])
              ->where('status_pagamento', 'pendente');
        });
    }

    public function scopeComFreteRodoviarioPendente($query)
    {
        return $query->whereHas('costs', function($q) {
            $q->where('tipo_custo', 'frete_rodoviario')
              ->where('status_pagamento', 'pendente');
        });
    }

    public function scopeAltoValor($query)
    {
        return $query->whereNotNull('valor_fatura')
                    ->whereNotNull('moeda');
    }

    public function temDocumentosEssenciaisPendentes(): bool
    {
        return $this->documents()
            ->whereIn('tipo_documento', ['Invoice', 'Packing List', 'BL', 'Mercante'])
            ->where('status', '!=', 'recebido_ok')
            ->exists();
    }

    public function documentosEssenciaisPendentes()
    {
        return $this->documents()
            ->whereIn('tipo_documento', ['Invoice', 'Packing List', 'BL', 'Mercante'])
            ->where('status', '!=', 'recebido_ok')
            ->get();
    }

    public function temPagamentosObrigatoriosPendentes(): bool
    {
        return $this->costs()
            ->whereIn('tipo_custo', ['frete_internacional', 'marinha_mercante', 'armazenagem_porto'])
            ->where('status_pagamento', 'pendente')
            ->exists();
    }

    public function pagamentosObrigatoriosPendentes()
    {
        return $this->costs()
            ->whereIn('tipo_custo', ['frete_internacional', 'marinha_mercante', 'armazenagem_porto'])
            ->where('status_pagamento', 'pendente')
            ->get();
    }

    public function temPendencias(): bool
    {
        return $this->temDocumentosEssenciaisPendentes() || 
               $this->temPagamentosObrigatoriosPendentes() ||
               $this->costs()->where('tipo_custo', 'frete_rodoviario')->where('status_pagamento', 'pendente')->exists();
    }
}
