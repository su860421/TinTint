<?php

namespace App\Services;

use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }

    /**
     * 檢查產品庫存
     */
    public function checkProductStock(string $productId, int $quantity): bool
    {
        /** @var Product|null $product */
        $product = $this->repository->find($productId);
        if (!$product) {
            return false;
        }

        return $product->hasEnoughStock($quantity);
    }

    /**
     * 更新產品庫存
     */
    public function updateProductStock(string $productId, int $quantity): bool
    {
        /** @var Product|null $product */
        $product = $this->repository->find($productId);
        if (!$product) {
            return false;
        }

        if ($quantity < 0) {
            return $product->decrementStock(abs($quantity));
        }

        $product->increment('stock', $quantity);
        return true;
    }

    /**
     * 獲取所有有庫存的商品
     */
    public function getAvailableProducts()
    {
        return $this->repository->getActiveProducts();
    }

    /**
     * 獲取商品及其庫存信息
     */
    public function getProductWithStock(string $productId)
    {
        return $this->repository->findWithStock($productId);
    }
}
