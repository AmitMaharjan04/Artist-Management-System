<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\ArtistInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ArtistRepository implements ArtistInterface
{

    public function create(array $data): object
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);

        DB::insert("INSERT INTO artist ($columns) VALUES ($placeholders)", $values);

        $id = DB::getPdo()->lastInsertId();
        return DB::selectOne("SELECT * FROM artist WHERE id = ?", [$id]);
    }

    public function update(int|string $id, array $data): object
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        DB::update("UPDATE artist SET $setClause WHERE id = ?", $values);

        return DB::selectOne("SELECT * FROM artist WHERE id = ?", [$id]);
    }

    public function delete(int $id): bool
    {
        return DB::delete("DELETE FROM artist WHERE id = ?", [$id]) > 0;
    }

    public function lists(int $page, int $perPage,  $sortField, $sortOrder, $filters = []): LengthAwarePaginator
    {
        $whereClause = '';
        $params = [];

        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $key => $value) {
                $conditions[] = "$key LIKE ?";
                $params[] = "%$value%";
            }
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        // Get total count
        $total = DB::selectOne("SELECT COUNT(*) as count FROM artist $whereClause", $params)->count;

        // Get paginated data
        $offset = ($page - 1) * $perPage;
        $results = DB::select(
            "SELECT id, `name`, DATE(dob) as dob, gender, `address`, first_release_year, no_of_albums_released FROM artist 
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

    public function find(int $id): ?object
    {
        return DB::selectOne("SELECT id, `name`, DATE(dob) as dob, gender, `address`, first_release_year, no_of_albums_released FROM artist WHERE id = ?", [$id]);
    }

    public function allArtists(): ?array
    {
        return DB::select("SELECT id, `name` FROM artist");
    }
}
