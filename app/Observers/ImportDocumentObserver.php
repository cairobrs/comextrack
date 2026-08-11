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

        if ($importDocument->tipo_documento === 'BL'
            && $importDocument->wasChanged('status')
            && $importDocument->status === 'recebido_ok') {
            $import->refresh();
            $statusAnteriorProcesso = $import->status_atual;

            if (! in_array($statusAnteriorProcesso, ['concluido', 'cancelado'], true)) {
                $import->updateQuietly(['status_atual' => 'em_transito']);
                $import->refresh();
                if ($statusAnteriorProcesso !== 'em_transito') {
                    ImportLogService::logStatusProcessoAlterado($import, $statusAnteriorProcesso, 'em_transito', true);
                }
            }
        }

        $import->refresh();

        $statusManager = new ImportStatusManager();
        $statusManager->evaluateAndUpdateStatus($import);
    }
}
