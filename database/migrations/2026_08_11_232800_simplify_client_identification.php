<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('nome_fantasia', 'nome_cliente');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('razao_social');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('razao_social')->nullable()->after('nome_cliente');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('nome_cliente', 'nome_fantasia');
        });
    }
};
