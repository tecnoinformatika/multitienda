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
        Schema::table('canales', function (Blueprint $table) {
            $table->json('productosWoo')->nullable()->after('incrementoprecio');
            $table->json('productosShopify')->nullable()->after('productosWoo');
            $table->json('productosMely')->nullable()->after('productosShopify');
            $table->json('productosMelyShops')->nullable()->after('productosMely');
            $table->json('productosPrestashop')->nullable()->after('productosMelyShops');
            $table->json('productosLinio')->nullable()->after('productosPrestashop');
            $table->json('productosFalabella')->nullable()->after('productosLinio');
            $table->json('productosFacebook')->nullable()->after('productosFalabella');
            $table->integer('totalproductos')->nullable()->after('productosFacebook');

        });
    }

    public function down()
    {
        Schema::table('canales', function (Blueprint $table) {
            $table->dropColumn('productosWoo');
            $table->dropColumn('productosShopify');
            $table->dropColumn('productosMely');
            $table->dropColumn('productosMelyShops');
            $table->dropColumn('productosPrestashop');
            $table->dropColumn('productosLinio');
            $table->dropColumn('productosFalabella');
            $table->dropColumn('productosFacebook');
            $table->dropColumn('totalproductos');
        });
    }
};
