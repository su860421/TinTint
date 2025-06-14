<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Enums\OrderStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public const ORDER_STATS_CACHE_KEY = 'order:stats';

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

    /**
     * Get order statistics excluding cancelled and refunded orders
     * 
     * @return array{
     *  total_orders: int,
     *  total_amount: float,
     *  today_orders: int,
     *  today_amount: float
     * }
     */
    public function getStats(): array
    {
        return Cache::remember(self::ORDER_STATS_CACHE_KEY, now()->endOfDay(), function (): array {
            $stats = $this->model
                ->select([
                    'status',
                    'created_at',
                    'total_amount'
                ])
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->get()
                ->groupBy(function ($order) {
                    return $order->created_at->isToday() ? 'today' : 'other';
                })
                ->pipe(function ($grouped) {
                    $today = $grouped->get('today', collect());
                    $all = $grouped->flatten();
                    
                    return [
                        'total_orders' => $all->count(),
                        'total_amount' => (float) $all->sum('total_amount'),
                        'today_orders' => $today->count(),
                        'today_amount' => (float) $today->sum('total_amount')
                    ];
                });

            return $stats;
        });
    }
}
