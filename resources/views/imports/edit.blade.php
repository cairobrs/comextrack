<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Processo de Importação') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    <form method="POST" action="{{ route('imports.update', $import) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="client_id" :value="__('Cliente')" />
                                <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $import->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->nome_cliente }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="numero_processo" :value="__('Número do Processo')" />
                                <x-text-input id="numero_processo" class="block mt-1 w-full" type="text" name="numero_processo" :value="old('numero_processo', $import->numero_processo)" required />
                                <x-input-error :messages="$errors->get('numero_processo')" class="mt-2" />
                            </div>

                            <div
                                x-data="{
                                    ncm: @js(old('ncm_principal', $import->ncm_principal)),
                                    applyNcmMask() {
                                        let d = String(this.ncm ?? '').replace(/\D/g, '').slice(0, 8);
                                        let f = '';
                                        if (d.length >= 1) f = d.slice(0, 4);
                                        if (d.length > 4) f += '.' + d.slice(4, 6);
                                        if (d.length > 6) f += '.' + d.slice(6, 8);
                                        this.ncm = f;
                                    }
                                }"
                                x-init="if (ncm) applyNcmMask()"
                            >
                                <x-input-label for="ncm_principal" :value="__('NCM principal')" />
                                <x-text-input
                                    id="ncm_principal"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="ncm_principal"
                                    x-model="ncm"
                                    @input="applyNcmMask()"
                                    maxlength="10"
                                    placeholder="0000.00.00"
                                />
                                <p class="mt-1 text-sm text-gray-500">Formato: 0000.00.00 (opcional)</p>
                                <x-input-error :messages="$errors->get('ncm_principal')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="modal" :value="__('Modal')" />
                                <select id="modal" name="modal" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione o modal</option>
                                    <option value="maritimo" {{ old('modal', $import->modal) == 'maritimo' ? 'selected' : '' }}>Marítimo</option>
                                    <option value="aereo" {{ old('modal', $import->modal) == 'aereo' ? 'selected' : '' }}>Aéreo</option>
                                    <option value="rodoviario" {{ old('modal', $import->modal) == 'rodoviario' ? 'selected' : '' }}>Rodoviário</option>
                                </select>
                                <x-input-error :messages="$errors->get('modal')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="descricao_mercadoria" :value="__('Descrição da Mercadoria')" />
                                <x-text-input id="descricao_mercadoria" class="block mt-1 w-full" type="text" name="descricao_mercadoria" :value="old('descricao_mercadoria', $import->descricao_mercadoria)" required />
                                <x-input-error :messages="$errors->get('descricao_mercadoria')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="pais_origem" :value="__('País de Origem')" />
                                <x-text-input id="pais_origem" class="block mt-1 w-full" type="text" name="pais_origem" :value="old('pais_origem', $import->pais_origem)" />
                                <x-input-error :messages="$errors->get('pais_origem')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="valor_fatura" :value="__('Valor da Fatura')" />
                                <x-text-input id="valor_fatura" class="block mt-1 w-full" type="text" name="valor_fatura_display" :value="old('valor_fatura', $import->valor_fatura) ? number_format((float)old('valor_fatura', $import->valor_fatura), 2, ',', '.') : ''" placeholder="0,00" />
                                <input type="hidden" id="valor_fatura_hidden" name="valor_fatura" value="{{ old('valor_fatura', $import->valor_fatura) }}" />
                                <x-input-error :messages="$errors->get('valor_fatura')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="moeda" :value="__('Moeda')" />
                                <select id="moeda" name="moeda" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="BRL" {{ old('moeda', $import->moeda) == 'BRL' ? 'selected' : '' }}>BRL - Real Brasileiro</option>
                                    <option value="USD" {{ old('moeda', $import->moeda) == 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                    <option value="EUR" {{ old('moeda', $import->moeda) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="JPY" {{ old('moeda', $import->moeda) == 'JPY' ? 'selected' : '' }}>JPY - Iene Japonês</option>
                                    <option value="GBP" {{ old('moeda', $import->moeda) == 'GBP' ? 'selected' : '' }}>GBP - Libra Esterlina</option>
                                    <option value="CNY" {{ old('moeda', $import->moeda) == 'CNY' ? 'selected' : '' }}>CNY - Yuan Chinês</option>
                                    <option value="CHF" {{ old('moeda', $import->moeda) == 'CHF' ? 'selected' : '' }}>CHF - Franco Suíço</option>
                                    <option value="CAD" {{ old('moeda', $import->moeda) == 'CAD' ? 'selected' : '' }}>CAD - Dólar Canadense</option>
                                    <option value="AUD" {{ old('moeda', $import->moeda) == 'AUD' ? 'selected' : '' }}>AUD - Dólar Australiano</option>
                                    <option value="ARS" {{ old('moeda', $import->moeda) == 'ARS' ? 'selected' : '' }}>ARS - Peso Argentino</option>
                                    <option value="MXN" {{ old('moeda', $import->moeda) == 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                                </select>
                                <x-input-error :messages="$errors->get('moeda')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="data_abertura" :value="__('Data de Abertura')" />
                                <x-text-input id="data_abertura" class="block mt-1 w-full" type="date" name="data_abertura" :value="old('data_abertura', $import->data_abertura?->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('data_abertura')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="data_prevista_chegada" :value="__('Data Prevista de Chegada')" />
                                <x-text-input id="data_prevista_chegada" class="block mt-1 w-full" type="date" name="data_prevista_chegada" :value="old('data_prevista_chegada', $import->data_prevista_chegada?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('data_prevista_chegada')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status_atual" :value="__('Status Atual')" />
                                <select id="status_atual" name="status_atual" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="aberto" {{ old('status_atual', $import->status_atual) == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                    <option value="em_transito" {{ old('status_atual', $import->status_atual) == 'em_transito' ? 'selected' : '' }}>Em Trânsito</option>
                                    <option value="em_desembaraco" {{ old('status_atual', $import->status_atual) == 'em_desembaraco' ? 'selected' : '' }}>Em Desembaraço</option>
                                    <option value="concluido" {{ old('status_atual', $import->status_atual) == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                    <option value="cancelado" {{ old('status_atual', $import->status_atual) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                <x-input-error :messages="$errors->get('status_atual')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="observacoes" :value="__('Observações')" />
                                <textarea id="observacoes" name="observacoes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('observacoes', $import->observacoes) }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('imports.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Voltar</a>
                            <x-primary-button>
                                {{ __('Salvar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const valorFaturaInput = document.getElementById('valor_fatura');
            const valorFaturaHidden = document.getElementById('valor_fatura_hidden');

            if (valorFaturaInput && valorFaturaHidden) {
                valorFaturaInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    
                    // Remove tudo que não é número ou vírgula
                    value = value.replace(/[^\d,]/g, '');
                    
                    // Se tiver mais de uma vírgula, mantém apenas a primeira
                    const parts = value.split(',');
                    if (parts.length > 2) {
                        value = parts[0] + ',' + parts.slice(1).join('');
                    }
                    
                    // Limita a 2 casas decimais após a vírgula
                    if (parts.length === 2 && parts[1].length > 2) {
                        value = parts[0] + ',' + parts[1].substring(0, 2);
                    }
                    
                    // Formata com pontos para milhares
                    if (parts.length > 0) {
                        const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        value = parts.length > 1 ? integerPart + ',' + parts[1] : integerPart;
                    }
                    
                    // Atualiza o campo visível
                    e.target.value = value;
                    
                    // Atualiza o campo hidden com valor numérico (sem formatação)
                    const numericValueForSubmit = value.replace(/\./g, '').replace(',', '.');
                    valorFaturaHidden.value = numericValueForSubmit || '';
                });

                // Ao enviar o formulário, garante que o valor está correto
                const form = valorFaturaInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const displayValue = valorFaturaInput.value;
                        const numericValue = displayValue.replace(/\./g, '').replace(',', '.');
                        valorFaturaHidden.value = numericValue || '';
                    });
                }
            }
        });
    </script>
</x-app-layout>

