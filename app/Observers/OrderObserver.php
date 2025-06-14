<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use App\Enums\OrderStatusEnum;
use App\Repositories\OrderRepository;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        Cache::forget(OrderRepository::ORDER_STATS_CACHE_KEY);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status') || $order->isDirty('total_amount')) {
            Cache::forget(OrderRepository::ORDER_STATS_CACHE_KEY);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        Cache::forget(OrderRepository::ORDER_STATS_CACHE_KEY);
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
