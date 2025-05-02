<?php

namespace App\Http\Repositories\Interfaces;

use App\Models\User;

interface UserInterface extends BaseCRUDInterface
{
    public function findByEmail(string $email): ?object;
    public function find(int $userId): ?object;
}
