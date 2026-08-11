<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $import = $this->route('import');
        
        $rules = [
            'numero_processo' => 'required|string|max:255|unique:imports,numero_processo,' . $import->id,
            'client_id' => 'required|exists:clients,id',
            'responsavel_interno_id' => 'nullable|exists:users,id',
            'modal' => 'required|in:maritimo,aereo,rodoviario',
            'ncm_principal' => 'nullable|string|max:10',
            'descricao_mercadoria' => 'required|string|max:255',
            'pais_origem' => 'nullable|string|max:255',
            'porto_origem' => 'nullable|string|max:255',
            'porto_destino' => 'nullable|string|max:255',
            'valor_fatura' => 'nullable|numeric|min:0',
            'moeda' => 'nullable|string|max:10',
            'taxa_cambio' => 'nullable|numeric|min:0',
            'data_abertura' => 'required|date',
            'data_prevista_chegada' => 'nullable|date',
            'status_atual' => 'nullable|in:aberto,em_transito,em_desembaraco,concluido,cancelado',
            'observacoes' => 'nullable|string|max:65535',
        ];

        // Taxa de câmbio obrigatória quando moeda não for BRL
        if ($this->moeda && $this->moeda !== 'BRL') {
            $rules['taxa_cambio'] = 'required|numeric|min:0.0001';
        }

        return $rules;
    }

    // Define taxa de câmbio como 1.0 se moeda for BRL e não informada
    protected function prepareForValidation(): void
    {
        if ($this->moeda === 'BRL' && !$this->filled('taxa_cambio')) {
            $this->merge(['taxa_cambio' => 1.0]);
        }
    }
}


