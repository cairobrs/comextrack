<?php

namespace App\Http\Controllers;

use App\Exports\ImportProcessExport;
use App\Http\Requests\StoreImportRequest;
use App\Http\Requests\UpdateImportRequest;
use App\Models\Client;
use App\Models\Import;
use App\Models\User;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Import::class);

        $validated = $request->validate([
            'search_process_number' => 'nullable|string|max:255',
            'client_id' => 'nullable|integer|exists:clients,id',
            'status_atual' => 'nullable|in:aberto,em_transito,em_desembaraco,concluido,cancelado',
        ]);

        $query = Import::with('client');
        $hasSearch = false;

        if (! empty($validated['search_process_number'] ?? null)) {
            $hasSearch = true;
            $searchValue = trim($validated['search_process_number']);
            $numbersOnly = preg_replace('/[^0-9]/', '', $searchValue);

            if (! empty($numbersOnly)) {
                $query->where('numero_processo', 'LIKE', '%'.$numbersOnly.'%');
            }
        }

        if (! empty($validated['client_id'] ?? null)) {
            $query->where('client_id', $validated['client_id']);
        }

        if (! empty($validated['status_atual'] ?? null)) {
            $query->where('status_atual', $validated['status_atual']);
        }

        $imports = $query->paginate(10)->withQueryString();
        $clients = Client::orderBy('nome_fantasia')->get();

        return view('imports.index', compact('imports', 'clients', 'hasSearch'));
    }

    public function create()
    {
        $this->authorize('create', Import::class);

        $clients = Client::orderBy('nome_fantasia')->get();
        $users = User::orderBy('name')->get();

        return view('imports.create', compact('clients', 'users'));
    }

    public function store(StoreImportRequest $request)
    {
        $this->authorize('create', Import::class);

        Import::create($request->validated());

        return redirect()->route('imports.index')
            ->with('success', 'Processo de importação criado com sucesso!');
    }

    public function show(Import $import)
    {
        $this->authorize('view', $import);

        $import->load(['client', 'steps', 'documents', 'costs']);

        return view('imports.show', compact('import'));
    }

    public function edit(Import $import)
    {
        $this->authorize('update', $import);

        $clients = Client::orderBy('nome_fantasia')->get();
        $users = User::orderBy('name')->get();

        return view('imports.edit', compact('import', 'clients', 'users'));
    }

    public function update(UpdateImportRequest $request, Import $import)
    {
        $this->authorize('update', $import);

        $import->update($request->validated());

        return redirect()->route('imports.index')
            ->with('success', 'Processo de importação atualizado com sucesso!');
    }

    public function destroy(Import $import)
    {
        $this->authorize('delete', $import);

        $import->delete();

        return redirect()->route('imports.index')
            ->with('success', 'Processo de importação excluído com sucesso!');
    }

    public function export(Import $import)
    {
        $this->authorize('view', $import);

        $export = new ImportProcessExport($import);

        return $export->download();
    }
}
