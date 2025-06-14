<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Enums\OrderStatusEnum;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithDetails(string $id);

    public function createWithItems(array $orderData, array $items);

    public function updateStatus(string $id, OrderStatusEnum $status): bool;

    public function getTotalOrders(): int;

    public function getTotalAmount(): float;

    public function getTodayOrders(): int;

    public function getTodayAmount(): float;
}
