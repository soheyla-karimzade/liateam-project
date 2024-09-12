<?php

namespace App\Services;

use App\Repositories\CrudRepositoryInterface;
use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;

    public function __construct(CrudRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $attributes)
    {
        return $this->productRepository->create($attributes);
    }

    public function updateProduct($id, array $attributes)
    {
        return $this->productRepository->update($id, $attributes);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }
}
