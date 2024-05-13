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
            $table->string('token_type')->nullable();
            $table->string('expires_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canales', function (Blueprint $table) {
            $table->dropColumn('token_type');
            $table->dropColumn('expires_in');
        });
        
    }
};
