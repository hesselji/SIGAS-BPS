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
        return self::id() !== null;
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
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
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
