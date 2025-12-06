<?php

namespace App\Observers;

use App\Models\Import;
use App\Services\ImportLogService;

// Registra logs quando o status do processo é alterado
class ImportObserver
{
    public function updated(Import $import): void
    {
        if ($import->wasChanged('status_atual')) {
            $statusAnterior = $import->getOriginal('status_atual');
            $statusNovo = $import->status_atual;
            $automatico = $import->wasRecentlyCreated === false && !$import->getDirty();
            
            ImportLogService::logStatusProcessoAlterado($import, $statusAnterior, $statusNovo, $automatico);
        }
    }
}
