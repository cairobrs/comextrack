<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('numero_processo')->unique();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->enum('modal', ['maritimo', 'aereo', 'rodoviario']);
            $table->string('ncm_principal')->nullable();
            $table->string('descricao_mercadoria');
            $table->string('pais_origem')->nullable();
            $table->string('porto_origem')->nullable();
            $table->string('porto_destino')->nullable();
            $table->decimal('valor_fatura', 15, 2)->nullable();
            $table->string('moeda', 10)->default('USD');
            $table->date('data_abertura');
            $table->date('data_prevista_chegada')->nullable();
            $table->enum('status_atual', ['aberto', 'em_transito', 'em_desembaraco', 'concluido', 'cancelado'])->default('aberto');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
