<?php

namespace App\Http\Controllers;

use App\Models\Import;

class DashboardController extends Controller
{
    public function index()
    {
        $totalHighValueImports = Import::query()
            ->whereNotNull('valor_fatura')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('moeda', 'BRL')
                        ->where('valor_fatura', '>', 500000);
                })->orWhere(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereNull('moeda')->orWhere('moeda', '!=', 'BRL');
                    })
                        ->whereRaw('(valor_fatura * COALESCE(taxa_cambio, 1)) > ?', [500000]);
                });
            })
            ->count();

        $totalPendingImports = Import::where(function($query) {
            $query->whereHas('documents', function($q) {
                $q->whereIn('tipo_documento', ['Invoice', 'Packing List', 'BL', 'Mercante'])
                  ->where('status', '!=', 'recebido_ok');
            })->orWhereHas('costs', function($q) {
                $q->whereIn('tipo_custo', ['frete_internacional', 'marinha_mercante', 'armazenagem_porto', 'frete_rodoviario'])
                  ->where('status_pagamento', 'pendente');
            });
        })->count();

        $totalCompletedImports = Import::where('status_atual', 'concluido')->count();

        return view('dashboard', compact(
            'totalHighValueImports',
            'totalPendingImports',
            'totalCompletedImports'
        ));
    }
}
