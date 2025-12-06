<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Processos de alto valor</h3>
                        <p class="text-3xl font-bold text-red-600 mb-1">{{ $totalHighValueImports }}</p>
                        <p class="text-xs text-gray-500">Acima de R$ 500 mil</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Processos com pendências</h3>
                        <p class="text-3xl font-bold text-yellow-600 mb-1">{{ $totalPendingImports }}</p>
                        <p class="text-xs text-gray-500">Documentos ou custos pendentes</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Processos concluídos</h3>
                        <p class="text-3xl font-bold text-green-600 mb-1">{{ $totalCompletedImports }}</p>
                        <p class="text-xs text-gray-500">Status: concluído</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
