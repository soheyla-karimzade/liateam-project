<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderRepository implements CrudRepositoryInterface
{

    protected Order $model;
    protected $userId;

    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->userId = Auth::id();
    }

    public function all()
    {
        return Cache::remember("user:{$this->userId }:order:*", 1, function () {
            return $this->model::all();
        });
    }

    public function find($id)
    {
        return Cache::remember("user:{$this->userId }:order:{$id}", 1, function () use ($id) {
            return $this->model->findOrFail($id);
        });
    }


    public function create(array $data)
    {
        foreach ($data['products'] as $product) {
            $productModel = Product::find($product['id']);
            if ($productModel->inventory < $product['quantity']) {
                throw new \Exception('Not enough inventory for product ' . $product['id']);
            }
            $productModel->inventory -= $product['quantity'];
            $productModel->save();
        }

        $products = collect($data['products'])->map(function ($product) {
            return [
                'id' => $product['id'],
                'quantity' => $product['quantity']
            ];
        });

        $order = new $this->model();
        $order->products = $products->toArray();
        $order->total_price = $data['total_price'];
        $order->count = $data['count'];
        $order->user_id = $this->userId;
        $order->save();

        $key = "user:{$this->userId}:order:{$order->id}";
        Cache::remember($key, 1, function () use ($order) {
            return $order;
        });

        return $order;
    }

    public function update($id, array $data)
    {

        $order = $this->model->findOrFail($id);
        $currentProducts = collect($order->products);

        foreach ($data['products'] as $newProduct) {
            $productModel = Product::find($newProduct['id']);
            if (!$productModel) {
                throw new \Exception('Product not found: ' . $newProduct['id']);
            }

            $currentProduct = $currentProducts->firstWhere('product_id', $newProduct['id']);
            if ($currentProduct) {
                $previousQuantity = $currentProduct['quantity'];
                $newQuantity = $newProduct['quantity'];

                if ($newQuantity > $previousQuantity) {
                    $difference = $newQuantity - $previousQuantity;
                    if ($productModel->inventory < $difference) {
                        throw new \Exception('Not enough inventory for product ' . $newProduct['id']);
                    }
                    $productModel->inventory -= $difference;
                } elseif ($newQuantity < $previousQuantity) {
                    // اضافه کردن تفاوت به موجودی محصول در صورت کاهش تعداد
                    $difference = $previousQuantity - $newQuantity;
                    $productModel->inventory += $difference;
                }
            }
            else {
                if ($productModel->inventory < $newProduct['quantity']) {
                    throw new \Exception('Not enough inventory for product ' . $newProduct['id']);
                }
                $productModel->inventory -= $newProduct['quantity'];
            }
            $productModel->save();
        }

        $order->products = $data['products'];
        $order->total_price = $data['total_price'];
        $order->count = $data['count'];
        $order->save();


        $key = "user:{$this->userId }:order:{$id}";
        Cache::forget($key);
        Cache::remember($key, 1, function () use ($order) {
            return $order;
        });

        return $order;

    }

    public function delete($id)
    {
//        Order::destroy($id);
//        Cache::forget('orders_all');

        $this->model->destroy($id);
        Cache::forget("user:{$this->userId }:order:{$id}");
    }
}
