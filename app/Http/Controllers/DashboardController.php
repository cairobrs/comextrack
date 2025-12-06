<?php

namespace App\Http\Controllers;

use App\Models\Import;

class DashboardController extends Controller
{
    public function index()
    {
        $totalHighValueImports = Import::altoValor()
            ->get()
            ->filter(fn($import) => $import->is_high_value)
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
