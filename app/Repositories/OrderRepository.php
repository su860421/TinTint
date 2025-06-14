<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Enums\OrderStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getWithDetails(string $id)
    {
        return $this->find($id, ['*']);
    }

    public function createWithItems(array $orderData, array $items)
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = $this->create($orderData);
            $totalAmount = 0;

            foreach ($items as $item) {
                $orderItem = $order->orderItems()->create($item);
                $totalAmount += $orderItem->subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);
            return $order->fresh(['orderItems', 'orderItems.product']);
        });
    }

    public function updateStatus(string $id, OrderStatusEnum $status): bool
    {
        $order = $this->find($id);
        return $order->update(['status' => $status->value]);
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
        return $this->model->whereDate('created_at', Carbon::today())->count();
    }

    public function getTodayAmount(): float
    {
        return (float) $this->model->whereDate('created_at', Carbon::today())->sum('total_amount');
    }
}
