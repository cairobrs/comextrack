<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('imports')->onDelete('cascade');
            $table->string('tipo_custo');
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('moeda', 10)->default('USD');
            $table->string('status_pagamento')->default('pendente');
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_costs');
    }
};
