<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface BaseServiceInterface
{
    public function index(
        int $perPage = 0,
        ?string $orderBy = null,
        string $orderDirection = 'asc',
        array $relationships = [],
        array $columns = ['*'],
        array $filters = []
    );

    public function find(string $id, array $columns = ['*']);
    
    public function create(array $data);
    
    public function update(string $id, array $data);
    
    public function delete(string $id);
}
