<?php

namespace App\Services;

use App\Models\ImportLog;
use Illuminate\Support\Facades\Auth;

class ImportLogService
{
    public static function logStatusProcessoAlterado($import, $statusAnterior, $statusNovo, $automatico = false): void
    {
        $descricao = $automatico 
            ? "AUTOMÁTICO: Status do processo alterado de '{$statusAnterior}' para '{$statusNovo}'"
            : "Status do processo alterado de '{$statusAnterior}' para '{$statusNovo}'";

        ImportLog::create([
            'import_id' => $import->id,
            'tipo_evento' => 'status_processo_alterado',
            'descricao' => $descricao,
            'user_id' => Auth::id(),
            'entidade_tipo' => 'Import',
            'entidade_id' => $import->id,
            'dados_anteriores' => ['status_atual' => $statusAnterior],
            'dados_novos' => ['status_atual' => $statusNovo],
            'automatico' => $automatico,
        ]);
    }

    public static function logStatusDocumentoAlterado($importDocument, $statusAnterior, $statusNovo): void
    {
        $descricao = "Status do documento '{$importDocument->tipo_documento}' alterado de '{$statusAnterior}' para '{$statusNovo}'";

        ImportLog::create([
            'import_id' => $importDocument->import_id,
            'tipo_evento' => 'status_documento_alterado',
            'descricao' => $descricao,
            'user_id' => Auth::id(),
            'entidade_tipo' => 'ImportDocument',
            'entidade_id' => $importDocument->id,
            'dados_anteriores' => ['status' => $statusAnterior],
            'dados_novos' => ['status' => $statusNovo],
            'automatico' => false,
        ]);
    }

    public static function logStatusCustoAlterado($importCost, $statusAnterior, $statusNovo): void
    {
        $descricao = "Status de pagamento do custo '{$importCost->tipo_custo_label}' alterado de '{$statusAnterior}' para '{$statusNovo}'";

        ImportLog::create([
            'import_id' => $importCost->import_id,
            'tipo_evento' => 'status_custo_alterado',
            'descricao' => $descricao,
            'user_id' => Auth::id(),
            'entidade_tipo' => 'ImportCost',
            'entidade_id' => $importCost->id,
            'dados_anteriores' => ['status_pagamento' => $statusAnterior],
            'dados_novos' => ['status_pagamento' => $statusNovo],
            'automatico' => false,
        ]);
    }

    public static function logCustoCriado($importCost): void
    {
        ImportLog::create([
            'import_id' => $importCost->import_id,
            'tipo_evento' => 'custo_criado',
            'descricao' => "Despesa '{$importCost->tipo_custo_label}' criada",
            'user_id' => Auth::id(),
            'entidade_tipo' => 'ImportCost',
            'entidade_id' => $importCost->id,
            'dados_novos' => self::costSnapshot($importCost),
            'automatico' => false,
        ]);
    }

    public static function logCustoAlterado($importCost, array $dadosAnteriores, array $dadosNovos): void
    {
        ImportLog::create([
            'import_id' => $importCost->import_id,
            'tipo_evento' => 'custo_alterado',
            'descricao' => "Despesa '{$importCost->tipo_custo_label}' atualizada",
            'user_id' => Auth::id(),
            'entidade_tipo' => 'ImportCost',
            'entidade_id' => $importCost->id,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'automatico' => false,
        ]);
    }

    public static function logCustoExcluido(int $importId, string $nomeDespesa, ?int $costId = null): void
    {
        ImportLog::create([
            'import_id' => $importId,
            'tipo_evento' => 'custo_excluido',
            'descricao' => "Despesa '{$nomeDespesa}' excluída",
            'user_id' => Auth::id(),
            'entidade_tipo' => 'ImportCost',
            'entidade_id' => $costId,
            'dados_anteriores' => ['nome' => $nomeDespesa],
            'automatico' => false,
        ]);
    }

    public static function costSnapshot($importCost): array
    {
        return [
            'categoria' => $importCost->categoria,
            'tipo_custo' => $importCost->tipo_custo,
            'nome' => $importCost->nome,
            'valor' => $importCost->valor,
            'moeda' => $importCost->moeda,
            'status_pagamento' => $importCost->status_pagamento,
            'data_vencimento' => $importCost->data_vencimento?->format('Y-m-d'),
            'data_pagamento' => $importCost->data_pagamento?->format('Y-m-d'),
            'observacoes' => $importCost->observacoes,
        ];
    }
}

