<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canal;
use GuzzleHttp\Client;
use App\Models\UsuarioMercadoLibre;
use App\Models\NotificationMeli;

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
    public function mostrarPedidos($canalid)
    {
        $canal = Canal::findOrFail($canalid);
        // Obtener el token de acceso del canal o de donde lo tengas almacenado
        $accessToken = $canal->token;

        // Obtener el ID del vendedor (seller) asociado al usuario autenticado
        $sellerId = Auth::user()->seller_id; // Suponiendo que tengas el ID del vendedor almacenado en la tabla de usuarios

        // Obtener los datos de los pedidos
        $datosPedidos = $this->obtenerPedidos($accessToken, $sellerId);
php
// Assuming you have a model called Order that has the necessary fields

$orders = json_decode($response->getBody(), true);

foreach ($orders['results'] as $orderData) {
    $order = new Order();

    $order->id = $orderData['id'];
    $order->status = $orderData['status'];
    $order->status_detail = $orderData['status_detail'];
    $order->date_created = $orderData['date_created'];
    $order->date_closed = $orderData['date_closed'];
    $order->expiration_date = $orderData['expiration_date'];
    $order->date_last_updated = $orderData['date_last_updated'];
    $order->hidden_for_seller = $orderData['hidden_for_seller'];
    $order->currency_id = $orderData['currency_id'];

    // Process order items
    foreach ($orderData['order_items'] as $item) {
        $orderItem = new OrderItem();

        $orderItem->currency_id = $item['currency_id'];
        $orderItem->item_id = $item['item']['id'];
        $orderItem->item_title = $item['item']['title'];
        $orderItem->sale_fee = $item['sale_fee'];
        $orderItem->quantity = $item['quantity'];
        $orderItem->unit_price = $item['unit_price'];

        $order->orderItems()->save($orderItem);
    }

    // Process payments
    foreach ($orderData['payments'] as $payment) {
        $orderPayment = new OrderPayment();

        $orderPayment->id = $payment['id'];
        $orderPayment->order_id = $payment['order_id'];
        $orderPayment->payer_id = $payment['payer_id'];
        $orderPayment->collector_id = $payment['collector']['id'];
        $orderPayment->currency_id = $payment['currency_id'];
        $orderPayment->status = $payment['status'];
        $orderPayment->status_code = $payment['status_code'];
        $orderPayment->status_detail = $payment['status_detail'];
        $orderPayment->transaction_amount = $payment['transaction_amount'];
        $orderPayment->shipping_cost = $payment['shipping_cost'];
        $orderPayment->overpaid_amount = $payment['overpaid_amount'];
        $orderPayment->total_paid_amount = $payment['total_paid_amount'];
        $orderPayment->marketplace_fee = $payment['marketplace_fee'];
        $orderPayment->coupon_amount = $payment['coupon_amount'];
        $orderPayment->date_created = $payment['date_created'];
        $orderPayment->date_last_modified = $payment['date_last_modified'];
        $orderPayment->card_id = $payment['card_id'];
        $orderPayment->reason = $payment['reason'];
        $orderPayment->activation_uri = $payment['activation_uri'];
        $orderPayment->payment_method_id = $payment['payment_method_id'];
        $orderPayment->installments = $payment['installments'];
        $orderPayment->issuer_id = $payment['issuer_id'];
        $orderPayment->coupon_id = $payment['coupon_id'];
        $orderPayment->operation_type = $payment['operation_type'];
        $orderPayment->payment_type = $payment['payment_type'];
        $orderPayment->installment_amount = $payment['installment_amount'];
        $orderPayment->deferred_period = $payment['deferred_period'];
        $orderPayment->date_approved = $payment['date_approved'];
        $orderPayment->authorization_code = $payment['authorization_code'];
        $orderPayment->transaction_order_id = $payment['transaction_order_id'];

        $order->orderPayments()->save($orderPayment);
    

        // Process shipping information
        $order->shipping_id = $orderData['shipping']['id'];
        $order->shipping_service_id = $orderData['shipping']['service_id'];
        $order->shipping_currency_id = $orderData['shipping']['currency_id'];
        $order->shipping_mode = $orderData['shipping']['shipping_mode'];
        $order->shipping_type = $orderData['shipping']['shipment_type'];
        $order->shipping_sender_id = $orderData['shipping']['sender_id'];
        $order->shipping_receiver_id = $orderData['shipping']['receiver_id'];
        $order->shipping_cost = $orderData['shipping']['cost'];
        $order->shipping_status = $orderData['shipping']['status'];
        $order->shipping_date_created = $orderData['shipping']['date_created'];
        $order->shipping_last_updated = $orderData['shipping']['last_updated'];
        $order->shipping_estimated_delivery_time = $orderData['shipping']['estimated_delivery_time'];
        $order->shipping_tracking_number = $orderData['shipping']['tracking_number'];
        $order->shipping_free_method = $orderData['shipping']['free_method'];
        $order->shipping_free_shipping = $orderData['shipping']['free_shipping'];
        $order->shipping_logistic_type = $orderData['shipping']['logistic_type'];
        $order->shipping_store_pick_up = $orderData['shipping']['store_pick_up'];

        // Save the order
        $order->save();
    

        // Pasar los datos de los pedidos a la vista
        return view('pedidos.index', compact('datosPedidos'));
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
}
