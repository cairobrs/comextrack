<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova despesa adicional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Nova despesa para o processo {{ $import->numero_processo }}</h3>

                    <form method="POST" action="{{ route('imports.costs.store', $import) }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="nome" :value="__('Nome da despesa')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="valor" :value="__('Valor')" />
                                <x-text-input id="valor" class="block mt-1 w-full" type="number" name="valor" step="0.01" min="0" :value="old('valor')" />
                                <x-input-error :messages="$errors->get('valor')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="moeda" :value="__('Moeda')" />
                                <select id="moeda" name="moeda" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach (['USD', 'BRL', 'EUR', 'GBP', 'JPY', 'CNY'] as $m)
                                        <option value="{{ $m }}" {{ old('moeda', 'USD') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('moeda')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="data_vencimento" :value="__('Data de vencimento')" />
                                <x-text-input id="data_vencimento" class="block mt-1 w-full" type="date" name="data_vencimento" :value="old('data_vencimento')" />
                                <x-input-error :messages="$errors->get('data_vencimento')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="data_pagamento" :value="__('Data de pagamento')" />
                                <x-text-input id="data_pagamento" class="block mt-1 w-full" type="date" name="data_pagamento" :value="old('data_pagamento')" />
                                <x-input-error :messages="$errors->get('data_pagamento')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="observacoes" :value="__('Observações')" />
                                <textarea id="observacoes" name="observacoes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" maxlength="1000">{{ old('observacoes') }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>
                        </div>

                        <p class="mt-4 text-sm text-gray-500">A despesa será criada com status pendente.</p>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('imports.show', $import) }}" class="text-gray-600 hover:text-gray-900 mr-4">Voltar para o processo</a>
                            <x-primary-button>
                                {{ __('Salvar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
