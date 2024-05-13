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
            $table->string('token')->nullable();
            $table->string('refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canales', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('refresh_token');
        });
        
    }
};
