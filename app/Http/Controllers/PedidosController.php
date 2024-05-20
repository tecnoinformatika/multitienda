<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PedidosController extends Controller
{
    public function verpedidos()
    {
        $pedidos = Order::get();

        return view('canal.transacciones.ordenes');
    }
}
