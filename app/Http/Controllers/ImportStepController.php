<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportStep;
use Illuminate\Http\Request;

class ImportStepController extends Controller
{
    public function create(Import $import)
    {
        return view('imports.steps.create', compact('import'));
    }

    public function store(Request $request, Import $import)
    {
        $validated = $request->validate([
            'nome_etapa' => 'required|string|max:255',
            'data_prevista' => 'nullable|date',
            'data_realizada' => 'nullable|date',
            'responsavel' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:65535',
        ]);

        $validated['import_id'] = $import->id;
        ImportStep::create($validated);

        return redirect()->route('imports.show', $import)
            ->with('status', 'Etapa criada com sucesso!');
    }

    public function edit(ImportStep $step)
    {
        return view('imports.steps.edit', compact('step'));
    }

    public function update(Request $request, ImportStep $step)
    {
        $validated = $request->validate([
            'nome_etapa' => 'required|string|max:255',
            'data_prevista' => 'nullable|date',
            'data_realizada' => 'nullable|date',
            'responsavel' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:65535',
        ]);

        unset($validated['import_id']);
        $step->update($validated);

        return redirect()->route('imports.show', $step->import)
            ->with('status', 'Etapa atualizada com sucesso!');
    }

    public function destroy(ImportStep $step)
    {
        $import = $step->import;
        $step->delete();

        return redirect()->route('imports.show', $import)
            ->with('status', 'Etapa excluída com sucesso!');
    }
}
