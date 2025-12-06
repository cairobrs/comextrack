<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('clients.update', $client) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="nome_fantasia" :value="__('Nome Fantasia')" />
                                <x-text-input id="nome_fantasia" class="block mt-1 w-full" type="text" name="nome_fantasia" :value="old('nome_fantasia', $client->nome_fantasia)" required autofocus />
                                <x-input-error :messages="$errors->get('nome_fantasia')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="razao_social" :value="__('Razão Social')" />
                                <x-text-input id="razao_social" class="block mt-1 w-full" type="text" name="razao_social" :value="old('razao_social', $client->razao_social)" />
                                <x-input-error :messages="$errors->get('razao_social')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj', $client->cnpj)" />
                                <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('E-mails para contato')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email', $client->email)" placeholder="Separe múltiplos e-mails por vírgula. Ex: operacional@cliente.com, financeiro@cliente.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nome_responsavel" :value="__('Nome do Responsável')" />
                                <x-text-input id="nome_responsavel" class="block mt-1 w-full" type="text" name="nome_responsavel" :value="old('nome_responsavel', $client->nome_responsavel)" placeholder="Exemplo: Maria Souza" />
                                <x-input-error :messages="$errors->get('nome_responsavel')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefone_responsavel" :value="__('Telefone/WhatsApp do Responsável')" />
                                <x-text-input id="telefone_responsavel" class="block mt-1 w-full" type="text" name="telefone_responsavel" :value="old('telefone_responsavel', $client->telefone_responsavel)" placeholder="(34) 99999-9999" />
                                <x-input-error :messages="$errors->get('telefone_responsavel')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="observacoes" :value="__('Observações')" />
                                <textarea id="observacoes" name="observacoes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('observacoes', $client->observacoes) }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Voltar</a>
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
            const telefoneInput = document.getElementById('telefone_responsavel');
            
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    
                    if (value.length === 0) {
                        e.target.value = '';
                        return;
                    }
                    
                    if (value.length <= 2) {
                        e.target.value = '(' + value;
                    } else if (value.length <= 6) {
                        e.target.value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                    } else if (value.length <= 10) {
                        e.target.value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 6) + '-' + value.substring(6);
                    } else {
                        const limited = value.substring(0, 11);
                        e.target.value = '(' + limited.substring(0, 2) + ') ' + limited.substring(2, 7) + '-' + limited.substring(7);
                    }
                });
            }
        });
    </script>
</x-app-layout>

