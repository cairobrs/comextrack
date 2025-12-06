<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportStep extends Model
{
    protected $fillable = [
        'import_id',
        'nome_etapa',
        'data_prevista',
        'data_realizada',
        'responsavel',
        'observacoes',
    ];

    protected $casts = [
        'data_prevista' => 'date',
        'data_realizada' => 'date',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->data_realizada) {
            return 'concluida';
        }

        if ($this->data_prevista && $this->data_prevista->isPast()) {
            return 'atrasada';
        }

        return 'pendente';
    }
}
