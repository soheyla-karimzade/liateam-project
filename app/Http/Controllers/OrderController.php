<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return response()->json($orders);
    }

    public function show($id){
        $order = $this->orderService->getOrderById($id);
        return response()->json($order,200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
            'count' => 'required|integer'
        ]);

        try {
            $order = $this->orderService->createOrder($validatedData);
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
            'count' => 'required|integer'
        ]);

        $order = $this->orderService->updateOrder($id, $validatedData);
        return response()->json($order,200);
    }

    public function destroy($id)
    {
        $this->orderService->deleteOrder($id);
        return response()->json(['message' => 'Order deleted successfully'],204);
    }
}
