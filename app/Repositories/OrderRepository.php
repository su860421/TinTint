<?php

namespace App\Repositories;

use App\Models\Order;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getWithDetails(string $id)
    {
        return $this->model
            ->with(['user', 'orderItems.product'])
            ->find($id);
    }

    public function createWithItems(array $orderData, array $items)
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = $this->create($orderData);
            $order->orderItems()->createMany($items);
            return $this->getWithDetails($order->id);
        });
    }

    public function updateStatus(string $id, OrderStatusEnum $status): bool
    {
        return (bool) $this->model->where('id', $id)
            ->update(['status' => $status]);
    }

    public function getTotalOrders(): int
    {
        return $this->model->count();
    }

    public function getTotalAmount(): float
    {
        return (float) $this->model->sum('total_amount');
    }

    public function getTodayOrders(): int
    {
        return $this->model->whereDate('created_at', today())->count();
    }

    public function getTodayAmount(): float
    {
        return (float) $this->model->whereDate('created_at', today())
            ->sum('total_amount');
    }
}
