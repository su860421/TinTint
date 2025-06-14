<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Enums\OrderStatusEnum;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get order statistics
     *
     * @return array
     */
    public function getStats(): array;

    public function getWithDetails(string $id);

    public function createWithItems(array $orderData, array $items);

    public function updateStatus(string $id, OrderStatusEnum $status): bool;

}
