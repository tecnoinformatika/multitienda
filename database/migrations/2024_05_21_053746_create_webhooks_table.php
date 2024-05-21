<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->integer('canal_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('status');
            $table->string('topic');
            $table->string('resource');
            $table->string('event');
            $table->text('hooks'); // Store as JSON
            $table->string('delivery_url');
            $table->timestamp('date_created')->nullable();
            $table->timestamp('date_created_gmt')->nullable();
            $table->timestamp('date_modified')->nullable();
            $table->timestamp('date_modified_gmt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhooks');
    }
};
