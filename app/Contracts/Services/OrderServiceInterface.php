<?php

namespace App\Contracts\Services;

use App\Enums\OrderStatusEnum;

interface OrderServiceInterface extends BaseServiceInterface
{
    /**
     * Get order with details
     *
     * @param string $orderId
     * @return mixed
     */
    public function getOrderWithDetails(string $orderId);

    /**
     * Create a new order
     *
     * @param array $data
     * @return mixed
     */
    public function createOrder(array $data);

    /**
     * Update order status
     *
     * @param string $orderId
     * @param OrderStatusEnum $status
     * @return bool
     */
    public function updateOrderStatus(string $orderId, OrderStatusEnum $status): bool;

    /**
     * Get order statistics
     *
     * @return array
     */
    public function getOrderStats(): array;
}
