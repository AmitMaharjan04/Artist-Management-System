<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\SongInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SongRepository implements SongInterface
{

    public function create(array $data): object
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);

        DB::insert("INSERT INTO music ($columns) VALUES ($placeholders)", $values);
        return DB::selectOne("SELECT * FROM music WHERE artist_id = ? ORDER BY created_at DESC
    LIMIT 1", [$data['artist_id']]);
    }

    public function update(int|string $id, array $data): object
    {
        $data['updated_at'] = $data['id'];
        unset($data['id']);
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        
        DB::update("UPDATE music SET $setClause WHERE updated_at = ?", $values);

        return DB::selectOne("SELECT * FROM music WHERE updated_at = ?", [$id]);
    }

    public function delete(int|string $id): bool
    {
        return DB::delete("DELETE FROM music WHERE updated_at = ?", [$id]) > 0;
    }

    public function lists(int $page, int $perPage,  $sortField, $sortOrder, $filters = []): LengthAwarePaginator
    {
        $whereClause = '';
        $params = [];

        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $key => $value) {
                if ($key === 'artist_name') {
                    $conditions[] = "artist.name LIKE ?";
                } else {
                    $conditions[] = "$key LIKE ?";
                }
                $params[] = "%$value%";
            }
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        // Get total count
        $total = DB::selectOne("SELECT COUNT(*) as count FROM music  LEFT JOIN artist ON music.artist_id = artist.id $whereClause", $params)->count;

        // Get paginated data
        $offset = ($page - 1) * $perPage;
        $results = DB::select(
            "SELECT artist.name AS artist_name, music.title, music.album_name, music.genre, music.created_at, music.updated_at
             FROM music
             LEFT JOIN artist ON music.artist_id = artist.id
             $whereClause
             ORDER BY $sortField $sortOrder
             LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );
        

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function findByEmail(string $email): ?object
    {
        return DB::selectOne("SELECT * FROM music WHERE email = ? limit 1", [$email]);
    }

    public function find($data): ?object
    {
        $data['artist_id'] = DB::selectOne("SELECT id FROM artist WHERE name = ? limit 1", [$data['artist_name']])->id;
        return DB::selectOne("SELECT artist_id, title, album_name, genre, created_at, updated_at FROM music WHERE artist_id = ? and updated_at = ?", [$data['artist_id'], $data['updated_at']]);
    }
}
