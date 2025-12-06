<div class="grid grid-cols-1 gap-6">
    <div>
        <x-input-label for="nome_etapa" :value="__('Nome da Etapa')" />
        <x-text-input id="nome_etapa" class="block mt-1 w-full" type="text" name="nome_etapa" :value="old('nome_etapa', isset($step) ? $step->nome_etapa : '')" required autofocus />
        <x-input-error :messages="$errors->get('nome_etapa')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="data_prevista" :value="__('Data Prevista')" />
        <x-text-input id="data_prevista" class="block mt-1 w-full" type="date" name="data_prevista" :value="old('data_prevista', isset($step) && $step->data_prevista ? $step->data_prevista->format('Y-m-d') : '')" />
        <x-input-error :messages="$errors->get('data_prevista')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="data_realizada" :value="__('Data Realizada')" />
        <x-text-input id="data_realizada" class="block mt-1 w-full" type="date" name="data_realizada" :value="old('data_realizada', isset($step) && $step->data_realizada ? $step->data_realizada->format('Y-m-d') : '')" />
        <x-input-error :messages="$errors->get('data_realizada')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="responsavel" :value="__('Responsável')" />
        <x-text-input id="responsavel" class="block mt-1 w-full" type="text" name="responsavel" :value="old('responsavel', isset($step) ? $step->responsavel : '')" />
        <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="observacoes" :value="__('Observações')" />
        <textarea id="observacoes" name="observacoes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('observacoes', isset($step) ? $step->observacoes : '') }}</textarea>
        <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
    </div>
</div>

