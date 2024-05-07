<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use GuzzleHttp\Client;
use Automattic\WooCommerce\Client as WooCommerceClient;
use App\Models\Canal;
use Auth;
use App\Models\CanalDisponible;
use Illuminate\Support\Facades\Validator;

class CanalesController extends Controller
{
    private $client;

    public function Canales()
    {
        $canales = CanalDisponible::all();

        return view('canal/canales')->with('canales',$canales);;
    }

    public function nuevocanal(Request $request, $any){
        $parts = explode('/', $any);
        $canal = end($parts);
        switch ($canal) {
            case 'woocommerce':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'shopify':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'mercadolibre':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'prestashop':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'mercadoshops':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'jumpseller':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'amazon':
                return view('canal/nuevocanal/'.$canal);
                break;
            case 'facebook':
                return view('canal/nuevocanal/'.$canal);
                break;
        }

       
    }
    public function validarURL(Request $request)
    {
        $url = $request->input('url');

        // Crear una instancia del cliente Guzzle
        $client = new Client();
        $response = $client->head(trim($url));
                dd($response);
        // Verificar el código de estado de la respuesta
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return response()->json(['success' => 'La URL existe.']);
        } else {
            return response()->json(['error' => 'La URL no existe o no está accesible.']);
        }
        try {
         
            // Hacer una solicitud HEAD a la URL para verificar si existe
            $response = $client->head($url);
            
            // Verificar el código de estado de la respuesta
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 200 && $statusCode < 400) {
                return response()->json(['success' => 'La URL existe.']);
            } else {
                return response()->json(['error' => 'La URL no existe o no está accesible.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo conectar a la URL.']);
        }
    }
    public function woocommerce(): View
    {
        return view('canal/woocommerce', [
            // Specify the base layout.
            // Eg: 'side-menu', 'simple-menu', 'top-menu', 'login'
            // The default value is 'side-menu'

            // 'layout' => 'side-menu'
        ]);
    }
    public function syscom(): View
    {
        return view('canal/syscom', [
            // Specify the base layout.
            // Eg: 'side-menu', 'simple-menu', 'top-menu', 'login'
            // The default value is 'side-menu'

            // 'layout' => 'side-menu'
        ]);
    }
    public function misCanales()
    {
        $canales = Canal::where('user_id',Auth::user()->id)->get();

        if(!empty($canales)){
            return view('miscanales', [

                'layout' => 'side-menu'
            ])->with('canales',$canales);
        }


    }
    public function crearwoocommerce(Request $request)
    {

        $usuario = Auth::user();


        $canal = new Canal;
        $canal->canal = "Woocommerce";
        $canal->nombre = "Woocommerce1";
        $canal->user_id = Auth::user()->id;
        $canal->url = $request->url;
        $canal->apikey = $request->consumer_key;
        $canal->secret = $request->consumer_secret;
        $canal->save();

        $usuario->canales()->save($canal);

        return redirect('/canales');
    }
    public function crearsyscom(Request $request)
    {

        $usuario = Auth::user();


        $canal = new Canal;
        $canal->canal = "Syscom";
        $canal->nombre = "Syscom1";
        $canal->user_id = Auth::user()->id;
        $canal->url = 'https://developers.syscomcolombia.com/api/v1/';
        $canal->apikey = $request->consumer_key;
        $canal->secret = $request->consumer_secret;
        $canal->save();

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

    public function validarcredencialesSys(Request $request)
    {

        $client = new Client();

        // Obtén las credenciales OAuth de tu archivo .env
        $clientId = $request->input('consumer_key');
        $clientSecret = $request->input('consumer_secret');
        $tokenUrl = "https://developers.syscomcolombia.com/api/v1/";

        try {
            $response = $client->post('https://developers.syscomcolombia.com/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
            ]);

            $body = $response->getBody();
            $token = json_decode($body)->access_token;

            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            // Si hay un error al hacer la solicitud, devuelve una respuesta negativa
            return response()->json(['valid' => false]);
        }
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
}
