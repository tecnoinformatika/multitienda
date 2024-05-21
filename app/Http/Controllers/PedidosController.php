<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\OrderShipping;
use App\Models\OrderBilling;
Use App\Models\Customer;
use App\Models\Canal;
use App\Models\CanalDisponible;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class PedidosController extends Controller
{
    public function verpedidos()
    {


        return view('canal.transacciones.ordenes');
    }
    public function listarTodoslosPedidos()
    {
        // Obtener el usuario logueado
        $user = Auth::user();

        // Obtener las órdenes del usuario logueado con sus relaciones
        $orders = Order::with(['customer', 'details', 'payment', 'shipping', 'billing', 'canal'])
                       ->whereHas('canal', function($query) use ($user) {
                           $query->where('user_id', $user->id);
                       })
                       ->get();

        return response()->json($orders);
    }
}
