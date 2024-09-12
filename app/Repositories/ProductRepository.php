<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\CrudRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements CrudRepositoryInterface
{
    protected Product $model;
    protected  $userId;

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->userId = Auth::id();
    }


    public function all()
    {
        return Cache::remember("user:{$this->userId }:product:*",  1, function () {
            return  $this->model::all();
        });
    }

    public function find($id)
    {
        return Cache::remember("user:{$this->userId }:product:{$id}", 1, function() use ($id) {
            return $this->model->findOrFail($id);
        });

    }

    public function create(array $data)
    {
        $product = $this->model->create($data);

        $key = "user:{$this->userId}:product:{$product->id}";
        Cache::remember($key, 1, function () use ($product) {
            return $product;
        });
        return $product;
    }

    public function update($id, array $data)
    {
        $product = Product::find($id);
        $product->update($data);

        $key = "user:{$this->userId }:product:{$id}";
        Cache::forget($key);
        Cache::remember($key, 1, function () use ($product) {
            return $product;
        });
        return $product;

    }

    public function delete($id)
    {
        $this->model->destroy($id);
        Cache::forget("user:{$this->userId }:product:{$id}");
    }
}
