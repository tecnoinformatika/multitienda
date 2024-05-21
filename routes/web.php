<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanalesController;
use App\Http\Controllers\SyscomController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\MercadolibreController;
use App\Http\Controllers\PedidosController;
use Illuminate\Http\Request;
use App\Models\Canal;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\WooCommerceController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NotificationMeliController;

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

//Ruta para Iniciar el Flujo de Autorización de socialite
Route::get('/auth/google', 'Auth\LoginController@redirectToGoogle')->name('auth.google');
Route::get('/auth/google/callback', 'Auth\LoginController@handleGoogleCallback');
Route::get('/woocommerce/callback', [WooCommerceController::class, 'handleAuthorizationCallback'])->name('woocommerce.callback');
Route::post('/woocommerce/handle-callback', [WooCommerceController::class, 'handleCallback'])->name('woocommerce.handleCallback');
Route::post('/woocommerce/webhook/{canal_id}/{tipo}', [WooCommerceController::class, 'handleWebhook'])->name('woocommerce.webhook');



Route::controller(PedidosController::class)->group(function () {
    Route::get('verpedidos', 'verpedidos')->name('verpedidos');
    Route::get('listarTodoslosPedidos', 'listarTodoslosPedidos')->name('listarTodoslosPedidos');
});
//Ruta para Iniciar el Flujo de Autorización de Syscom
Route::controller(WooCommerceController::class)->group(function () {
    Route::any('woocommerce/confirmed/{any}', 'confirmed')->name('woocommerce.confirmed');
    Route::any('woocommerce/add/{any}', 'add')->name('woocommerce.add');
    Route::post('crear-woocommerce', 'crearwoocommerce')->name('crear-woocommerce');
    Route::post('validar-credencialesWoo', 'validarcredencialesWoo')->name('validar-credencialesWoo');
    Route::get('VerWoocommerce/{id}', 'VerWoocommerce')->name('VerWoocommerce');
    Route::get('/obtenerProductosWoo/{id}', 'obtenerProductosWoo')->name('obtenerProductosWoo');
});
//Rutas para manejo de Mercadolibre
Route::controller(MercadolibreController::class)->group(function () {
    Route::get('VerMercadolibre/{id}', 'VerMercadolibre')->name('VerMercadolibre');
    Route::get('/obtenerProductosMeli/{id}', 'obtenerProductosMeli')->name('obtenerProductosMeli');
    route::get('createTestUsers', 'createTestUsers');
    Route::get('/listarPedidos/{canal_id}', 'descargarPedidos')->name('descargarPedidos ');
});
Route::controller(NotificationMeliController::class)->group(function () {

    Route::post('/mercadolibre/notification', 'handleMercadolibreNotification');
});
//Ruta para Iniciar el Flujo de canales
Route::get('/', [App\Http\Controllers\HomeController::class, 'root']);
Route::controller(CanalesController::class)->group(function () {
    Route::get('canales', 'canales')->name('canales');
    Route::get('mis-canales', 'misCanales')->name('mis-canales');
    Route::get('woocommerce', 'woocommerce')->name('woocommerce');
    Route::get('ver/{any}/{id}', 'redirigirCanal')->name('redirigirCanal');
    Route::get('syscom', 'syscom')->name('syscom');
    Route::post('crear-syscom', 'crearsyscom')->name('crear-syscom');
    Route::post('validar-credencialesSys', 'validarcredencialesSys')->name('validar-credencialesSys');
    Route::get('canal/{any}/new', 'nuevocanal')->name('nuevocanal');
    Route::get('validar-url', 'validarURL')->name('validar-url');
    Route::get('generar-enlace-autorizacion', 'generarEnlaceAutorizacion')->name('generarEnlaceAutorizacion');
    Route::get('obtenerCanales/{id}', 'obtenerCanales')->name('obtenerCanales');
    Route::get('/login/facebook', 'redirectToFacebook')->name('login.facebook');
    Route::get('/login/facebook/callback', 'handleFacebookCallback')->name('login.facebook.callback');
    Route::get('/auth/mercadolibre', 'redirectToMercadoLibre')->name('auth.mercadolibre');
    Route::get('/auth/mercadolibre/callback', 'handleMercadoLibreCallback');
});

//Ruta para Iniciar el Flujo de productos
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
