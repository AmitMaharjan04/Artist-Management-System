<?php

namespace App\Http\Services;

use App\Http\Repositories\Interfaces\SongInterface;

class SongService
{
    public function __construct(private SongInterface $songRepository) {}


}
