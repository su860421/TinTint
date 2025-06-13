<?php

namespace App\Contracts\Services;

use App\Enums\OrderStatusEnum;

interface OrderServiceInterface extends BaseServiceInterface
{
    public function getOrderWithDetails(int $id);
    public function createOrder(array $orderData);
    public function updateOrderStatus(int $id, OrderStatusEnum $status);
    public function getOrderStats(): array;
    
    /**
     * 驗證訂單項目
     *
     * @param array $items 訂單項目數組
     * @return bool
     * @throws \Exception 如果庫存不足
     */
    public function validateOrderItems(array $items): bool;
}
