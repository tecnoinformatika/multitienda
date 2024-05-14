<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canal;
use GuzzleHttp\Client;


class MercadolibreController extends Controller
{

    // Ver los productos del usuario en MercadoLibre
    public function VerMercadolibre($id)
    {
        $canal = Canal::findOrFail($id);
      

         // Verificar y actualizar el token de acceso si es necesario
         $this->verificarYActualizarToken($canal);
 
         // Obtener todos los productos del usuario en MercadoLibre
         $productos = $this->obtenerProductos($canal->token);        

         dd($productos);


        // Devuelve los productos a la vista
        return view('canal.mercadolibre', ['canal' => $canal, 'productos' => $productos]);
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
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $response = $client->get('https://api.mercadolibre.com/users/me/items/search');
        $productos = json_decode((string) $response->getBody(), true);

        return $productos;
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
                    'site_id' => 'MLC',
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

    public function handleMercadoLibreNotification(Request $request)
    {
        // Procesar la notificación recibida de MercadoLibre
        $notificationData = $request->all();

        // Determinar el tipo de notificación y tomar acciones en consecuencia
        switch ($notificationData['topic']) {
            case 'items':
                // Procesar notificación de items
                $itemId = $this->extractIdFromResource($notificationData['resource']);
                $itemInfo = $this->getItemInfo($itemId);
                // Realizar acciones con la información del item
                break;
            case 'orders_v2':
                // Procesar notificación de órdenes
                $orderId = $this->extractIdFromResource($notificationData['resource']);
                $orderInfo = $this->getOrderInfo($orderId);
                // Realizar acciones con la información de la orden
                break;
            case 'questions':
                // Procesar notificación de preguntas
                $questionId = $this->extractIdFromResource($notificationData['resource']);
                $questionInfo = $this->getQuestionInfo($questionId);
                // Realizar acciones con la información de la pregunta
                break;
            // Agregar casos para otros tipos de notificaciones según sea necesario
            default:
                // No hacer nada o manejar otros tipos de notificaciones según sea necesario
                break;
        }

        // Responder a MercadoLibre con un código de estado HTTP 200 para indicar que la notificación fue recibida correctamente
        return response()->json(['message' => 'Notification received'], 200);
    }

    // Método para extraer el ID del recurso de la URL
    private function extractIdFromResource($resourceUrl)
    {
        $parts = explode('/', $resourceUrl);
        return end($parts);
    }

    // Método para obtener información del item
    private function getItemInfo($itemId)
    {
        // Realizar una solicitud GET a la API de MercadoLibre para obtener información del item
        // Implementa esta lógica según la estructura de la API de MercadoLibre y tus necesidades específicas
    }

    // Método para obtener información de la orden
    private function getOrderInfo($orderId)
    {
        // Realizar una solicitud GET a la API de MercadoLibre para obtener información de la orden
        // Implementa esta lógica según la estructura de la API de MercadoLibre y tus necesidades específicas
    }

    // Método para obtener información de la pregunta
    private function getQuestionInfo($questionId)
    {
        // Realizar una solicitud GET a la API de MercadoLibre para obtener información de la pregunta
        // Implementa esta lógica según la estructura de la API de MercadoLibre y tus necesidades específicas
    }
}
