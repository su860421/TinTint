<?php

namespace App\Contracts\Services;

interface BaseServiceInterface
{
    public function getAll();
    public function getById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function getPaginated(int $perPage = 20);
}
