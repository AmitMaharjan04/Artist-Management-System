<?php

namespace App\Providers;

use App\Http\Repositories\ArtistRepository;
use App\Http\Repositories\Interfaces\ArtistInterface;
use App\Http\Repositories\Interfaces\SongInterface;
use App\Http\Repositories\Interfaces\UserInterface;
use App\Http\Repositories\SongRepository;
use App\Http\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserInterface::class    =>  UserRepository::class,
        ArtistInterface::class    =>  ArtistRepository::class,
        SongInterface::class        =>  SongRepository::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}