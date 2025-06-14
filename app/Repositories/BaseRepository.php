<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;
use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function index(
        int $perPage = 0,
        ?string $orderBy = null,
        string $orderDirection = 'asc',
        array $relationships = [],
        array $columns = ['*'],
        array $filters = []
    ) {
        $query = $this->model->select($columns);

        if (count($relationships) > 0) {
            $query = $this->loadModelRelationships($query, $relationships);
        }

        $this->applyFilters($query, $filters);

        if ($orderBy !== null) {
            $this->applySorting($query, $orderBy, $orderDirection);
        }

        if ($perPage > 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    protected function loadModelRelationships($query, array $relationships)
    {
        [$countRelations, $regularRelations] = $this->separateRelationships($relationships);

        foreach ($countRelations as $relation) {
            $query->withCount($relation);
        }

        $query->with($regularRelations);

        return $query;
    }

    protected function separateRelationships(array $relationships)
    {
        $countRelations = [];
        $regularRelations = [];

        foreach ($relationships as $key => $relationship) {
            if (Str::endsWith($relationship, '.count')) {
                $countRelations[] = Str::before($relationship, '.count');
                continue;
            }
            $regularRelations[] = $relationship;
        }

        return [$countRelations, $regularRelations];
    }

    protected function applyFilters($query, $filters)
    {
        foreach ($filters as $filter) {
            if (!is_array($filter)) {
                $filter = json_decode($filter, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new RepositoryException(__('Invalid JSON format in filter. ').json_last_error_msg(), 400);
                }
            }

            if (count($filter) === 2) {
                [$field, $value] = $filter;
                $query->where($field, '=', $value);
            } elseif (count($filter) === 3) {
                [$field, $operator, $value] = $filter;
                $query->where($field, $operator, $value);
                if (($field == 'end_at' || $field == 'expired_at') && $operator == '>=') {
                    $query->orWhere($field, '=', null);
                }
            } else {
                throw new RepositoryException(__('Invalid filter format.'), 400);
            }
        }
    }

    protected function applySorting($query, $orderBy, $orderDirection)
    {
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            throw new RepositoryException(__('Invalid order direction. Use asc or desc.'), 400);
        }

        $validColumns = $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());

        if (!in_array($orderBy, $validColumns)) {
            throw new RepositoryException(__('Invalid column for sorting.'), 400);
        }

        $query->orderBy($orderBy, $orderDirection);
    }

    public function find(string $id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function create(array $attributes)
    {
        try {
            return $this->model->create($attributes);
        } catch (Exception $e) {
            throw new RepositoryException(__('An error occurred while creating the model. ').$e->getMessage(), 500);
        }
    }

    public function update(string $id, array $attributes)
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);

        return $model;
    }

    public function delete(string $id)
    {
        $model = $this->model->findOrFail($id);
        return $model->delete();
    }
}
