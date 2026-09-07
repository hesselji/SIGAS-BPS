<?php

namespace App\Models;

use App\Core\Database;

final class User
{
    /**
     * Ambil user aktif berdasarkan ID.
     */
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT
                u.*,
                wt.name AS work_team_name
             FROM users u
             LEFT JOIN work_teams wt ON wt.id = u.work_team_id
             WHERE u.id = ?
               AND u.is_active = 1
             LIMIT 1'
        );

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }


    /**
     * Ambil user aktif berdasarkan email.
     */
    public static function byEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT *
             FROM users
             WHERE email = ?
               AND is_active = 1
             LIMIT 1'
        );

        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }


    /**
     * Ambil seluruh user untuk halaman admin.
     */
    public static function all(): array
    {
        return Database::connection()
            ->query(
                'SELECT
                    u.id,
                    u.name,
                    u.email,
                    u.role,
                    u.is_active,
                    u.created_at,
                    wt.name AS work_team_name
                 FROM users u
                 LEFT JOIN work_teams wt ON wt.id = u.work_team_id
                 ORDER BY u.name'
            )
            ->fetchAll();
    }


    /**
     * Tambah user baru dari halaman admin.
     */
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (
                name,
                email,
                password_hash,
                role,
                work_team_id,
                is_active,
                created_at,
                updated_at
             )
             VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())'
        );

        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash(
                $data['password'],
                PASSWORD_BCRYPT
            ),
            $data['role'],
            $data['work_team_id'] ?: null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }


    /**
     * Ganti password user.
     *
     * Password plaintext hanya diterima di method ini,
     * kemudian langsung diubah menjadi hash BCRYPT.
     */
    public static function updatePassword(
        int $id,
        string $newPassword
    ): void {
        $passwordHash = password_hash(
            $newPassword,
            PASSWORD_BCRYPT
        );

        $stmt = Database::connection()->prepare(
            'UPDATE users
             SET
                password_hash = ?,
                updated_at = NOW()
             WHERE id = ?
               AND is_active = 1'
        );

        $stmt->execute([
            $passwordHash,
            $id,
        ]);
    }
}