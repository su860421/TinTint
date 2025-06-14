<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\BaseServiceInterface;
use App\Contracts\Repositories\BaseRepositoryInterface;

class BaseService implements BaseServiceInterface
{
    protected $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(
        int $perPage = 0,
        ?string $orderBy = null,
        string $orderDirection = 'asc',
        array $relationships = [],
        array $columns = ['*'],
        array $filters = []
    ) {
        return $this->repository->index(
            $perPage,
            $orderBy,
            $orderDirection,
            $relationships,
            $columns,
            $filters
        );
    }

    public function find(string $id, array $columns = ['*'])
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id)
    {
        return $this->repository->delete($id);
    }
}
