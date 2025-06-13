<?php

namespace App\Contracts\Repositories;

use App\Enums\OrderStatusEnum;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithDetails(int $id);
    public function getWithPaginate(int $perPage = 20);
    public function updateStatus(int $id, OrderStatusEnum $status);
    public function getTotalOrders();
    public function getTotalAmount();
    public function getTodayOrders();
    public function getTodayAmount();
    public function createWithItems(array $orderData, array $items);
}
