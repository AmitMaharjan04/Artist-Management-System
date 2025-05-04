<?php

namespace App\Http\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseCRUDInterface 
{
    public function create(array $data): object;
    public function update(int|string $id, array $data): object;
    public function delete(int $id): bool;
    public function lists(int $page, int $perPage,  $sortField, $sortOrder, $filters = []): LengthAwarePaginator;
    public function find(int $id): ?object;
}
