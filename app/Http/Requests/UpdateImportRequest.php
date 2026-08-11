<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $import = $this->route('import');

        return [
            'numero_processo' => 'required|string|max:255|unique:imports,numero_processo,'.$import->id,
            'client_id' => 'required|exists:clients,id',
            'modal' => 'required|in:maritimo,aereo,rodoviario',
            'ncm_principal' => 'nullable|string|max:10',
            'descricao_mercadoria' => 'required|string|max:255',
            'pais_origem' => 'nullable|string|max:255',
            'valor_fatura' => 'nullable|numeric|min:0',
            'moeda' => 'nullable|string|max:10',
            'data_abertura' => 'required|date',
            'data_prevista_chegada' => 'nullable|date',
            'status_atual' => 'nullable|in:aberto,em_transito,em_desembaraco,concluido,cancelado',
            'observacoes' => 'nullable|string|max:65535',
        ];
    }
}
