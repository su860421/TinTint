<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findWithStock(string $id);
    
    public function getActiveProducts();

    public function updateStock(string $id, int $quantity): bool;
}
