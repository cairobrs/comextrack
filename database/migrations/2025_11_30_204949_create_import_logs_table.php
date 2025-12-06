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
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained()->onDelete('cascade');
            $table->string('tipo_evento'); // status_processo_alterado, status_documento_alterado, status_custo_alterado
            $table->text('descricao'); // Descrição legível do evento
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('entidade_tipo')->nullable(); // Import, ImportDocument, ImportCost
            $table->unsignedBigInteger('entidade_id')->nullable(); // ID da entidade relacionada
            $table->json('dados_anteriores')->nullable(); // Valores anteriores (opcional)
            $table->json('dados_novos')->nullable(); // Valores novos (opcional)
            $table->boolean('automatico')->default(false); // Indica se foi uma mudança automática
            $table->timestamps();
            
            $table->index(['import_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
