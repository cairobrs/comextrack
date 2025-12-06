<?php

namespace App\Observers;

use App\Models\ImportDocument;
use App\Services\ImportStatusManager;
use App\Services\ImportLogService;

// Quando BL é recebido, muda processo para "em_transito". Avalia conclusão após alterações.
class ImportDocumentObserver
{
    public function updated(ImportDocument $importDocument): void
    {
        if ($importDocument->wasChanged('status')) {
            $statusAnterior = $importDocument->getOriginal('status');
            $statusNovo = $importDocument->status;
            ImportLogService::logStatusDocumentoAlterado($importDocument, $statusAnterior, $statusNovo);
        }

        $import = $importDocument->import;
        if (!$import) {
            return;
        }

        if (in_array($import->status_atual, ['concluido', 'cancelado'])) {
            return;
        }

        $statusAnteriorProcesso = $import->status_atual;

        if ($importDocument->tipo_documento === 'BL' && $importDocument->wasChanged('status')) {
            $import->updateQuietly(['status_atual' => 'em_transito']);
            if ($import->wasChanged('status_atual')) {
                ImportLogService::logStatusProcessoAlterado($import, $statusAnteriorProcesso, 'em_transito', true);
            }
        }

        $statusManager = new ImportStatusManager();
        $statusManager->evaluateAndUpdateStatus($import);
    }
}
