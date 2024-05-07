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
        // Aquí puedes manejar la lógica después de que el usuario haya confirmado la autorización
        // Por ejemplo, podrías guardar las claves API de WooCommerce en la base de datos

        // Redireccionar a alguna página de confirmación o a donde desees
        return redirect()->route('/')->with('success', 'WooCommerce ha sido vinculado exitosamente.');
    }

    public function add($any, Request $request)
    {
        $data = $request->all();

        $store = Canal::where(['id' => $any])->first();
        if ($store) {
            
            $store->apikey = $request->consumer_key;
            $store->secret = $request->consumer_secret;
            $store->save();
        }
    
        return ['s' => 200];

        
    }
}
