<?php

namespace App\Http\Repositories\Interfaces;

interface ArtistInterface extends BaseCRUDInterface
{
    public function allArtists(): ?array;

}
