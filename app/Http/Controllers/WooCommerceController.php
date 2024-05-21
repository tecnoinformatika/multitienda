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
    public function __construct()
    {
        $this->middleware('auth');
    }
    
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

        $consumerKey = $request->consumer_key;
        $consumerSecret = $request->consumer_secret;
        $urlClient = $request->urlRequest;

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
        $total = '';
        $page = 1;
        $allProducts = [];

        try {
            // Obtener productos página por página hasta que no haya más productos
            while (true) {
                $products = $woocommerce->get('products', ['per_page' => 100, 'page' => $page]);

                if (empty($products)) {
                    break; // Si no hay más productos, salir del bucle
                }

                $allProducts = array_merge($allProducts, $products);
                $page++;
            }
            $total = count($allProducts);

        } catch (\Exception $e) {
            // Si hay un error al hacer la solicitud, devuelve una respuesta negativa
            return response()->json(['valid' => false]);
        }

        if(isset($canal))
        {
            $canal->canal = "Woocommerce";
            $canal->nombre = "Woocommerce1";
            $canal->user_id = Auth::user()->id;
            $canal->url = $request->urlRequest;
            $canal->apikey = $request->consumer_key;
            $canal->secret = $request->consumer_secret;
            $canal->productosWoo = $allProducts;
            $canal->totalproductos = $total;
            $canal->save();
        }else{
            $canal = new Canal;
            $canal->canal = "Woocommerce";
            $canal->nombre = "Woocommerce1";
            $canal->user_id = Auth::user()->id;
            $canal->url = $request->urlRequest;
            $canal->apikey = $request->consumer_key;
            $canal->secret = $request->consumer_secret;
            $canal->productosWoo = $allProducts;
            $canal->totalproductos = $total;
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
        $products = $canal->productosWoo;

        // Devuelve los productos como respuesta JSON
        return response()->json($products);
    }
    public function handleAuthorizationCallback(Request $request)
    {
        if ($request->has('code')) {
            $authorizationCode = $request->input('code');
            $accessToken = $this->exchangeAuthorizationCodeForToken($authorizationCode);

            // Guardar el token de acceso en la sesión
            Session::put('woocommerce_access_token', $accessToken);

            // Crear el webhook en WooCommerce
            $this->createWebhook($accessToken);

            return redirect()->route('dashboard')->with('success', 'Autorización y webhook creados exitosamente');
        }

        return redirect()->route('dashboard')->with('error', 'La autorización falló');
    }

    private function exchangeAuthorizationCodeForToken($authorizationCode)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://tutienda.com/wp-json/wc/v3/oauth/token', [
            'form_params' => [
                'client_id' => env('WOOCOMMERCE_CLIENT_ID'), // Asegúrate de configurar estos valores en tu archivo .env
                'client_secret' => env('WOOCOMMERCE_CLIENT_SECRET'),
                'redirect_uri' => route('woocommerce.callback'),
                'code' => $authorizationCode,
                'grant_type' => 'authorization_code',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['access_token'])) {
            return $data['access_token'];
        }

        // Maneja errores
        throw new \Exception('No se pudo obtener el token de acceso de WooCommerce');
        }

    private function createWebhook($accessToken)
    {
        $client = new Client();
        $response = $client->post('https://tutienda.com/wp-json/wc/v3/webhooks', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'json' => [
                'name' => 'Pedido Creado',
                'topic' => 'order.created',
                'delivery_url' => route('woocommerce.webhook'),
                'secret' => env('WEBHOOK_SECRET'), // Una clave secreta para validar los webhooks entrantes
            ],
        ]);

        $webhook = json_decode($response->getBody(), true);

        // Maneja la respuesta del webhook si es necesario
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-wc-webhook-signature');

        // Valida la firma del webhook
        if ($this->validateWebhookSignature($payload, $signature)) {
            $data = json_decode($payload, true);

            // Almacenar el pedido en la base de datos
            $this->storeOrder($data);

            return response()->json(['message' => 'Webhook recibido y procesado correctamente'], 200);
        }

        return response()->json(['message' => 'Firma de webhook inválida'], 400);
    }

    private function validateWebhookSignature($payload, $signature)
    {
        $computedSignature = base64_encode(hash_hmac('sha256', $payload, env('WEBHOOK_SECRET'), true));
        return hash_equals($computedSignature, $signature);
    }

    private function storeOrder($data)
    {
        // Implementa la lógica para almacenar el pedido en la base de datos
        // Esto puede incluir modelos como Order, Customer, OrderDetails, etc.
    }

}
