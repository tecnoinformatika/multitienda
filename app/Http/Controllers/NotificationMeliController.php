<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canal;
use GuzzleHttp\Client;
use App\Models\UsuarioMercadoLibre;
use App\Models\NotificationMeli;

class NotificationMeliController extends Controller
{
    public function handleMercadoLibreNotification(Request $request)
    {
        // Procesar la notificación recibida de MercadoLibre
        $notificationData = $request->all();
        NotificationMeli::create([
            'notification_id' => $notificationData['_id'],
            'resource' => $notificationData['resource'],
            'user_id' => $notificationData['user_id'],
            'topic' => $notificationData['topic'],
            'application_id' => $notificationData['application_id'],
            'attempts' => $notificationData['attempts'],
        ]);
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
            

    }

    // Método para obtener información de la pregunta
    private function getQuestionInfo($questionId)
    {
        // Realizar una solicitud GET a la API de MercadoLibre para obtener información de la pregunta
        // Implementa esta lógica según la estructura de la API de MercadoLibre y tus necesidades específicas
    }
}
