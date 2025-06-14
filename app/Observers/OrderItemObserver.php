<?php

namespace App\Observers;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Repositories\OrderRepository;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        DB::transaction(function () use ($orderItem) {
            $orderItem->order->increment('total_amount', $orderItem->subtotal);
            Cache::forget(OrderRepository::ORDER_STATS_CACHE_KEY);
        });
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        if ($orderItem->isDirty('subtotal')) {
            DB::transaction(function () use ($orderItem) {
                $diff = $orderItem->subtotal - $orderItem->getOriginal('subtotal');
                $orderItem->order->increment('total_amount', $diff);
                Cache::forget(OrderRepository::ORDER_STATS_CACHE_KEY);
            });
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        DB::transaction(function () use ($orderItem) {
            $orderItem->order->decrement('total_amount', $orderItem->subtotal);
            Cache::forget(OrderRepository::ORDER_STATS_CACHE_KEY);
        });
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
