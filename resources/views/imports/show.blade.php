<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Processo de Importação') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            @if($import->is_high_value)
                <div class="mb-3 p-3 border-l-4 bg-red-50 border-red-500 rounded shadow-md">
                    <p class="font-semibold text-red-800 text-base">
                        ⚠️ Mercadoria acima de 500 mil reais
                    </p>
                    @if($import->valor_fatura_em_reais)
                        <p class="text-red-700 mt-2 font-medium">
                            Valor estimado em reais: <span class="text-red-900 font-bold">R$ {{ number_format($import->valor_fatura_em_reais, 2, ',', '.') }}</span>
                        </p>
                    @endif
                    <p class="text-sm text-red-700 mt-2">
                        <strong>Atenção:</strong> revisar com cuidado documentação, seguros e condições de embarque.
                    </p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 text-gray-900">
                    <div class="mb-3 flex justify-between items-center">
                        <a href="{{ route('imports.index') }}" class="text-gray-700 hover:text-gray-900">← Voltar</a>
                        <a href="{{ route('imports.export', $import) }}" style="background-color: #16a34a; color: #ffffff;" class="hover:bg-green-700 font-bold py-2 px-4 rounded shadow-md inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ffffff;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exportar para Excel
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-base font-semibold mb-3">Informações do Processo</h3>
                            <dl class="space-y-1.5">
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Número do Processo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $import->numero_processo }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Cliente</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->client->nome_cliente }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Responsável Interno</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($import->responsavelInterno)
                                            {{ $import->responsavelInterno->name }}
                                        @else
                                            <span class="text-gray-400">Não definido</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Modal</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($import->modal) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $import->status_atual)) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Descrição da Mercadoria</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->descricao_mercadoria }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">NCM principal</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->ncm_principal ?: '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-base font-semibold mb-3">Informações de Origem e Destino</h3>
                            <dl class="space-y-1.5">
                                @if($import->pais_origem)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">País de Origem</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->pais_origem }}</dd>
                                </div>
                                @endif
                                @if($import->porto_origem)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Porto de Origem</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->porto_origem }}</dd>
                                </div>
                                @endif
                                @if($import->porto_destino)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Porto de Destino</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->porto_destino }}</dd>
                                </div>
                                @endif
                                @if($import->valor_fatura)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Valor da Fatura</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ number_format($import->valor_fatura, 2, ',', '.') }} {{ $import->moeda }}
                                        @if($import->taxa_cambio && $import->moeda !== 'BRL')
                                            <br><span class="text-xs text-gray-500">Taxa usada: 1 {{ $import->moeda }} = {{ number_format($import->taxa_cambio, 4, ',', '.') }} BRL</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($import->valor_fatura_em_reais)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Valor Estimado em Reais</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">
                                        R$ {{ number_format($import->valor_fatura_em_reais, 2, ',', '.') }}
                                    </dd>
                                </div>
                                @endif
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Data de Abertura</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->data_abertura->format('d/m/Y') }}</dd>
                                </div>
                                @if($import->data_prevista_chegada)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Data Prevista de Chegada</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->data_prevista_chegada->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                                @if($import->observacoes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-700">Observações</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $import->observacoes }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('imports.edit', $import) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Editar
                        </a>
                    </div>
                </div>
            </div>

            @php
                $ordemDocumentosFase1 = ['Invoice', 'Packing List', 'BL', 'Mercante'];
                $documentosFase1 = $import->documents->filter(function($doc) {
                    return in_array($doc->tipo_documento, ['Invoice', 'Packing List', 'BL', 'Mercante']);
                })->sortBy(function($doc) use ($ordemDocumentosFase1) {
                    $index = array_search($doc->tipo_documento, $ordemDocumentosFase1);
                    return $index !== false ? $index : 999;
                });
                $temPendenciaFase1 = $documentosFase1->contains(function($doc) {
                    return $doc->status != 'recebido_ok';
                });
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 text-gray-900">
                    <h3 class="text-base font-semibold mb-2">Antes do navio atracar – Documentos essenciais</h3>
                    <p class="text-xs text-gray-600 mb-3">Documentos necessários para liberação da carga antes da atracação do navio.</p>
                    
                    @if($temPendenciaFase1)
                        <div class="mb-3 p-2 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <p class="text-xs text-yellow-800">
                                ⚠️ <strong>Há pendências nesta fase. Revisar antes de prosseguir.</strong>
                            </p>
                        </div>
                    @endif
                    
                    @if($documentosFase1->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arquivo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documentosFase1 as $document)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $document->tipo_documento }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @if($document->status == 'recebido_ok')
                                                    <span class="text-green-700 font-medium">{{ $document->status_label }}</span>
                                                @elseif($document->status == 'aguardando_correcoes')
                                                    <span class="text-red-700 font-medium">{{ $document->status_label }}</span>
                                                @elseif($document->status == 'aguardando_recebimento')
                                                    <span class="text-yellow-700 font-medium">{{ $document->status_label }}</span>
                                                @elseif($document->status == 'nao_aplicavel')
                                                    <span class="text-gray-700 font-medium">{{ $document->status_label }}</span>
                                                @else
                                                    <span class="text-gray-500">{{ $document->status_label }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                @if($document->arquivo)
                                                    <a href="{{ route('documents.download', $document) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Baixar arquivo
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                @if($document->observacoes)
                                                    @if(strlen($document->observacoes) > 50)
                                                        <span class="observacao-tooltip" data-obs="{{ htmlspecialchars($document->observacoes, ENT_QUOTES, 'UTF-8') }}">
                                                            {{ Str::limit($document->observacoes, 50) }}...
                                                        </span>
                                                    @else
                                                        {{ $document->observacoes }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('documents.edit', $document) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Atualizar status
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-700">Nenhum documento essencial cadastrado para esta fase.</p>
                    @endif
                </div>
            </div>

            @php
                $ordemCustosFase2 = ['frete_internacional', 'marinha_mercante', 'armazenagem_porto'];
                $custosFase2 = $import->costs->filter(function($cost) {
                    return in_array($cost->tipo_custo, ['frete_internacional', 'marinha_mercante', 'armazenagem_porto']);
                })->sortBy(function($cost) use ($ordemCustosFase2) {
                    $index = array_search($cost->tipo_custo, $ordemCustosFase2);
                    return $index !== false ? $index : 999;
                });
                $temCustoPendenteFase2 = $custosFase2->contains(function($cost) {
                    return $cost->status_pagamento == 'pendente';
                });
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 text-gray-900">
                    <h3 class="text-base font-semibold mb-2">Após o navio atracar – Pagamentos obrigatórios</h3>
                    <p class="text-xs text-gray-600 mb-3">Custos que devem ser pagos após a atracação do navio para liberação da carga.</p>
                    
                    @if($temCustoPendenteFase2)
                        <div class="mb-3 p-2 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <p class="text-xs text-yellow-800">
                                ⚠️ <strong>Pagamentos pendentes. Necessário antes da liberação da carga.</strong>
                            </p>
                        </div>
                    @endif
                    
                    @if($custosFase2->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Custo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moeda</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($custosFase2 as $cost)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cost->tipo_custo_label }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->valor !== null ? number_format($cost->valor, 2, ',', '.') : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->moeda ?? '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->data_vencimento ? $cost->data_vencimento->format('d/m/Y') : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->data_pagamento ? $cost->data_pagamento->format('d/m/Y') : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @if($cost->status_pagamento == 'pago')
                                                    <span class="text-green-700 font-medium">{{ $cost->status_pagamento_label }}</span>
                                                @else
                                                    <span class="text-yellow-700 font-medium">{{ $cost->status_pagamento_label }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $cost->observacoes }}">{{ $cost->observacoes ? Str::limit($cost->observacoes, 40) : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('costs.edit', $cost) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Editar custo
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-700">Nenhum custo cadastrado para esta fase.</p>
                    @endif
                </div>
            </div>

            @php
                $custosFase3 = $import->costs->filter(function($cost) {
                    return $cost->tipo_custo == 'frete_rodoviario';
                });
                $temCustoPendenteFase3 = $custosFase3->contains(function($cost) {
                    return $cost->status_pagamento == 'pendente';
                });
                $etapasFase3 = $import->steps->filter(function($step) {
                    $nomeLower = strtolower($step->nome_etapa);
                    return str_contains($nomeLower, 'agendamento') || 
                           str_contains($nomeLower, 'coleta') || 
                           str_contains($nomeLower, 'transferência') || 
                           str_contains($nomeLower, 'eadi') ||
                           str_contains($nomeLower, 'zona secundária');
                });
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 text-gray-900">
                    <h3 class="text-base font-semibold mb-2">Transferência / EADI / Frete rodoviário</h3>
                    <p class="text-xs text-gray-600 mb-3">Etapas de movimentação da carga e custo de transporte para zona secundária/EADI.</p>
                    
                    @if($temCustoPendenteFase3)
                        <div class="mb-3 p-2 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <p class="text-xs text-yellow-800">
                                ⚠️ <strong>Aguardando contratação/pagamento do transporte para EADI.</strong>
                            </p>
                        </div>
                    @endif
                    
                    @if($etapasFase3->count() > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold mb-2">Etapas de Transferência</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Etapa</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Prevista</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Realizada</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($etapasFase3 as $step)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $step->nome_etapa }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $step->data_prevista ? $step->data_prevista->format('d/m/Y') : '-' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $step->data_realizada ? $step->data_realizada->format('d/m/Y') : '-' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    @if($step->status == 'concluida')
                                                        <span class="text-green-700 font-medium">Concluída</span>
                                                    @elseif($step->status == 'atrasada')
                                                        <span class="text-red-700 font-medium">Atrasada</span>
                                                    @else
                                                        <span class="text-yellow-700 font-medium">Pendente</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('imports.steps.edit', $step) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($custosFase3->count() > 0)
                        <div class="mt-4">
                            <h4 class="text-sm font-semibold mb-2">Frete Rodoviário</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Custo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moeda</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($custosFase3 as $cost)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cost->tipo_custo_label }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->valor !== null ? number_format($cost->valor, 2, ',', '.') : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->moeda ?? '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->data_vencimento ? $cost->data_vencimento->format('d/m/Y') : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $cost->data_pagamento ? $cost->data_pagamento->format('d/m/Y') : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @if($cost->status_pagamento == 'pago')
                                                    <span class="text-green-700 font-medium">{{ $cost->status_pagamento_label }}</span>
                                                @else
                                                    <span class="text-yellow-700 font-medium">{{ $cost->status_pagamento_label }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $cost->observacoes }}">{{ $cost->observacoes ? Str::limit($cost->observacoes, 40) : '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('costs.edit', $cost) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Editar custo
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-4 text-gray-900">
                    <h3 class="text-base font-semibold mb-3">Histórico de Movimentações</h3>
                    
                    @if($import->logs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Data/Hora</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Usuário</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Descrição</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($import->logs->take(20) as $log)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                @if($log->user)
                                                    {{ $log->user->name }}
                                                @else
                                                    <span class="text-gray-400">Sistema</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                @if($log->automatico)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                        AUTOMÁTICO
                                                    </span>
                                                @endif
                                                {{ $log->descricao }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($import->logs->count() > 20)
                            <p class="mt-3 text-xs text-gray-500">Mostrando os últimos 20 eventos de {{ $import->logs->count() }} registros.</p>
                        @endif
                    @else
                        <p class="text-gray-700">Nenhuma movimentação registrada para este processo.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .observacao-tooltip {
            cursor: help;
            position: relative;
            display: inline-block;
        }
        
        .observacao-tooltip:hover::after {
            content: attr(data-obs);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1f2937;
            color: white;
            padding: 10px 14px;
            border-radius: 6px;
            white-space: pre-wrap;
            max-width: 400px;
            width: max-content;
            z-index: 1000;
            font-size: 12px;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            word-wrap: break-word;
            pointer-events: none;
        }
        
        .observacao-tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #1f2937;
            z-index: 1001;
            margin-bottom: 4px;
            pointer-events: none;
        }
    </style>
</x-app-layout>
