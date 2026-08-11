<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'nome_cliente' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:65535',
        ];
    }
}

