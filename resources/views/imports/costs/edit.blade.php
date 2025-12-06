<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Atualizar custo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">
                            {{ $cost->tipo_custo_label }} - Processo {{ $cost->import->numero_processo }}
                        </h3>
                    </div>

                    <form method="POST" action="{{ route('costs.update', $cost) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="tipo_custo" :value="__('Tipo de Custo')" />
                                <x-text-input id="tipo_custo" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $cost->tipo_custo_label }}" disabled />
                                <p class="mt-1 text-sm text-gray-500">Este campo não pode ser alterado</p>
                            </div>

                            <div>
                                <x-input-label for="status_pagamento" :value="__('Status de Pagamento')" />
                                <select id="status_pagamento" name="status_pagamento" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach(\App\Models\ImportCost::statusPagamentoOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ old('status_pagamento', $cost->status_pagamento) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status_pagamento')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('imports.show', $cost->import) }}" class="text-gray-600 hover:text-gray-900 mr-4">Voltar para o processo</a>
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

