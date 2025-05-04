<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserInterface
{

    public function create(array $data): object
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);

        DB::insert("INSERT INTO user ($columns) VALUES ($placeholders)", $values);

        $id = DB::getPdo()->lastInsertId();
        return DB::selectOne("SELECT * FROM user WHERE id = ?", [$id]);
    }

    public function update(int|string $id, array $data): object
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        DB::update("UPDATE user SET $setClause WHERE id = ?", $values);

        return DB::selectOne("SELECT * FROM user WHERE id = ?", [$id]);
    }

    public function delete(int $id): bool
    {
        return DB::delete("DELETE FROM user WHERE id = ?", [$id]) > 0;
    }

    public function lists(int $page, int $perPage,  $sortField, $sortOrder, $filters = []): LengthAwarePaginator
    {
        $whereClause = '';
        $params = [];

        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $key => $value) {
                $conditions[] = $key === 'name' ? "CONCAT(first_name, ' ', last_name) LIKE ?" : "$key LIKE ?";
                $params[] = "%$value%";
            }
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sortFieldSql = ($sortField === 'name') ? "CONCAT(first_name, ' ', last_name)" : $sortField;
        // Get total count
        $total = DB::selectOne("SELECT COUNT(*) as count FROM user $whereClause", $params)->count;

        // Get paginated data
        $offset = ($page - 1) * $perPage;
        $results = DB::select(
            "SELECT id, `password`, email, phone, DATE(dob) as dob, gender, address, role_type, CONCAT(first_name, ' ', last_name) AS name FROM user 
             $whereClause 
             ORDER BY $sortFieldSql $sortOrder 
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
        return DB::selectOne("SELECT * FROM user WHERE email = ? limit 1", [$email]);
    }

    public function find(int $id): ?object
    {
        return DB::selectOne("SELECT id, first_name, last_name, `password`, email, phone, DATE(dob) as dob, gender, address, role_type FROM user WHERE id = ?", [$id]);
    }
}
