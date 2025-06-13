<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes)
    {
        $instance = $this->find($id);
        if (!$instance) {
            return false;
        }
        return $instance->update($attributes);
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id)
    {
        $instance = $this->find($id);
        if (!$instance) {
            return false;
        }
        return $instance->delete();
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 20)
    {
        return $this->model->paginate($perPage);
    }
}
