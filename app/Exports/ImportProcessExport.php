<?php

namespace App\Exports;

use App\Models\Import;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportProcessExport
{
    protected Import $import;

    public function __construct(Import $import)
    {
        $this->import = $import->load([
            'client',
            'responsavelInterno',
            'documents',
            'costs',
            'steps',
            'logs.user'
        ]);
    }

    public function download(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        
        $this->createResumoSheet($spreadsheet);
        $this->createDocumentosSheet($spreadsheet);
        $this->createCustosSheet($spreadsheet);
        $this->createEtapasSheet($spreadsheet);
        $this->createHistoricoSheet($spreadsheet);
        
        $spreadsheet->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spreadsheet);
        
        $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->import->numero_processo);
        $filename = 'processo_' . $safeFilename . '.xlsx';
        
        return new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function createResumoSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Resumo do Processo');
        
        $row = 1;
        
        $sheet->setCellValue('A' . $row, 'RESUMO DO PROCESSO DE IMPORTAÇÃO');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;
        
        $data = [
            'Número do Processo' => $this->import->numero_processo,
            'NCM principal' => $this->import->ncm_principal ?? '-',
            'Cliente' => $this->import->client->nome_fantasia ?? '-',
            'Modal' => ucfirst($this->import->modal ?? '-'),
            'País de Origem' => $this->import->pais_origem ?? '-',
            'Porto de Origem' => $this->import->porto_origem ?? '-',
            'Porto de Destino' => $this->import->porto_destino ?? '-',
            'Valor da Fatura' => $this->import->valor_fatura ? number_format($this->import->valor_fatura, 2, ',', '.') : '-',
            'Moeda' => $this->import->moeda ?? '-',
            'Taxa de Câmbio' => $this->import->taxa_cambio ? number_format($this->import->taxa_cambio, 4, ',', '.') : '-',
            'Valor Estimado em Reais' => $this->import->valor_fatura_em_reais ? 'R$ ' . number_format($this->import->valor_fatura_em_reais, 2, ',', '.') : '-',
            'Status Atual' => ucfirst(str_replace('_', ' ', $this->import->status_atual ?? '-')),
            'Responsável Interno' => $this->import->responsavelInterno->name ?? '-',
            'Data de Abertura' => $this->import->data_abertura ? $this->import->data_abertura->format('d/m/Y') : '-',
            'Data Prevista de Chegada' => $this->import->data_prevista_chegada ? $this->import->data_prevista_chegada->format('d/m/Y') : '-',
            'Observações' => $this->import->observacoes ?? '-',
        ];
        
        foreach ($data as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    protected function createDocumentosSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Documentos');
        
        $headers = ['Tipo de Documento', 'Status', 'Observações'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }
        
        $row = 2;
        foreach ($this->import->documents as $document) {
            $sheet->setCellValue('A' . $row, $document->tipo_documento);
            $sheet->setCellValue('B' . $row, $document->status_label);
            $sheet->setCellValue('C' . $row, $document->observacoes ?? '-');
            $row++;
        }
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(50);
    }

    protected function createCustosSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Custos');
        
        $headers = ['Tipo de Custo', 'Valor', 'Moeda', 'Valor em BRL', 'Status de Pagamento', 'Data de Pagamento', 'Observações'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }
        
        $row = 2;
        foreach ($this->import->costs as $cost) {
            $valorEmReais = null;
            if ($cost->valor !== null) {
                if ($cost->moeda === 'BRL') {
                    $valorEmReais = $cost->valor;
                } else {
                    $taxa = $this->import->taxa_cambio ?? 1;
                    $valorEmReais = $cost->valor * $taxa;
                }
            }
            $valorEmReaisFormatado = $valorEmReais !== null ? 'R$ ' . number_format($valorEmReais, 2, ',', '.') : '-';
            
            $sheet->setCellValue('A' . $row, $cost->tipo_custo_label);
            $sheet->setCellValue('B' . $row, $cost->valor ? number_format($cost->valor, 2, ',', '.') : '-');
            $sheet->setCellValue('C' . $row, $cost->moeda ?? '-');
            $sheet->setCellValue('D' . $row, $valorEmReaisFormatado);
            $sheet->setCellValue('E' . $row, $cost->status_pagamento_label);
            $sheet->setCellValue('F' . $row, $cost->data_pagamento ? $cost->data_pagamento->format('d/m/Y') : '-');
            $sheet->setCellValue('G' . $row, $cost->observacoes ?? '-');
            $row++;
        }
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(40);
    }

    protected function createEtapasSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Etapas');
        
        $headers = ['Nome da Etapa', 'Data Prevista', 'Data Realizada', 'Responsável', 'Status', 'Observações'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }
        
        $row = 2;
        foreach ($this->import->steps as $step) {
            $statusLabel = match($step->status) {
                'concluida' => 'Concluída',
                'atrasada' => 'Atrasada',
                default => 'Pendente',
            };
            
            $sheet->setCellValue('A' . $row, $step->nome_etapa);
            $sheet->setCellValue('B' . $row, $step->data_prevista ? $step->data_prevista->format('d/m/Y') : '-');
            $sheet->setCellValue('C' . $row, $step->data_realizada ? $step->data_realizada->format('d/m/Y') : '-');
            $sheet->setCellValue('D' . $row, $step->responsavel ?? '-');
            $sheet->setCellValue('E' . $row, $statusLabel);
            $sheet->setCellValue('F' . $row, $step->observacoes ?? '-');
            $row++;
        }
        
        // Ajustar largura das colunas
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(40);
    }

    protected function createHistoricoSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Histórico');
        
        $headers = ['Data e Hora', 'Tipo de Evento', 'Usuário', 'Descrição'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }
        
        $row = 2;
        foreach ($this->import->logs as $log) {
            $sheet->setCellValue('A' . $row, $log->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue('B' . $row, $log->tipo_evento ?? '-');
            $sheet->setCellValue('C' . $row, $log->user->name ?? '-');
            $sheet->setCellValue('D' . $row, $log->descricao ?? '-');
            $row++;
        }
        
        // Ajustar largura das colunas
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(50);
    }
}

