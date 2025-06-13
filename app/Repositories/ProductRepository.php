<?php

namespace App\Repositories;

use App\Models\Product;
use App\Contracts\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function updateStock(string $id, int $quantity)
    {
        return DB::transaction(function () use ($id, $quantity) {
            $product = $this->findWithStock($id);
            if (!$product) {
                return false;
            }

            $newStock = $product->stock + $quantity;
            if ($newStock < 0) {
                return false;
            }

            return $product->update(['stock' => $newStock]);
        });
    }

    public function checkStock(string $id, int $quantity): bool
    {
        $product = $this->findWithStock($id);
        if (!$product) {
            return false;
        }

        return $product->stock >= $quantity;
    }

    public function getActiveProducts()
    {
        return $this->model->where('stock', '>', 0)->get();
    }

    public function findWithStock(string $id)
    {
        return $this->model->select('id', 'name', 'price', 'stock')->find($id);
    }
}
