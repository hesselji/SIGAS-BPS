<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\OutgoingLetter;
use App\Models\User;

final class DashboardController
{
    /**
     * Menampilkan halaman dashboard.
     */
    public function index(): void
    {
        Auth::requireLogin();

        View::render(
            'dashboard/index',
            OutgoingLetter::dashboardStats()
        );
    }

    /**
     * Menampilkan halaman form ganti password.
     */
    public function changePasswordForm(): void
    {
        Auth::requireLogin();

        View::render(
            'dashboard/change-password'
        );
    }

    /**
     * Memproses perubahan password user.
     */
    public function changePassword(): void
    {
        Auth::requireLogin();

        $currentPassword = trim(
            $_POST['current_password'] ?? ''
        );

        $newPassword = trim(
            $_POST['new_password'] ?? ''
        );

        $confirmPassword = trim(
            $_POST['confirm_password'] ?? ''
        );

        /**
         * Mengambil ID user yang sedang login.
         */
        $authUser = auth_user();

        $userId = (int) (
            $authUser['id'] ?? 0
        );

        if ($userId <= 0) {
            $_SESSION['error'] =
                'Sesi pengguna tidak valid. Silakan login kembali.';

            header('Location: /login');
            exit;
        }

        /**
         * Validasi field kosong.
         */
        if (
            $currentPassword === ''
            || $newPassword === ''
            || $confirmPassword === ''
        ) {
            $_SESSION['error'] =
                'Semua field password wajib diisi.';

            header(
                'Location: /dashboard/change-password'
            );
            exit;
        }

        /**
         * Ambil user terbaru dari database.
         */
        $user = User::find($userId);

        if (!$user) {
            $_SESSION['error'] =
                'Data pengguna tidak ditemukan atau akun tidak aktif.';

            header(
                'Location: /dashboard'
            );
            exit;
        }

        /**
         * Verifikasi password saat ini.
         */
        if (
            !password_verify(
                $currentPassword,
                $user['password_hash']
            )
        ) {
            $_SESSION['error'] =
                'Password saat ini tidak sesuai.';

            header(
                'Location: /dashboard/change-password'
            );
            exit;
        }

        /**
         * Validasi panjang password baru.
         */
        if (strlen($newPassword) < 8) {
            $_SESSION['error'] =
                'Password baru minimal harus terdiri dari 8 karakter.';

            header(
                'Location: /dashboard/change-password'
            );
            exit;
        }

        /**
         * Validasi konfirmasi password.
         */
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] =
                'Konfirmasi password baru tidak sama.';

            header(
                'Location: /dashboard/change-password'
            );
            exit;
        }

        /**
         * Password baru tidak boleh sama dengan password lama.
         */
        if (
            password_verify(
                $newPassword,
                $user['password_hash']
            )
        ) {
            $_SESSION['error'] =
                'Password baru tidak boleh sama dengan password saat ini.';

            header(
                'Location: /dashboard/change-password'
            );
            exit;
        }

        /**
         * Simpan password baru.
         */
        $updated = User::updatePassword(
            $userId,
            $newPassword
        );

        if (!$updated) {
            $_SESSION['error'] =
                'Password gagal diperbarui. Silakan coba kembali.';

            header(
                'Location: /dashboard/change-password'
            );
            exit;
        }

        $_SESSION['success'] =
            'Password berhasil diperbarui.';

        header('Location: /dashboard');
        exit;
    }
}