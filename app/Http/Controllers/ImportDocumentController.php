<?php

namespace App\Http\Controllers;

use App\Models\ImportDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ImportDocumentController extends Controller
{
    public function edit(ImportDocument $document)
    {
        $this->authorize('update', $document);

        return view('imports.documents.edit', compact('document'));
    }

    public function update(Request $request, ImportDocument $document)
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(ImportDocument::statusOptions()))],
            'observacoes' => ['nullable', 'string', 'max:65535'],
            'arquivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('arquivo')) {
            if ($document->arquivo && Storage::disk('local')->exists($document->arquivo)) {
                Storage::disk('local')->delete($document->arquivo);
            }
            $validated['arquivo'] = $request->file('arquivo')->store('documentos/'.$document->import_id, 'local');
        } else {
            unset($validated['arquivo']);
        }

        if (isset($validated['status']) && $validated['status'] === 'recebido_ok') {
            $validated['observacoes'] = 'Documento de acordo, sem pendências.';
        }

        unset($validated['import_id']);
        $document->update($validated);

        return redirect()->route('imports.show', $document->import)
            ->with('status', 'Documento atualizado com sucesso!');
    }

    public function download(ImportDocument $document)
    {
        $this->authorize('update', $document);

        if (! $document->arquivo || ! Storage::disk('local')->exists($document->arquivo)) {
            abort(404);
        }

        return Storage::disk('local')->download($document->arquivo, basename($document->arquivo));
    }
}
