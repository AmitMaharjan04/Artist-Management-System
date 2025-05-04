<?php

namespace App\Http\Services;

use App\Http\Repositories\Interfaces\ArtistInterface;

class SongService
{
    public function __construct(private ArtistInterface $artistRepository) {}

    public function datas()
    {
        $artists = $this->artistRepository->allArtists();
        $artists = array_map(function ($row) {
            return [
                'id'    => $row->name,
                'name'  => $row->name,
            ];
        }, array: $artists);
        $genresRaw = [
            'rnb'   => 'RnB',
            'country'   => 'Country',
            'classic'   => 'Classic',
            'rock'  => 'Rock',
            'jazz'  => 'Jazz',
        ];

        $genres = array_map(function ($key, $value) {
            return [
                'key'   => $key,
                'value' => $value,
            ];
        }, array_keys($genresRaw), $genresRaw);
        return [
            'artists'   =>  $artists,
            'genres'    =>  $genres
        ];
    }
}
