<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Automattic\WooCommerce\Client as WooCommerceClient;
use App\Models\Canal;
use Auth;
use App\Models\CanalDisponible;
use Illuminate\Support\Facades\Validator;
use Socialite;

class CanalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private $client;


    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();

        // Aquí puedes manejar la lógica para autenticar al usuario en tu aplicación

        return redirect()->route('home');
    }

    public function redirectToMercadoLibre()
    {
        $clientId = config('services.mercadolibre.client_id');
        $redirectUri = config('services.mercadolibre.redirect_uri');

        // Incluye el ID del canal como parte de la URL de redirección
        return redirect()->away("https://auth.mercadolibre.com.co/authorization?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}");
    }

    public function handleMercadoLibreCallback(Request $request)
    {
        $code = $request->input('code');

        $clientId = config('services.mercadolibre.client_id');
        $clientSecret = config('services.mercadolibre.client_secret');
        $redirectUri = config('services.mercadolibre.redirect');

        $client = new Client();
        $response = $client->post("https://api.mercadolibre.com/oauth/token", [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ],
        ]);

        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];
        $refreshToken = json_decode((string) $response->getBody(), true)['refresh_token'];
        $expiresIn = json_decode((string) $response->getBody(), true)['expires_in'];
        $tokenType = json_decode((string) $response->getBody(), true)['token_type'];
        $userId = json_decode((string) $response->getBody(), true)['user_id'];
        $scope = json_decode((string) $response->getBody(), true)['scope'];

        // Guarda el token de acceso asociado con el canal correspondiente
        $canal = new Canal();
        $canal->Canal = 'Mercadolibre';
        $canal->nombre = 'Mercadolibre';
        $canal->token = $accessToken;
        $canal->expires_in = $expiresIn;
        $canal->refresh_token = $refreshToken;
        $canal->MeliUser_id = $userId;
        $canal->user_id = Auth::user()->id;
        $canal->scope = $scope;
        $canal->token_type = $tokenType;
        $canal->save();

        return redirect()->route('canales');
    }
    

    public function Canales()
    {
        $canales = CanalDisponible::all();
       
        $miscanales = Canal::where('user_id',Auth::user()->id)->select('id as id','Canal as Canal','url as url')->get();
      
        return view('canal/canales')->with('canales',$canales)->with('miscanales',$miscanales);
    }
    public function obtenerCanales($id)
    {
         // Obtener el ID del usuario actualmente autenticado
            $userId = Auth::id();

            // Consulta para obtener los otros canales del usuario excluyendo el canal actual
            $canales = Canal::where('user_id', $userId)
                            ->where('id', '!=', $id) // Excluir el canal actual por su ID
                            ->select('id as id', 'Canal as canal', 'url as url')
                            ->get();

            // Devolver los canales como respuesta en formato JSON
            return response()->json($canales);
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
            case 'facebook':
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

        $client = new Client([
            'verify' => false,
        ]);

        try {

            // Hacer una solicitud HEAD a la URL para verificar si existe
            $response = $client->head($url);

            // Verificar el código de estado de la respuesta
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 200 && $statusCode < 400) {
                return response()->json(['success' => 'La URL existe.']);
                // En tu controlador de Laravel o en cualquier lugar donde construyas el enlace

            } else {
                return response()->json(['error' => 'La URL no existe o no está accesible.']);
            }
        } catch (ConnectException $e) {
            return response()->json(['error' => 'No se pudo conectar a la URL.']);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                if ($statusCode === 404) {
                    return response()->json(['error' => 'La URL no existe.']);
                } else {
                    return response()->json(['error' => 'Error al acceder a la URL.']);
                }
            } else {
                return response()->json(['error' => 'No se pudo acceder a la URL.']);
            }
        }
    }
    public function generarEnlaceAutorizacion(Request $request)
    {
        $url = $request->input('url1');
        // Verificar si la URL termina con '/' y quitarlo si es así
        if (substr($url, -1) === '/') {
            $url = rtrim($url, '/');
        }

        $urlbase = config('app.url');
        $store = Canal::where(['url' => $url])->where('user_id',Auth::user()->id)->first();
        if (!$store) {
            $store = new Canal();
        }
        $store->canal = "Woocommerce";
        $store->user_id = Auth::user()->id;
        //$store->data = json_encode($data);
        $store->nombre = "Woocommerce";
        $store->url = $url;
        $store->save();
        // Construye el enlace de autorización
        $woocommerce_auth_url = $url.'/wc-auth/v1/authorize';
        $app_name = 'MultiTiendas';
        $scope = 'read_write';
        $user_id = Auth::user()->id;
        $return_url = urlencode($urlbase.'/woocommerce/confirmed/'.$store->id);
        $callback_url = urlencode($urlbase.'/woocommerce/add/'.$store->id);
        $authorization_link = "$woocommerce_auth_url?app_name=$app_name&scope=$scope&user_id=$user_id&return_url=$return_url&callback_url=$callback_url";

        // Retorna el enlace de autorización
        return response()->json(['authorization_link' => $authorization_link]);
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
    public function redirigirCanal($any,$id)
    {
        $parts = explode('/', $any);
        $canal = end($parts);
        switch ($canal) {
            case 'Woocommerce':
                return redirect('/VerWoocommerce/'.$id);
                break;
            case 'Shopify':
                return redirect('/VerShopify/'.$id);
                break;
            case 'Facebook':
                return redirect('/VerFacebook/'.$id);
                break;
            case 'Mercadolibre':
                return redirect('/VerMercadolibre/'.$id);
                break;
            case 'Shopify':
                return redirect('/VerShopify'.$id);
                break;
            case 'Shopify':
                return redirect('/VerShopify'.$id);
                break;
            case 'Shopify':
                return redirect('/VerShopify'.$id);
                break;
            case 'Shopify':
                return redirect('/VerShopify'.$id);
                break;
        }
    }
}
