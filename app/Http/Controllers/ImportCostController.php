<?php

namespace App\Http\Controllers;

use App\Models\ImportCost;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImportCostController extends Controller
{
    public function edit(ImportCost $cost)
    {
        $this->authorize('update', $cost);

        return view('imports.costs.edit', compact('cost'));
    }

    public function update(Request $request, ImportCost $cost)
    {
        $this->authorize('update', $cost);

        $validated = $request->validate([
            'valor' => ['nullable', 'numeric', 'min:0'],
            'moeda' => ['nullable', 'string', 'max:3'],
            'data_vencimento' => ['nullable', 'date'],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'status_pagamento' => ['required', Rule::in(array_keys(ImportCost::statusPagamentoOptions()))],
        ]);

        foreach (['data_vencimento', 'data_pagamento'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] === '') {
                $validated[$key] = null;
            }
        }

        if (array_key_exists('valor', $validated) && $validated['valor'] === '') {
            $validated['valor'] = null;
        }

        if (array_key_exists('moeda', $validated) && $validated['moeda'] === '') {
            $validated['moeda'] = null;
        }

        unset($validated['import_id']);
        $cost->update($validated);

        return redirect()->route('imports.show', $cost->import)
            ->with('status', 'Custo atualizado com sucesso!');
    }
}
