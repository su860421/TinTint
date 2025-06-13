<?php

namespace App\Services;

use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function checkProductStock(int $productId, int $quantity): bool
    {
        return $this->repository->checkStock($productId, $quantity);
    }

    /**
     * {@inheritDoc}
     */
    public function updateProductStock(int $productId, int $quantity): bool
    {
        return $this->repository->updateStock($productId, $quantity);
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableProducts()
    {
        return $this->repository->getActiveProducts();
    }

    /**
     * {@inheritDoc}
     */
    public function getProductWithStock(int $productId)
    {
        return $this->repository->findWithStock($productId);
    }
}
