<?php

namespace App\Services;

use App\Repositories\CrudRepositoryInterface;
use App\Repositories\OrderRepository;

class OrderService
{
    protected $orderRepository;

    public function __construct(CrudRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders()
    {
        return $this->orderRepository->all();
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->find($id);
    }

    public function createOrder(array $attributes)
    {
        return $this->orderRepository->create($attributes);
    }

    public function updateOrder($id, array $attributes)
    {
        return $this->orderRepository->update($id, $attributes);
    }

    public function deleteOrder($id)
    {
        return $this->orderRepository->delete($id);
    }
}
