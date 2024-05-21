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
        $webhookOrdersCreate = $this->createWebhook($consumerKey, $consumerSecret,$canal);


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
        $success = $request->input('success');
        $userId = $request->input('user_id');

        if ($success == 1) {
        // El usuario ha sido autenticado con éxito
            return redirect()->route('canales')->with('success', 'Autenticación exitosa. Ahora espere las credenciales.');
        }

        // La autenticación falló
        return redirect()->route('dashboard')->with('error', 'La autenticación falló.');
    }
    public function handleCallback(Request $request)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['consumer_key']) && isset($data['consumer_secret'])) {
            // Guardar las claves API en la base de datos o en la sesión
            Session::put('woocommerce_consumer_key', $data['consumer_key']);
            Session::put('woocommerce_consumer_secret', $data['consumer_secret']);

            // Crear el webhook en WooCommerce
            $this->createWebhook($data['consumer_key'], $data['consumer_secret']);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'API keys not received'], 400);

        // if ($request->has('code') && $request->has('store_url')) {
        //     $authorizationCode = $request->input('code');
        //     $storeUrl = $request->input('store_url');

        //     // Intercambiar el código de autorización por un token de acceso
        //     $accessTokenData = $this->exchangeAuthorizationCodeForToken($authorizationCode, $storeUrl);

        //     // Crear y guardar el canal en la base de datos
        //     $canal = Canal::create([
        //         'canal' => 'woocommerce',
        //         'nombre' => 'Woocommerce', // Puedes obtener este nombre de otro campo del request si es necesario
        //         'url' => $storeUrl,
        //         'usuario_id' => Auth::user()->id(), // O el ID del usuario autenticado
        //         'apikey' => $accessTokenData['consumer_key'],
        //         'secret' => $accessTokenData['consumer_secret'],
        //         'token' => $accessTokenData['access_token'],
        //         'refresh_token' => $accessTokenData['refresh_token'],
        //         'token_type' => $accessTokenData['token_type'],
        //         'expires_in' => $accessTokenData['expires_in'],
        //         'scope' => $accessTokenData['scope'],
        //         // Añade otros campos necesarios
        //     ]);

        //     // Guardar el token de acceso en la sesión
        //     Session::put('woocommerce_access_token', $accessTokenData['access_token']);

        //     // Crear el webhook en WooCommerce
        //     $this->createWebhook($accessTokenData['access_token'], $storeUrl);

        //     return redirect()->route('canales')->with('success', 'Autorización y webhook creados exitosamente');
        // }

       // return redirect()->route('canales')->with('error', 'La autorización falló');

    }

    private function exchangeAuthorizationCodeForToken($authorizationCode, $storeUrl)
    {
        $client = new Client();
        $response = $client->post($storeUrl.'/wp-json/wc/v3/oauth/token', [
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

    private function createWebhook($consumerKey,$consumerSecret,$canal)
    {
        $client = new Client([
            'verify' => false
        ]);
        $deliveryUrl = route('woocommerce.webhook', ['canal_id' => $canal->id]) . '/webhooks-order';
        $response = $client->post($canal->url.'/wp-json/wc/v3/webhooks', [
            'auth' => [$consumerKey, $consumerSecret],
            'json' => [
                'name' => 'Order Created Webhook',
                'topic' => 'order.created',
                'delivery_url' => $deliveryUrl,
                'status' => 'active',
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        // Guardar el webhook en la base de datos
        Webhook::create([
            'name' => $data['name'],
            'canal_id' => $canal->id,
            'status' => $data['status'],
            'topic' => $data['topic'],
            'resource' => $data['resource'],
            'event' => $data['event'],
            'hooks' => $data['hooks'],
            'delivery_url' => $data['delivery_url'],
            'date_created' => $data['date_created'],
            'date_created_gmt' => $data['date_created_gmt'],
            'date_modified' => $data['date_modified'],
            'date_modified_gmt' => $data['date_modified_gmt'],
        ]);

        return $data;

        // Maneja la respuesta del webhook si es necesario
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        dd($payload);
        // Procesar el payload del webhook, por ejemplo, guardando el pedido en la base de datos
        // Asegúrate de validar y manejar los datos según tus necesidades

        // Ejemplo de guardar un pedido
        $order = new Order();
        $order->platform_order_id = $payload['id'];
        $order->status = $payload['status'];
        $order->total = $payload['total'];
        $order->save();

        return response()->json(['status' => 'success'], 200);
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
