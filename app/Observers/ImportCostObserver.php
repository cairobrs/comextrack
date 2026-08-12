<?php

namespace App\Observers;

use App\Models\ImportCost;
use App\Models\Import;
use App\Services\ImportLogService;
use App\Services\ImportStatusManager;

class ImportCostObserver
{
    public function created(ImportCost $importCost): void
    {
        if (! $importCost->isAdicional()) {
            return;
        }

        ImportLogService::logCustoCriado($importCost);

        $import = $importCost->import;
        if (! $import) {
            return;
        }

        $statusManager = new ImportStatusManager();
        $statusManager->evaluateAndUpdateStatus($import);
    }

    public function updated(ImportCost $importCost): void
    {
        if ($importCost->wasChanged('status_pagamento')) {
            $statusAnterior = $importCost->getOriginal('status_pagamento');
            $statusNovo = $importCost->status_pagamento;
            ImportLogService::logStatusCustoAlterado($importCost, $statusAnterior, $statusNovo);
        }

        $import = $importCost->import;
        if (! $import) {
            return;
        }

        $statusManager = new ImportStatusManager();
        $statusManager->evaluateAndUpdateStatus($import);
    }

    public function deleted(ImportCost $importCost): void
    {
        if (! $importCost->isAdicional()) {
            return;
        }

        $importId = $importCost->import_id;
        $nomeDespesa = $importCost->tipo_custo_label;
        $costId = $importCost->id;

        ImportLogService::logCustoExcluido($importId, $nomeDespesa, $costId);

        $import = Import::find($importId);
        if (! $import) {
            return;
        }

        $statusManager = new ImportStatusManager();
        $statusManager->evaluateAndUpdateStatus($import);
    }
}
