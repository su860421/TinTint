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

    public function getWithDetails(int $id)
    {
        return $this->model->with(['user', 'orderItems.product'])->find($id);
    }

    public function getWithPaginate(int $perPage = 20)
    {
        return $this->model->with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateStatus(int $id, OrderStatusEnum $status)
    {
        $order = $this->find($id);
        if (!$order) {
            return false;
        }

        return $order->update(['status' => $status]);
    }

    public function getTotalOrders()
    {
        return $this->model->count();
    }

    public function getTotalAmount()
    {
        return $this->model->sum('total_amount');
    }

    public function getTodayOrders()
    {
        return $this->model->whereDate('created_at', today())->count();
    }

    public function getTodayAmount()
    {
        return $this->model->whereDate('created_at', today())->sum('total_amount');
    }

    public function createWithItems(array $orderData, array $items)
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = $this->create($orderData);
            
            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }
            
            return $order->load(['orderItems.product', 'user']);
        });
    }
}
