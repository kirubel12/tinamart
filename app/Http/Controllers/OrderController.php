<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy("id", "desc")->paginate(10);

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found.',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'data' => $orders
        ], 200);
    }
}
