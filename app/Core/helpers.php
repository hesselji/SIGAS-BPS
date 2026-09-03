<?php
use App\Core\Auth;
use App\Core\Csrf;

function e(?string $value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function csrf_field(): string { return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">'; }
function auth_user(): ?array { return Auth::user(); }
function old(string $key, string $default = ''): string { return e($_SESSION['old'][$key] ?? $default); }
function flash(string $key): ?string { $v = $_SESSION[$key] ?? null; unset($_SESSION[$key]); return $v; }
function format_date_id(?string $value, bool $withTime = false): string {
    if (!$value) return '-';
    $ts = strtotime($value);
    return $withTime ? date('d M Y H:i', $ts) : date('d M Y', $ts);
}
function sensitivity_label(string $code): string {
    return match ($code) { 'BIASA' => 'Biasa', 'PENTING' => 'Penting', 'RAHASIA' => 'Rahasia', 'SANGAT_RAHASIA' => 'Sangat Rahasia', default => $code };
}
