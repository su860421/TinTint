<?php

namespace App\Contracts\Repositories;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function updateStock(string $id, int $quantity);
    public function checkStock(string $id, int $quantity): bool;
    public function getActiveProducts();
    public function findWithStock(string $id);
}
