<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Automattic\WooCommerce\Client as WooCommerceClient;
use App\Models\Canal;
use Auth;
use App\Models\CanalDisponible;
use Illuminate\Support\Facades\Validator;

class WooCommerceController extends Controller
{
    public function confirmed($any, Request $request)
    {
        $canales = CanalDisponible::all();


        $success = $request->query('success');
        if ($success == 1) {
            // Autorización exitosa, obtener los parámetros necesarios
            $store = Canal::findOrFail($any);
            $consumerKey = $request->consumer_key;
            $consumerSecret = $request->consumer_secret;

            // Guardar las credenciales en la base de datos
            $store->apikey = $consumerKey;
            $store->secret = $consumerSecret;
            $store->save();

            // Redirigir o mostrar un mensaje de éxito
            return view('canal.canales')->with('canales',$canales);;
        } else {
            // Autorización denegada, redirigir o mostrar un mensaje de error
            return view('canal.canales')->with('canales',$canales);;
        }
    }

    public function add($any, Request $request)
    {
       // Obtener los datos de la solicitud en formato JSON
        $requestData = json_decode(file_get_contents('php://input'), true);

        // Obtener los parámetros necesarios
        $consumerKey = $requestData['consumer_key'];
        $consumerSecret = $requestData['consumer_secret'];

        // Guardar las credenciales en la base de datos
        $store = Canal::findOrFail($any);
        $store->apikey = $consumerKey;
        $store->secret = $consumerSecret;
        $store->save();

    // Enviar una respuesta exitosa
    return response()->json(['message' => 'Credenciales guardadas correctamente'], 200);


    }
    public function crearwoocommerce(Request $request)
    {



        $usuario = Auth::user();

        $canal = Canal::where('url', $request->urlRequest)->where('user_id',Auth::user()->id)->first();

        if(isset($canal))
        {
            $canal->canal = "Woocommerce";
            $canal->nombre = "Woocommerce1";
            $canal->user_id = Auth::user()->id;
            $canal->url = $request->url;
            $canal->apikey = $request->consumer_key;
            $canal->secret = $request->consumer_secret;
            $canal->save();
        }else{
            $canal = new Canal;
            $canal->canal = "Woocommerce";
            $canal->nombre = "Woocommerce1";
            $canal->user_id = Auth::user()->id;
            $canal->url = $request->url;
            $canal->apikey = $request->consumer_key;
            $canal->secret = $request->consumer_secret;
            $canal->save();
        }



        $usuario->canales()->save($canal);

        return redirect('/canales');
    }
    public function validarcredencialesWoo(Request $request)
    {
        $consumerKey = $request->input('consumer_key');
        $consumerSecret = $request->input('consumer_secret');
        $urlClient = $request->input('urlClient');

        $woocommerce = new WooCommerceClient(
            $urlClient,
            $consumerKey,
            $consumerSecret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false, // Desactivar la verificación SSL

            ]
        );




        // Recoge las credenciales del formulario



        try {
            // Intenta hacer una solicitud al API de WooCommerce
            $productos = $woocommerce->get('products');
                    // Si la solicitud es exitosa, devuelve una respuesta positiva
            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            // Si hay un error al hacer la solicitud, devuelve una respuesta negativa
            return response()->json(['valid' => false]);
        }
    }
    public function VerWoocommerce($id)
    {
        $canal = Canal::findOrFail($id);

        $woocommerce = new WooCommerceClient(
            $canal->url,
            $canal->apikey,
            $canal->secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false, // Desactivar la verificación SSL
            ]
        );
        
        // Recupera todos los productos de WooCommerce
        $products = $woocommerce->get('products');
        
        // Devuelve los productos a la vista
        return view('canal.woocommerce', ['canal' => $canal]);

    }
    public function obtenerProductosWoo($id)
    {
        $canal = Canal::findOrFail($id);

        $woocommerce = new WooCommerceClient(
            $canal->url,
            $canal->apikey,
            $canal->secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false, // Desactivar la verificación SSL
            ]
        );
        
        // Recupera todos los productos de WooCommerce
        $products = $woocommerce->get('products');

        // Devuelve los productos como respuesta JSON
        return response()->json($products);
    }
}
