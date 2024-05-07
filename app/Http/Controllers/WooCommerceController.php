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
            return redirect()->route('canal.canales');
        } else {
            // Autorización denegada, redirigir o mostrar un mensaje de error
            return redirect()->route('woocommerce.error');
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
}
