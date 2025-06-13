<?php

namespace App\Services;

use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService implements OrderServiceInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $repository;

    /**
     * @var ProductServiceInterface
     */
    protected $productService;

    public function __construct(
        OrderRepositoryInterface $repository,
        ProductServiceInterface $productService
    ) {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->productService = $productService;
    }

    public function getOrderWithDetails(int $id)
    {
        return $this->repository->getWithDetails($id);
    }

    public function createOrder(array $orderData)
    {
        return DB::transaction(function () use ($orderData) {
            // 驗證庫存
            $this->validateOrderItems($orderData['items']);
            
            $totalAmount = 0;
            $items = [];

            // 處理訂單項目
            foreach ($orderData['items'] as $item) {
                $product = $this->productService->getProductWithStock($item['product_id']);
                
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal
                ];

                // 更新庫存
                $this->productService->updateProductStock($product->id, -$item['quantity']);
            }

            // 創建訂單
            return $this->repository->createWithItems([
                'user_id' => $orderData['user_id'],
                'total_amount' => $totalAmount,
                'status' => OrderStatusEnum::PENDING
            ], $items);
        });
    }

    public function updateOrderStatus(int $id, OrderStatusEnum $status)
    {
        return $this->repository->updateStatus($id, $status);
    }

    public function getOrderStats(): array
    {
        return [
            'total_orders' => $this->repository->getTotalOrders(),
            'total_amount' => $this->repository->getTotalAmount(),
            'today_orders' => $this->repository->getTodayOrders(),
            'today_amount' => $this->repository->getTodayAmount()
        ];
    }

    public function validateOrderItems(array $items): bool
    {
        foreach ($items as $item) {
            if (!$this->productService->checkProductStock($item['product_id'], $item['quantity'])) {
                throw new \Exception("商品庫存不足");
            }
        }
        return true;
    }
}
