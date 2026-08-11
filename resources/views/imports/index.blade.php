<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Processos de Importação') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 text-gray-900">
                    <h3 class="text-base font-semibold mb-3">Filtros</h3>
                    <form method="GET" action="{{ route('imports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <x-input-label for="search_process_number" :value="__('Buscar por número do processo')" />
                            <x-text-input id="search_process_number" class="block mt-1 w-full" type="text" name="search_process_number" :value="request('search_process_number')" placeholder="Ex: 178988 (apenas números)" />
                            <p class="mt-1 text-xs text-gray-500">Digite apenas os números do processo</p>
                        </div>

                        <div>
                            <x-input-label for="client_id" :value="__('Cliente')" />
                            <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Todos os clientes</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nome_cliente }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="status_atual" :value="__('Status')" />
                            <select id="status_atual" name="status_atual" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Todos os status</option>
                                <option value="aberto" {{ request('status_atual') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                <option value="em_transito" {{ request('status_atual') == 'em_transito' ? 'selected' : '' }}>Em Trânsito</option>
                                <option value="em_desembaraco" {{ request('status_atual') == 'em_desembaraco' ? 'selected' : '' }}>Em Desembaraço</option>
                                <option value="concluido" {{ request('status_atual') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                <option value="cancelado" {{ request('status_atual') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <x-primary-button type="submit">Buscar</x-primary-button>
                            @if(request()->hasAny(['client_id', 'status_atual', 'search_process_number']))
                                <a href="{{ route('imports.index') }}" class="ml-2 text-gray-700 hover:text-gray-900">Limpar</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    <div class="mb-3">
                        <a href="{{ route('imports.create') }}" class="bg-blue-600 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded shadow-md">
                            Novo Processo
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Número do Processo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Cliente</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Modal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Data Prevista Chegada</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($imports as $import)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $import->numero_processo }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $import->client->nome_cliente }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($import->modal) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $import->status_atual)) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $import->data_prevista_chegada ? $import->data_prevista_chegada->format('d/m/Y') : '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('imports.show', $import) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                            <a href="{{ route('imports.edit', $import) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Editar</a>
                                            <form action="{{ route('imports.destroy', $import) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este processo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-700">
                                            @if($hasSearch ?? false)
                                                <strong>Nenhum processo encontrado com esse número.</strong>
                                            @else
                                                Nenhum processo de importação encontrado.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $imports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

