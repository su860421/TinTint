<?php

namespace App\Services;

use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Enums\OrderStatusEnum;
use Exception;

class OrderService extends BaseService implements OrderServiceInterface
{
    public function __construct(
        OrderRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }

    /**
     * Get order with details
     *
     * @param string $orderId
     * @return mixed
     */
    public function getOrderWithDetails(string $orderId)
    {
        return $this->repository->getWithDetails($orderId);
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function createOrder(array $data)
    {
        if (empty($data['items'])) {
            throw new Exception('Order items are required');
        }

        $orderData = [
            'user_id' => $data['user_id'],
            'status' => OrderStatusEnum::PENDING,
            'total_amount' => 0,
        ];

        return $this->repository->createWithItems($orderData, $data['items']);
    }

    /**
     * Update order status
     *
     * @param string $orderId
     * @param OrderStatusEnum $status
     * @return bool
     */
    public function updateOrderStatus(string $orderId, OrderStatusEnum $status): bool
    {
        return $this->repository->updateStatus($orderId, $status);
    }

    /**
     * Get order statistics
     *
     * @return array
     */
    public function getOrderStats(): array
    {
        return [
            'total_orders' => $this->repository->getTotalOrders(),
            'total_amount' => $this->repository->getTotalAmount(),
            'today_orders' => $this->repository->getTodayOrders(),
            'today_amount' => $this->repository->getTodayAmount(),
        ];
    }
}
