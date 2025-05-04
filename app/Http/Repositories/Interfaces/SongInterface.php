<?php

namespace App\Http\Repositories\Interfaces;

use App\Models\User;

interface SongInterface extends BaseCRUDInterface
{
    public function find($data): ?object;
}
