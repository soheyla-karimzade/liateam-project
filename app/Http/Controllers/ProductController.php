<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        return response()->json($products);
    }

    public function show($id){
        $order = $this->productService->getProductById($id);
        return response()->json($order,200);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'inventory' => 'required|integer'
        ]);
        $product = $this->productService->createProduct($validatedData);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'inventory' => 'required|integer'
        ]);
        $product = $this->productService->updateProduct($id, $validatedData);
        return response()->json($product,200);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully'],204);
    }
}
