<?php

namespace App\Http\Controllers;

use App\Models\ImportCost;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImportCostController extends Controller
{
    public function edit(ImportCost $cost)
    {
        return view('imports.costs.edit', compact('cost'));
    }

    public function update(Request $request, ImportCost $cost)
    {
        $validated = $request->validate([
            'status_pagamento' => ['required', Rule::in(array_keys(ImportCost::statusPagamentoOptions()))],
            'observacoes' => ['nullable', 'string', 'max:65535'],
        ]);

        unset($validated['import_id']);
        $cost->update($validated);

        return redirect()->route('imports.show', $cost->import)
            ->with('status', 'Custo atualizado com sucesso!');
    }
}
