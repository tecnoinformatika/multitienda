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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->integer('producto_id')->nullable();
            $table->text('nombre')->nullable();
            $table->string('modelo')->nullable();
            $table->integer('total_existencia')->nullable();
            $table->text('titulo')->nullable();
            $table->string('pvol')->nullable();
            $table->string('marca')->nullable();
            $table->integer('marca_id')->nullable();
            $table->string('sat_key')->nullable();
            $table->string('img_portada')->nullable();
            $table->json('categorias')->nullable();
            $table->integer('nivel1')->nullable();
            $table->integer('nivel2')->nullable();
            $table->integer('nivel3')->nullable();
            $table->string('marca_logo')->nullable();
            $table->string('link')->nullable();
            $table->json('precios')->nullable();
            $table->json('existencia')->nullable();
            $table->json('caracteristicas')->nullable();
            $table->text('descripcion')->nullable();
            $table->json('recursos')->nullable();
            $table->json('imagenes')->nullable();
            $table->json('unidad_de_medida')->nullable();
            $table->string('alto')->nullable();
            $table->string('largo')->nullable();
            $table->string('ancho')->nullable();


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
