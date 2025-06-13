<?php

namespace App\Contracts\Repositories;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function updateStock(int $id, int $quantity);
    public function checkStock(int $id, int $quantity): bool;
    public function getActiveProducts();
    public function findWithStock(int $id);
}
