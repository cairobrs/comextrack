<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropForeign(['responsavel_interno_id']);
        });

        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn([
                'responsavel_interno_id',
                'porto_origem',
                'porto_destino',
                'taxa_cambio',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->foreignId('responsavel_interno_id')->nullable()->after('client_id')->constrained('users')->onDelete('set null');
            $table->string('porto_origem')->nullable()->after('pais_origem');
            $table->string('porto_destino')->nullable()->after('porto_origem');
            $table->decimal('taxa_cambio', 10, 4)->nullable()->after('moeda');
        });
    }
};
