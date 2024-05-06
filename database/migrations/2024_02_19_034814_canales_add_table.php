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
        Schema::create('canales', function (Blueprint $table) {
            $table->id();
            $table->string('Canal');
            $table->string('nombre');
            $table->string('url')->nullable();
            $table->string('apikey')->nullable();
            $table->string('secret')->nullable();
            $table->string('pais')->nullable();
            $table->integer('incrementoprecio')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canales');
    }
};
