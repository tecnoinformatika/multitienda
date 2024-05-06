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
        Schema::table('canales', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); // Asumiendo que user_id es un ID de usuario
            // Aquí puedes agregar más columnas si es necesario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canales', function (Blueprint $table) {
            $table->dropColumn('user_id');
            // Si es necesario, también puedes revertir los cambios aquí
        });
    }
};
