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
Route::get('woocommerce/confirmed', [WooCommerceController::class, 'confirmed'])->name('woocommerce.confirmed');
Route::get('woocommerce/add', [WooCommerceController::class, 'add'])->name('woocommerce.add');
//Ruta para Iniciar el Flujo de Autorización de WooCommerce
Route::get('/woo/authorize', function (Request $request) {
    $data = $request->all();
   
    $local_store = config('app.url');
    $remote_store = $data['remote_store'];

    // Guardar los detalles de la tienda en la base de datos
    $store = Canal::where(['url' => $remote_store])->where('user_id',Auth::user()->id)->first();
    if (!$store) {
        $store = new Canal();
    }
    $store->canal = "Woocommerce";
    $store->user_id = Auth::user()->id;
    //$store->data = json_encode($data);
    $store->nombre = "Woocommerce";
    $store->url = $remote_store;
    $store->save();

    $endpoint = '/wc-auth/v1/authorize';
    $params = [
        'app_name' => 'MultiTiendas',
        'scope' => 'read_write',
        'user_id' => Auth::user()->id,
        'return_url' => $local_store.'/woo/connect/response/'.$store->id,
        'callback_url' => $local_store.'/woo/connect/callback/'.$store->id
    ];
    $api = $remote_store . $endpoint . '?' . http_build_query($params);

    return Redirect::away($api);
});
//Ruta para Manejar la Respuesta de Autorización de WooCommerce
Route::any('/woo/connect/response/{local_store}', function ($local_store, Request $request) {
    $data = $request->all();

    $store = Canal::where(['id' => $local_store])->first();

    return view('woo_connect_response', ['store' => $store, 'data' => $data]);
});

//Ruta para Manejar la Devolución de Llamada de WooCommerce
Route::any('/woo/connect/callback/{local_store}', function ($local_store, Request $request) {
    $data = $request->all();

    $store = \App\Store::where(['id' => $data['user_id']])->first();
    if ($store) {
        $store->data = json_encode($data);
        $store->local_store = $data['user_id'];
        $store->status = '200';
        $store->synced_at = now();
        $store->save();
    }

    return ['s' => 200];
});

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
