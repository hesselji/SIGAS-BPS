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
           AND u.deleted_at IS NULL
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
           AND deleted_at IS NULL
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
                u.work_team_id,
                u.is_active,
                u.created_at,
                wt.name AS work_team_name
             FROM users u
             LEFT JOIN work_teams wt ON wt.id = u.work_team_id
             WHERE u.deleted_at IS NULL
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

    /*
|--------------------------------------------------------------------------
| Ambil User Untuk Administrasi
|--------------------------------------------------------------------------
|
| Berbeda dengan find(), akun yang sedang dibekukan tetap dapat
| dibuka oleh administrator.
|
*/
public static function findForAdmin(int $id): ?array
{
    $stmt = Database::connection()->prepare(
        'SELECT
            u.*,
            wt.name AS work_team_name
         FROM users u
         LEFT JOIN work_teams wt ON wt.id = u.work_team_id
         WHERE u.id = ?
           AND u.deleted_at IS NULL
         LIMIT 1'
    );

    $stmt->execute([$id]);

    return $stmt->fetch() ?: null;
}


/*
|--------------------------------------------------------------------------
| Cek Email
|--------------------------------------------------------------------------
*/
public static function emailExists(
    string $email,
    ?int $exceptId = null
): bool {
    if ($exceptId !== null) {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*)
             FROM users
             WHERE email = ?
               AND id <> ?'
        );

        $stmt->execute([
            $email,
            $exceptId
        ]);
    } else {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*)
             FROM users
             WHERE email = ?'
        );

        $stmt->execute([$email]);
    }

    return (int) $stmt->fetchColumn() > 0;
}


/*
|--------------------------------------------------------------------------
| Update Informasi User
|--------------------------------------------------------------------------
*/
public static function updateProfile(
    int $id,
    array $data
): void {
    $stmt = Database::connection()->prepare(
        'UPDATE users
         SET
            name = ?,
            email = ?,
            role = ?,
            work_team_id = ?,
            updated_at = NOW()
         WHERE id = ?
           AND deleted_at IS NULL'
    );

    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['role'],
        $data['work_team_id'] ?: null,
        $id
    ]);
}


/*
|--------------------------------------------------------------------------
| Aktifkan / Bekukan Akun
|--------------------------------------------------------------------------
*/
public static function setActive(
    int $id,
    bool $active
): void {
    $stmt = Database::connection()->prepare(
        'UPDATE users
         SET
            is_active = ?,
            updated_at = NOW()
         WHERE id = ?
           AND deleted_at IS NULL'
    );

    $stmt->execute([
        $active ? 1 : 0,
        $id
    ]);
}


/*
|--------------------------------------------------------------------------
| Soft Delete User
|--------------------------------------------------------------------------
|
| User tidak dihapus secara fisik agar relasi surat dan audit tetap aman.
|
*/
public static function softDelete(int $id): void
{
    $stmt = Database::connection()->prepare(
        'UPDATE users
         SET
            is_active = 0,
            deleted_at = NOW(),
            updated_at = NOW()
         WHERE id = ?
           AND deleted_at IS NULL'
    );

    $stmt->execute([$id]);
}


/*
|--------------------------------------------------------------------------
| Hitung Admin Aktif
|--------------------------------------------------------------------------
*/
public static function activeAdminCount(): int
{
    $stmt = Database::connection()->query(
        "SELECT COUNT(*)
         FROM users
         WHERE role = 'ADMIN'
           AND is_active = 1
           AND deleted_at IS NULL"
    );

    return (int) $stmt->fetchColumn();
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