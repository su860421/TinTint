<?php

namespace App\Contracts\Repositories;

use App\Enums\OrderStatusEnum;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get order with its details
     *
     * @param string $id
     * @return mixed
     */
    public function getWithDetails(string $id);

    /**
     * Create order with items
     *
     * @param array $orderData
     * @param array $items
     * @return mixed
     */
    public function createWithItems(array $orderData, array $items);

    /**
     * Update order status
     *
     * @param string $id
     * @param OrderStatusEnum $status
     * @return bool
     */
    public function updateStatus(string $id, OrderStatusEnum $status): bool;

    /**
     * Get total number of orders
     *
     * @return int
     */
    public function getTotalOrders(): int;

    /**
     * Get total amount of all orders
     *
     * @return float
     */
    public function getTotalAmount(): float;

    /**
     * Get today's order count
     *
     * @return int
     */
    public function getTodayOrders(): int;

    /**
     * Get today's total amount
     *
     * @return float
     */
    public function getTodayAmount(): float;
}
