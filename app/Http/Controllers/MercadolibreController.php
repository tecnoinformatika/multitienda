<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canal;
use GuzzleHttp\Client;
use App\Models\UsuarioMercadoLibre;
use App\Models\NotificationMeli;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderShipping;
use App\Models\OrderBilling;
use App\Models\OrderPayment;
use App\Models\Customer;

class MercadolibreController extends Controller
{

    // Ver los productos del usuario en MercadoLibre
    public function VerMercadolibre($id)
    {
        $canal = Canal::findOrFail($id);


         // Verificar y actualizar el token de acceso si es necesario
         $this->verificarYActualizarToken($canal);

         // Obtener todos los productos del usuario en MercadoLibre
         $this->obtenerInformacionUsuario($canal);
         $productos = $this->obtenerProductos($canal->token);

        // Devuelve los productos a la vista
        return view('canal.mercadolibre', ['canal' => $canal, 'productos' => $productos]);
    }

    public function obtenerProductosMeli($id){
        $canal = Canal::findOrFail($id);
        $productos = $this->obtenerProductos($canal->token);

        return response()->json($productos);
    }
    private function obtenerInformacionUsuario($canal)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $canal->token,
            ],
        ]);

        $response = $client->get('https://api.mercadolibre.com/users/me');
        $userData = json_decode((string) $response->getBody(), true);

        // Verifica si se obtuvo la información del usuario correctamente
        if (isset($userData['id'])) {
            $userId = $userData['id'];
            $username = $userData['nickname']; // Aquí se obtiene el nombre de usuario

            // Actualiza el nombre de usuario en el modelo Canal
            $canal->nombre = $username;
            $canal->save();

            return ['user_id' => $userId, 'username' => $username];
        } else {
            // Maneja el caso en el que no se pudo obtener la información del usuario
            return null;
        }
    }
    private function obtenerUserId($accessToken)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $response = $client->get('https://api.mercadolibre.com/users/me');
        $userData = json_decode((string) $response->getBody(), true);

        // Verifica si se obtuvo el ID del usuario correctamente
        if (isset($userData['id'])) {
            return $userData['id'];
        } else {
            // Maneja el caso en el que no se puede obtener el ID del usuario
            return null;
        }
    }
    // Verificar y actualizar el token de acceso si es necesario
    private function verificarYActualizarToken($canal)
    {
            $expiresIn = $canal->expires_in ?? 0;

            $newAccessToken = $this->refreshAccessToken($canal);

    }
    // Actualizar el token de acceso si es necesario
    private function refreshAccessToken($canal)
    {
        $clientId = config('services.mercadolibre.client_id');
        $clientSecret = config('services.mercadolibre.client_secret');
        $refreshToken = $canal->refresh_token;

        $client = new Client();
        $response = $client->post("https://api.mercadolibre.com/oauth/token", [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
            ],
        ]);

        $canal->token = json_decode((string) $response->getBody(), true)['access_token'];
        $canal->refresh_token = json_decode((string) $response->getBody(), true)['refresh_token'];
        $canal->expires_in = json_decode((string) $response->getBody(), true)['expires_in'];
        $canal->updated_at = now();
        $canal->save();

        return json_decode((string) $response->getBody(), true)['access_token'];
    }
    // Obtener todos los productos del usuario en MercadoLibre
    private function obtenerProductos($accessToken)
    {
        $userId = $this->obtenerUserId($accessToken);

        if (!$userId) {
            // Maneja el caso en el que no se pudo obtener el ID del usuario
            return null;
        }

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $response = $client->get("https://api.mercadolibre.com/users/{$userId}/items/search");
        $productoIds = json_decode((string) $response->getBody(), true)['results'];

        $productosDetallados = [];

        foreach ($productoIds as $productoId) {
            $response = $client->get("https://api.mercadolibre.com/items/{$productoId}");
            $productoDetallado = json_decode((string) $response->getBody(), true);

            // Obtener el nombre de la categoría
            $categoriaId = $productoDetallado['category_id'];
            $responseCategoria = $client->get("https://api.mercadolibre.com/categories/{$categoriaId}");
            $categoriaDetallada = json_decode((string) $responseCategoria->getBody(), true);
            $categoriaNombre = $categoriaDetallada['name'];

            // Agregar el nombre de la categoría al producto
            $productoDetallado['category_name'] = $categoriaNombre;

            $productosDetallados[] = $productoDetallado;
        }

        return $productosDetallados;
    }

    public function createTestUsers()
    {
        $canal = Canal::findOrFail(20);

        $this->verificarYActualizarToken($canal);
        $accessToken = $canal->token; // Aquí deberías poner tu token de acceso

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);

        // Iterar para crear 10 usuarios de prueba
        for ($i = 0; $i < 10; $i++) {
            $response = $client->post('https://api.mercadolibre.com/users/test_user', [
                'json' => [
                    'site_id' => 'MCO',
                ],
            ]);
            //dd($response);
            $userData = json_decode($response->getBody(), true);

            // Crear un registro en la base de datos para el usuario creado
            UsuarioMercadoLibre::create([
                'user_id' => auth()->id(), // Puedes ajustar esto según tu lógica de autenticación
                'nickname' => $userData['nickname'],
                'password' => $userData['password'],
                'site_status' => $userData['site_status'],
            ]);
        }

        return response()->json(['message' => 'Se crearon 10 usuarios de prueba en MercadoLibre']);
    }
    public function descargarPedidos($canal_id)
    {
        $canal = Canal::findOrFail($canal_id);
        $this->verificarYActualizarToken($canal);
        // Obtener el token de acceso del canal o de donde lo tengas almacenado
        $accessToken = $canal->token;

        // Obtener el ID del vendedor (seller) asociado al usuario autenticado
        $sellerId = $canal->MeliUser_id; // Suponiendo que tengas el ID del vendedor almacenado en la tabla de usuarios

        // Obtener los datos de los pedidos
        $datosPedidos = $this->obtenerPedidos($accessToken, $sellerId);
        //dd($datosPedidos);
        // Assuming you have a model called Order that has the necessary fields

        $this->almacenarPedidos($datosPedidos, $canal);
    }
    // Obtener los datos de los pedidos

    public function obtenerPedidos($accessToken, $sellerId)
    {
        // Realizar la solicitud GET a la API de MercadoLibre para obtener los pedidos
        $client = new Client();

        $response = $client->get("https://api.mercadolibre.com/orders/search?seller={$sellerId}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        // Decodificar la respuesta JSON
        $datosPedidos = json_decode($response->getBody(), true);



        return $datosPedidos;
    }
    public function almacenarPedidos($datosPedidos, $canal)
    {
        foreach ($datosPedidos['results'] as $pedidos) {

            $pedido = $this->consultarComprador($pedidos['id'], $canal);
            $envio = $this->consultarEnvio($pedido['shipping']['id'], $canal);
            //dd($envio);
             // Verificar si el pedido ya existe
            if (Order::where('platform_order_id', $pedido['id'])->exists()) {
                continue; // Saltar este pedido si ya existe
            }


            // Almacenar el cliente
            $customer = Customer::updateOrCreate(
                ['document' => $pedido ['buyer']['id']], // Suponiendo que el ID del cliente en MercadoLibre coincide con el documento
                [
                    'first_name' => $pedido['buyer']['first_name'],
                    'last_name' => $pedido['buyer']['last_name'],
                    'nickname' => $pedido['buyer']['nickname'],
                    'city' => $envio['receiver_address']['city']['name'],
                    'state' => $envio['receiver_address']['state']['name'],
                    'postcode' => $envio['receiver_address']['zip_code'],
                    'country' => $envio['receiver_address']['country']['name'],
                    'address' => $envio['receiver_address']['address_line'].' - '.$envio['receiver_address']['neighborhood']['name'].' - '.$envio['receiver_address']['comment'],
                    'canal_id' => $canal->id,
                ]
            );

            $order = Order::create([
                'platform' => 'MercadoLibre ', // Definir la plataforma
                'platform_order_id' => $pedido['id'],
                'status' => $pedido['status'],
                'customers_id' => $customer->id,
                'canal_id' => $canal->id, // Suponiendo que tienes disponible el ID del canal
            ]);

            // Almacenar los detalles del pedido
            foreach ($pedido['order_items'] as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['item']['id'],
                    'product_name' => $item['item']['title'],
                    'categori_id' => $item['item']['category_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['unit_price'] * $item['quantity'],
                ]);
            }
            foreach ($pedido['payments'] as $item) {
                OrderPayment::create([
                    'order_id' => $order->id,
                    'payment_method' => $item['payment_method_id'],
                    'payment_status' => $item['status'],
                    'payment_date' => $item['date_approved'],
                    'total_paid_amount' => $item['total_paid_amount'],
                    'currency' => $item['currency_id'],
                ]);
            }

            // Almacenar los datos de envío
            OrderShipping::create([
                'order_id' => $order->id,
                'shipping_method' => $envio['tracking_method'],
                'shipping_status' => $envio['status'],
                'shipping_date' => $envio['date_created'],
                'tracking_number' => $envio['tracking_number'],
                'shipping_address' => $envio['receiver_address']['address_line'].' - '.$envio['receiver_address']['neighborhood']['name'].' - '.$envio['receiver_address']['comment'],
                'shipping_city' => $envio['receiver_address']['city']['name'],
                'shipping_state' => $envio['receiver_address']['state']['name'],
                'shipping_postcode' => $envio['receiver_address']['zip_code'],
                'shipping_country' => $envio['receiver_address']['country']['name'],
            ]);

            // Almacenar los datos de facturación
            OrderBilling::create([
                'order_id' => $order->id,
                'billing_address' => $envio['receiver_address']['address_line'].' - '.$envio['receiver_address']['neighborhood']['name'].' - '.$envio['receiver_address']['comment'],
                'billing_city' => $envio['receiver_address']['city']['name'],
                'billing_state' => $envio['receiver_address']['state']['name'],
                'billing_postcode' => $envio['receiver_address']['zip_code'],
                'billing_country' => $envio['receiver_address']['country']['name'],
            ]);
        }
    }
    private function consultarComprador($id, $canal)
    {

        $accessToken = $canal->token;
        $client = new Client();

        $response = $client->get("https://api.mercadolibre.com/orders/{$id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $datosPedidos = json_decode($response->getBody(), true);



        return $datosPedidos;
    }
    private function consultarEnvio($id, $canal)
    {

        $accessToken = $canal->token;
        $client = new Client();

        $response = $client->get("https://api.mercadolibre.com/shipments/{$id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $datosPedidos = json_decode($response->getBody(), true);



        return $datosPedidos;
    }

    public function nuevoProductoMeli($canalId)
    {


        $canal = Canal::findOrFail($canalId);
        // Utiliza $canalId en tu lógica de controlador


        return view('canal.transacciones.nuevo-producto-mercadolibre',['canal' => $canal]);
    }

    public function predictCategory(Request $request)
    {
        $canal = Canal::findOrFail($request->query('canal_id'));
        // Obtener el token de acceso del canal o de donde lo tengas almacenado
        $accessToken = $canal->token;

        $siteId = 'MCO'; // Sitio de Colombia
        $productName = $request->query('q');
        $limit = 1;

        $client = new Client();
        $response = $client->request('GET', "https://api.mercadolibre.com/sites/$siteId/domain_discovery/search", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'query' => [
                'limit' => $limit,
                'q' => $productName
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        return response()->json($data);
    }
    public function getCategories(Request $request)
    {
        $canal = Canal::findOrFail($request->query('canal_id'));
        $this->verificarYActualizarToken($canal);
        // Obtener el token de acceso del canal o de donde lo tengas almacenado
        $accessToken = $canal->token;
        $siteId = 'MCO'; // Sitio de Colombia

        $client = new Client();
        $response = $client->request('GET', "https://api.mercadolibre.com/sites/$siteId/categories", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        return response()->json($data);
    }
    public function getSubcategorias(Request $request)
    {
        $categoriaId = $request->input('categoriaId');
        $canal = Canal::findOrFail($request->query('canal_id'));

        $accessToken = $canal->token;
        $siteId = 'MCO'; // Sitio de Colombia
        $client = new Client();
        $response = $client->request('GET', "https://api.mercadolibre.com/categories/$categoriaId", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);
        $data = json_decode($response->getBody(), true);
        $subcategorias = $data['children_categories'];
        return response()->json($subcategorias);
    }

    public function getCategoriasFinal(Request $request)
    {
        $subcategoriaId = $request->input('subcategoriaId');
        $canal = Canal::findOrFail($request->query('canal_id'));

        $accessToken = $canal->token;
        $siteId = 'MCO'; // Sitio de Colombia
        $client = new Client();
        $response = $client->request('GET', "https://api.mercadolibre.com/categories/$subcategoriaId", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);
        $data = json_decode($response->getBody(), true);
        $subcategorias = $data['children_categories'];
        return response()->json($subcategorias);
    }

}
