<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('clients.index') }}" class="text-gray-700 hover:text-gray-900">← Voltar</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Informações do Cliente</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Nome do cliente</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $client->nome_cliente }}</dd>
                                </div>
                                @if($client->cnpj)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">CNPJ</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $client->cnpj }}</dd>
                                </div>
                                @endif
                                @if($client->email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">E-mails para contato</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $client->email }}</dd>
                                </div>
                                @endif
                                @if($client->nome_responsavel || $client->telefone_responsavel)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Responsável</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($client->nome_responsavel)
                                            {{ $client->nome_responsavel }}
                                        @endif
                                        @if($client->nome_responsavel && $client->telefone_responsavel)
                                            <br>
                                        @endif
                                        @if($client->telefone_responsavel)
                                            {{ $client->telefone_responsavel }}
                                        @endif
                                    </dd>
                                </div>
                                @endif
                                @if($client->observacoes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Observações</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $client->observacoes }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('clients.edit', $client) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Editar
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Processos de Importação deste Cliente</h3>
                    
                    @if($client->imports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Número do Processo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Modal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Data Prevista Chegada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($client->imports as $import)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $import->numero_processo }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($import->modal) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $import->status_atual)) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $import->data_prevista_chegada ? $import->data_prevista_chegada->format('d/m/Y') : '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('imports.show', $import) }}" class="text-indigo-600 hover:text-indigo-900">Ver Processo</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-700">Nenhum processo de importação encontrado para este cliente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

