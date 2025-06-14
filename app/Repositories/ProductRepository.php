<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findWithStock(string $id)
    {
        return $this->find($id, ['*']);
    }

    public function getActiveProducts()
    {
        return $this->index(
            perPage: 0,
            filters: [['stock', '>', 0]]
        );
    }

    public function updateStock(string $id, int $quantity): bool
    {
        return DB::transaction(function () use ($id, $quantity) {
            $product = $this->find($id);
            
            if ($quantity < 0) {
                if ($product->stock < abs($quantity)) {
                    return false;
                }
                return $product->decrement('stock', abs($quantity));
            }
            
            return $product->increment('stock', $quantity);
        });
    }
}
