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
        Schema::table('clients', function (Blueprint $table) {
            // Verificar se a coluna telefone_responsavel já existe (pode ter sido criada pelo renameColumn)
            if (!Schema::hasColumn('clients', 'telefone_responsavel')) {
                // Se não existe, criar
                $table->string('telefone_responsavel')->nullable()->after('telefone');
            }
            // Adicionar nome_responsavel
            if (!Schema::hasColumn('clients', 'nome_responsavel')) {
                $table->string('nome_responsavel')->nullable()->after('telefone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['nome_responsavel', 'telefone_responsavel']);
        });
    }
};
