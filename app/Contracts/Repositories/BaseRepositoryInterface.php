<?php

namespace App\Contracts\Repositories;

interface BaseRepositoryInterface
{
    public function all();
    public function find(string $id);
    public function create(array $attributes);
    public function update(string $id, array $attributes);
    public function delete(string $id);
    public function paginate(int $perPage = 20);
}
