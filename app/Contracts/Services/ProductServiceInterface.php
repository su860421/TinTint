<?php

namespace App\Contracts\Services;

interface ProductServiceInterface extends BaseServiceInterface
{
    /**
     * 檢查產品庫存
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function checkProductStock(int $productId, int $quantity): bool;

    /**
     * 更新產品庫存
     *
     * @param int $productId
     * @param int $quantity 可以是負數（減少庫存）或正數（增加庫存）
     * @return bool
     */
    public function updateProductStock(int $productId, int $quantity): bool;

    /**
     * 獲取所有有庫存的商品
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableProducts();

    /**
     * 獲取商品及其庫存信息
     *
     * @param int $productId
     * @return mixed
     */
    public function getProductWithStock(int $productId);
}
