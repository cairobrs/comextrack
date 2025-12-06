<?php

namespace App\Http\Controllers;

use App\Models\ImportDocument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImportDocumentController extends Controller
{
    public function edit(ImportDocument $document)
    {
        return view('imports.documents.edit', compact('document'));
    }

    public function update(Request $request, ImportDocument $document)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(ImportDocument::statusOptions()))],
            'observacoes' => ['nullable', 'string', 'max:65535'],
        ]);

        if (isset($validated['status']) && $validated['status'] === 'recebido_ok') {
            $validated['observacoes'] = 'Documento de acordo, sem pendências.';
        }

        unset($validated['import_id']);
        $document->update($validated);

        return redirect()->route('imports.show', $document->import)
            ->with('status', 'Documento atualizado com sucesso!');
    }
}
