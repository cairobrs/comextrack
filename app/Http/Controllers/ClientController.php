<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);

        Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        $client->load('imports');

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}
