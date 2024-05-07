<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanalesController;
use App\Http\Controllers\SyscomController;
use App\Http\Controllers\ProductosController;
use Illuminate\Http\Request;
use App\Models\Canal;
use Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\WooCommerceController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Auth::routes();
Route::get('woocommerce/confirmed/{id_tienda }', [WooCommerceController::class, 'confirmed'])->name('woocommerce.confirmed');
Route::get('woocommerce/add/{id_tienda }', [WooCommerceController::class, 'add'])->name('woocommerce.add');
//Ruta para Iniciar el Flujo de Autorización de WooCommerce


Route::get('/', [App\Http\Controllers\HomeController::class, 'root']);
Route::controller(CanalesController::class)->group(function () {
    Route::get('canales', 'canales')->name('canales');
    Route::get('mis-canales', 'misCanales')->name('mis-canales');
    Route::get('woocommerce', 'woocommerce')->name('woocommerce');
    Route::post('crear-woocommerce', 'crearwoocommerce')->name('crear-woocommerce');
    Route::post('validar-credencialesWoo', 'validarcredencialesWoo')->name('validar-credencialesWoo');
    Route::get('syscom', 'syscom')->name('syscom');
    Route::post('crear-syscom', 'crearsyscom')->name('crear-syscom');
    Route::post('validar-credencialesSys', 'validarcredencialesSys')->name('validar-credencialesSys');
    Route::get('canal/{any}/new', 'nuevocanal')->name('nuevocanal');
    Route::get('validar-url', 'validarURL')->name('validar-url');
    Route::get('generar-enlace-autorizacion', 'generarEnlaceAutorizacion')->name('generarEnlaceAutorizacion');
});
Route::controller(ProductosController::class)->group(function () {
    Route::post('sincronizar-producto/{id}', 'sincronizar')->name('sincronizar-producto');
    Route::get('sincronizarsyscom1', 'sincronizarsyscom1')->name('sincronizarsyscom1');
    Route::get('listarproductos', 'listarproductos')->name('listarproductos');
    Route::get('obtener-productos-html', 'obtenerProductosHTML')->name('obtener-productos-html');
    route::get('obtener-ultimos-productos', 'obtenerultimosProductos')->name('obtener-ultimos-productos');
});
Route::controller(SyscomController::class)->group(function () {
    Route::post('seleccionar-destino-syscom/{id}', 'destinosyscom')->name('seleccionar-destino-syscom');
    Route::get('sincronizar-syscom/{idProducto}/{canal}/{canalelegido}/{stock}/{aumento}', 'sincronizarsyscom')->name('sincronizar-syscom');
    Route::get('/subcategorias/{categoria_id}', 'obtenerSubcategorias')->name('subcategorias.obtener');
    Route::get('/productos/{categoria_id}/{canal}', 'productos')->name('productos.obtener');

});
Route::get('/import-syscom-products', function () {
    Artisan::call('importar:productos');
    return 'Importación de productos iniciada';
});

Route::middleware('auth')->group(function () {


    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');



});
