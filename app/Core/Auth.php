<?php
namespace App\Core;

use App\Models\User;

final class Auth
{
    public static function user(): ?array
    {
        $id = $_SESSION['user_id'] ?? null;
        return $id ? User::find((int) $id) : null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function check(): bool
{
    $id = self::id();

    if ($id === null) {
        return false;
    }

    return User::find($id) !== null;
}

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'ADMIN';
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

   public static function requireLogin(): void
{
    $id = self::id();

    /*
    |--------------------------------------------------------------------------
    | Belum Login
    |--------------------------------------------------------------------------
    */
    if ($id === null) {
        $_SESSION['flash_error'] =
            'Silakan login terlebih dahulu.';

        header('Location: /login');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Akun Dibekukan / Dihapus
    |--------------------------------------------------------------------------
    */
    if (User::find($id) === null) {

        unset($_SESSION['user_id']);

        $_SESSION['flash_error'] =
            'Akun Anda sedang dinonaktifkan atau tidak lagi tersedia. Hubungi administrator.';

        header('Location: /login');
        exit;
    }
}

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            View::render('errors/403');
            exit;
        }
    }
}
