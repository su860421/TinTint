<?php

namespace App\Services;

use App\Contracts\Services\BaseServiceInterface;
use App\Contracts\Repositories\BaseRepositoryInterface;

class BaseService implements BaseServiceInterface
{
    /**
     * @var BaseRepositoryInterface
     */
    protected $repository;

    /**
     * BaseService constructor.
     *
     * @param BaseRepositoryInterface $repository
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->repository->all();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getById(string $id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * @param string $id
     * @param array $data
     * @return mixed
     */
    public function update(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function delete(string $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * @param int $perPage
     * @return mixed
     */
    public function getPaginated(int $perPage = 20)
    {
        return $this->repository->paginate($perPage);
    }
}
