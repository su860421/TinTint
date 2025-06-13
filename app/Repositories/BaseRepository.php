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
     * @param string $id
     * @return Model|null
     */
    public function find(string $id)
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
     * @param string $id
     * @param array $attributes
     * @return bool
     */
    public function update(string $id, array $attributes)
    {
        $instance = $this->find($id);
        if (!$instance) {
            return false;
        }
        return $instance->update($attributes);
    }

    /**
     * @param string $id
     * @return bool|null
     */
    public function delete(string $id)
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
