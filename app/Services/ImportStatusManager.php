<?php

namespace App\Services;

use App\Models\Import;

/**
 * Gerencia a atualização automática de status dos processos de importação.
 * 
 * Regras: custos obrigatórios pagos → em_desembaraco; frete rodoviário pago + custos pagos + documentos OK → concluido.
 */
class ImportStatusManager
{
    public function evaluateAndUpdateStatus(Import $import): void
    {
        if (in_array($import->status_atual, ['concluido', 'cancelado'])) {
            return;
        }

        $import->refresh();
        $statusAnterior = $import->status_atual;

        if ($this->allMandatoryCostsPaid($import)) {
            if ($import->status_atual !== 'em_desembaraco') {
                $import->updateQuietly(['status_atual' => 'em_desembaraco']);
                $import->refresh();
                
                if ($statusAnterior !== 'em_desembaraco') {
                    ImportLogService::logStatusProcessoAlterado($import, $statusAnterior, 'em_desembaraco', true);
                }
            }
        }

        if ($this->canComplete($import)) {
            if ($import->status_atual !== 'concluido') {
                $statusAntesConclusao = $import->status_atual;
                $import->updateQuietly(['status_atual' => 'concluido']);
                ImportLogService::logStatusProcessoAlterado($import, $statusAntesConclusao, 'concluido', true);
            }
        } else {
            if ($import->status_atual === 'concluido' && $this->allMandatoryCostsPaid($import)) {
                $import->updateQuietly(['status_atual' => 'em_desembaraco']);
                ImportLogService::logStatusProcessoAlterado($import, 'concluido', 'em_desembaraco', true);
            }
        }
    }

    private function canComplete(Import $import): bool
    {
        if ($import->status_atual !== 'em_desembaraco') {
            return false;
        }

        if (!$this->allMandatoryCostsPaid($import)) {
            return false;
        }

        if (!$this->freightRoadPaid($import)) {
            return false;
        }

        if (!$this->allEssentialDocumentsOk($import)) {
            return false;
        }

        return true;
    }

    private function allEssentialDocumentsOk(Import $import): bool
    {
        $essentialDocumentTypes = ['Invoice', 'Packing List', 'BL', 'Mercante'];
        $essentialDocuments = $import->documents()
            ->whereIn('tipo_documento', $essentialDocumentTypes)
            ->get();

        if ($essentialDocuments->count() < 4) {
            return false;
        }

        foreach ($essentialDocuments as $document) {
            if ($document->status !== 'recebido_ok') {
                return false;
            }
        }

        return true;
    }

    private function allMandatoryCostsPaid(Import $import): bool
    {
        $mandatoryCostTypes = ['frete_internacional', 'marinha_mercante', 'armazenagem_porto'];
        $mandatoryCosts = $import->costs()
            ->whereIn('tipo_custo', $mandatoryCostTypes)
            ->get();

        if ($mandatoryCosts->count() < 3) {
            return false;
        }

        foreach ($mandatoryCosts as $cost) {
            if ($cost->status_pagamento !== 'pago') {
                return false;
            }
        }

        return true;
    }

    private function freightRoadPaid(Import $import): bool
    {
        $freightRoad = $import->costs()
            ->where('tipo_custo', 'frete_rodoviario')
            ->first();

        if (!$freightRoad) {
            return false;
        }

        return $freightRoad->status_pagamento === 'pago';
    }
}

