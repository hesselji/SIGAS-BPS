<?php
namespace App\Models;

use App\Core\Database;

final class User
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT u.*, wt.name AS work_team_name FROM users u LEFT JOIN work_teams wt ON wt.id=u.work_team_id WHERE u.id=? AND u.is_active=1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function byEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email=? AND is_active=1 LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return Database::connection()->query('SELECT u.id,u.name,u.email,u.role,u.is_active,u.created_at,wt.name AS work_team_name FROM users u LEFT JOIN work_teams wt ON wt.id=u.work_team_id ORDER BY u.name')->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO users (name,email,password_hash,role,work_team_id,is_active,created_at,updated_at) VALUES (?,?,?,?,?,1,NOW(),NOW())');
        $stmt->execute([$data['name'],$data['email'],password_hash($data['password'], PASSWORD_BCRYPT),$data['role'],$data['work_team_id'] ?: null]);
        return (int) Database::connection()->lastInsertId();
    }
}
