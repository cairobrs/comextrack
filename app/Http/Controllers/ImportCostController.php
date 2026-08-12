<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportCost;
use App\Services\ImportLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImportCostController extends Controller
{
    public function create(Import $import)
    {
        $this->authorize('create', ImportCost::class);

        return view('imports.costs.create', compact('import'));
    }

    public function store(Request $request, Import $import)
    {
        $this->authorize('create', ImportCost::class);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'valor' => ['nullable', 'numeric', 'min:0'],
            'moeda' => ['nullable', 'string', 'max:3'],
            'data_vencimento' => ['nullable', 'date'],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ]);

        $import->costs()->create([
            'categoria' => ImportCost::CATEGORIA_ADICIONAL,
            'tipo_custo' => ImportCost::TIPO_ADICIONAL,
            'nome' => $validated['nome'],
            'valor' => $validated['valor'] ?? null,
            'moeda' => $validated['moeda'] ?? 'USD',
            'status_pagamento' => 'pendente',
            'data_vencimento' => $validated['data_vencimento'] ?? null,
            'data_pagamento' => $validated['data_pagamento'] ?? null,
            'observacoes' => $validated['observacoes'] ?? null,
        ]);

        return redirect()->route('imports.show', $import)
            ->with('status', 'Despesa adicional criada com sucesso!');
    }

    public function edit(ImportCost $cost)
    {
        $this->authorize('update', $cost);

        return view('imports.costs.edit', compact('cost'));
    }

    public function update(Request $request, ImportCost $cost)
    {
        $this->authorize('update', $cost);

        $rules = [
            'valor' => ['nullable', 'numeric', 'min:0'],
            'moeda' => ['nullable', 'string', 'max:3'],
            'data_vencimento' => ['nullable', 'date'],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'status_pagamento' => ['required', Rule::in(array_keys(ImportCost::statusPagamentoOptions()))],
        ];

        if ($cost->isAdicional()) {
            $rules['nome'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

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

        if ($cost->isPadrao()) {
            unset($validated['nome']);
        }

        unset($validated['import_id'], $validated['categoria'], $validated['tipo_custo']);

        $dadosAnteriores = ImportLogService::costSnapshot($cost);
        $cost->update($validated);
        $cost->refresh();
        $dadosNovos = ImportLogService::costSnapshot($cost);

        if ($cost->isAdicional() && $dadosAnteriores !== $dadosNovos) {
            $camposAlterados = [];
            foreach ($dadosNovos as $campo => $valor) {
                if (($dadosAnteriores[$campo] ?? null) !== $valor) {
                    $camposAlterados[] = $campo;
                }
            }

            if (array_diff($camposAlterados, ['status_pagamento']) !== []) {
                ImportLogService::logCustoAlterado($cost, $dadosAnteriores, $dadosNovos);
            }
        }

        return redirect()->route('imports.show', $cost->import)
            ->with('status', 'Custo atualizado com sucesso!');
    }

    public function destroy(ImportCost $cost)
    {
        $this->authorize('delete', $cost);

        $import = $cost->import;
        $cost->delete();

        return redirect()->route('imports.show', $import)
            ->with('status', 'Despesa adicional excluída com sucesso!');
    }
}
