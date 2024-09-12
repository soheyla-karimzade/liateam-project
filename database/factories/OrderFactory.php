<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{


    public static function getRandom($limit = 1)
    {

        $result= Product::raw(function($collection) use ($limit) {
            return $collection->aggregate([
                ['$sample' => ['size' => $limit]]
            ])->toArray();
        });
//dd($result);
        return collect($result);

    }


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $productCount = rand(1, 5);

        $products = Product::all()->toArray();

        shuffle($products);

        $selectedProducts = array_slice($products, 0, $productCount);

        $orderProducts = array_map(function ($product) {
            return [
                'product_id' => $product['_id'],
                'quantity' => rand(1, 5)
            ];
        }, $selectedProducts);

        $totalPrice = array_reduce($orderProducts, function ($carry, $product) {
            $productData = Product::find($product['product_id']);
            return $carry + ($product['quantity'] * $productData->price);
        }, 0);

        return [
            'products' => $orderProducts, // آرایه محصولات
            'count' => array_sum(array_column($orderProducts, 'quantity')), // مجموع تعداد محصولات
            'total_price' => $totalPrice, // مجموع قیمت کل
            'user_id'=>User::factory()->create()

        ];
    }
}
