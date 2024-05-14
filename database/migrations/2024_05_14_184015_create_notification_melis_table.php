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
        Schema::create('notification_melis', function (Blueprint $table) {
            $table->id();
            $table->string('notification_id');
            $table->string('resource');
            $table->unsignedBigInteger('user_id');
            $table->string('topic');
            $table->unsignedBigInteger('application_id');
            $table->integer('attempts');
            $table->timestamp('sent');
            $table->timestamp('received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_melis');
    }
};
