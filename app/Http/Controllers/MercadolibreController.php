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
        $accessTokenExpiresAt = now()->addSeconds($expiresIn);
        if ($accessTokenExpiresAt->lte(now()->addHour())) {
            $newAccessToken = $this->refreshAccessToken($canal);
            $canal->token = $newAccessToken;
            $canal->refresh_token = $data['refresh_token'];
            $canal->expires_in = $data['expires_in'];
            $canal->updated_at = now();
            $canal->save();
            $canal->save();
        }
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
}
