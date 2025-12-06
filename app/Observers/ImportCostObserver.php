<?php

namespace App\Observers;

use App\Models\ImportCost;
use App\Services\ImportStatusManager;
use App\Services\ImportLogService;

// Registra logs e avalia atualização de status após alterações em custos
class ImportCostObserver
{
    public function updated(ImportCost $importCost): void
    {
        if ($importCost->wasChanged('status_pagamento')) {
            $statusAnterior = $importCost->getOriginal('status_pagamento');
            $statusNovo = $importCost->status_pagamento;
            ImportLogService::logStatusCustoAlterado($importCost, $statusAnterior, $statusNovo);
        }

        $import = $importCost->import;
        if (!$import) {
            return;
        }

        $statusManager = new ImportStatusManager();
        $statusManager->evaluateAndUpdateStatus($import);
    }
}
