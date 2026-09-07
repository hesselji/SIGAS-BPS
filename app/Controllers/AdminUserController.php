<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\MasterData;
use App\Models\User;

final class AdminUserController
{
    public function index(): void { Auth::requireAdmin(); View::render('admin/users/index',['users'=>User::all()]); }
    public function create(): void { Auth::requireAdmin(); View::render('admin/users/create',['teams'=>MasterData::workTeams()]); }
    public function store(): void
    {
        Auth::requireAdmin();Csrf::validate($_POST['_token']??null);
        $name=trim($_POST['name']??'');$email=strtolower(trim($_POST['email']??''));$password=$_POST['password']??'';$role=$_POST['role']??'USER';$team=(int)($_POST['work_team_id']??0);
        $errors=[];if(strlen($name)<3)$errors['name']='Nama minimal 3 karakter.';if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors['email']='Email tidak valid.';if(strlen($password)<8)$errors['password']='Password minimal 8 karakter.';if(!in_array($role,['ADMIN','USER'],true))$errors['role']='Role tidak valid.';
        if($errors){$_SESSION['errors']=$errors;$_SESSION['old']=$_POST;header('Location:/admin/users/create');exit;}
        try{$id=User::create(['name'=>$name,'email'=>$email,'password'=>$password,'role'=>$role,'work_team_id'=>$team]);AuditLog::write(Auth::id(),'CREATE_USER','user',$id,['email'=>$email,'role'=>$role]);$_SESSION['flash_success']='User berhasil dibuat.';header('Location:/admin/users');}catch(\Throwable $e){$_SESSION['flash_error']='Gagal membuat user. Pastikan email belum digunakan.';header('Location:/admin/users/create');}
    }

    /*
|--------------------------------------------------------------------------
| Halaman Edit User
|--------------------------------------------------------------------------
*/
public function edit(string $id): void
{
    Auth::requireAdmin();

    $userId = (int) $id;

    $user = User::findForAdmin($userId);

    if (!$user) {
        http_response_code(404);
        View::render('errors/404');
        exit;
    }

    View::render(
        'admin/users/edit',
        [
            'user' => $user,
            'teams' => MasterData::workTeams()
        ]
    );
}


/*
|--------------------------------------------------------------------------
| Update User
|--------------------------------------------------------------------------
*/
public function update(string $id): void
{
    Auth::requireAdmin();

    Csrf::validate(
        $_POST['_token'] ?? null
    );

    $userId = (int) $id;

    $user = User::findForAdmin($userId);

    if (!$user) {
        $_SESSION['flash_error'] =
            'Pengguna tidak ditemukan.';

        header('Location: /admin/users');
        exit;
    }


    $name = trim(
        $_POST['name'] ?? ''
    );

    $email = strtolower(
        trim($_POST['email'] ?? '')
    );

    $role =
        $_POST['role'] ?? 'USER';

    $team = (int) (
        $_POST['work_team_id'] ?? 0
    );


    $errors = [];


    /*
    |--------------------------------------------------------------------------
    | Validasi Nama
    |--------------------------------------------------------------------------
    */

    if (strlen($name) < 3) {
        $errors['name'] =
            'Nama minimal 3 karakter.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Email
    |--------------------------------------------------------------------------
    */

    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {
        $errors['email'] =
            'Email tidak valid.';
    } elseif (
        User::emailExists(
            $email,
            $userId
        )
    ) {
        $errors['email'] =
            'Email sudah digunakan oleh akun lain.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Role
    |--------------------------------------------------------------------------
    */

    if (
        !in_array(
            $role,
            ['ADMIN', 'USER'],
            true
        )
    ) {
        $errors['role'] =
            'Role tidak valid.';
    }


    /*
    |--------------------------------------------------------------------------
    | Admin Tidak Boleh Menurunkan Role Akun Sendiri
    |--------------------------------------------------------------------------
    */

    if (
        $userId === Auth::id() &&
        $role !== $user['role']
    ) {
        $errors['role'] =
            'Role akun yang sedang Anda gunakan tidak dapat diubah.';
    }


    /*
    |--------------------------------------------------------------------------
    | Lindungi Admin Terakhir
    |--------------------------------------------------------------------------
    */

    if (
        $user['role'] === 'ADMIN' &&
        $role !== 'ADMIN' &&
        (int) $user['is_active'] === 1 &&
        User::activeAdminCount() <= 1
    ) {
        $errors['role'] =
            'Admin terakhir tidak dapat diubah menjadi USER.';
    }


    /*
    |--------------------------------------------------------------------------
    | Jika Validasi Gagal
    |--------------------------------------------------------------------------
    */

    if ($errors) {

        $_SESSION['errors'] =
            $errors;

        $_SESSION['old'] =
            $_POST;

        header(
            'Location: /admin/users/' .
            $userId .
            '/edit'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    try {

        User::updateProfile(
            $userId,
            [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'work_team_id' => $team
            ]
        );


        AuditLog::write(
            Auth::id(),
            'UPDATE_USER',
            'user',
            $userId,
            [
                'old_email' =>
                    $user['email'],

                'new_email' =>
                    $email,

                'old_role' =>
                    $user['role'],

                'new_role' =>
                    $role
            ]
        );


        $_SESSION['flash_success'] =
            'Data pengguna berhasil diperbarui.';


        header(
            'Location: /admin/users'
        );

        exit;

    } catch (\Throwable $e) {

        $_SESSION['flash_error'] =
            'Gagal memperbarui pengguna.';

        header(
            'Location: /admin/users/' .
            $userId .
            '/edit'
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Bekukan / Aktifkan User
|--------------------------------------------------------------------------
*/
public function toggleStatus(string $id): void
{
    Auth::requireAdmin();

    Csrf::validate(
        $_POST['_token'] ?? null
    );

    $userId = (int) $id;

    $user = User::findForAdmin(
        $userId
    );

    if (!$user) {

        $_SESSION['flash_error'] =
            'Pengguna tidak ditemukan.';

        header(
            'Location: /admin/users'
        );

        exit;
    }


    $currentlyActive =
        (int) $user['is_active'] === 1;

    $newActive =
        !$currentlyActive;


    /*
    |--------------------------------------------------------------------------
    | Jangan Bekukan Akun Sendiri
    |--------------------------------------------------------------------------
    */

    if (
        $userId === Auth::id() &&
        !$newActive
    ) {
        $_SESSION['flash_error'] =
            'Anda tidak dapat membekukan akun yang sedang digunakan.';

        header(
            'Location: /admin/users'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Jangan Bekukan Admin Terakhir
    |--------------------------------------------------------------------------
    */

    if (
        !$newActive &&
        $user['role'] === 'ADMIN' &&
        User::activeAdminCount() <= 1
    ) {
        $_SESSION['flash_error'] =
            'Admin terakhir tidak dapat dibekukan.';

        header(
            'Location: /admin/users'
        );

        exit;
    }


    User::setActive(
        $userId,
        $newActive
    );


    AuditLog::write(
        Auth::id(),
        $newActive
            ? 'ACTIVATE_USER'
            : 'FREEZE_USER',
        'user',
        $userId,
        [
            'email' =>
                $user['email']
        ]
    );


    $_SESSION['flash_success'] =
        $newActive
            ? 'Akun berhasil diaktifkan kembali.'
            : 'Akun berhasil dibekukan.';


    header(
        'Location: /admin/users'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Hapus User
|--------------------------------------------------------------------------
*/
public function delete(string $id): void
{
    Auth::requireAdmin();

    Csrf::validate(
        $_POST['_token'] ?? null
    );

    $userId = (int) $id;

    $user = User::findForAdmin(
        $userId
    );


    if (!$user) {

        $_SESSION['flash_error'] =
            'Pengguna tidak ditemukan.';

        header(
            'Location: /admin/users'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Jangan Hapus Akun Sendiri
    |--------------------------------------------------------------------------
    */

    if ($userId === Auth::id()) {

        $_SESSION['flash_error'] =
            'Anda tidak dapat menghapus akun yang sedang digunakan.';

        header(
            'Location: /admin/users'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Lindungi Admin Terakhir
    |--------------------------------------------------------------------------
    */

    if (
        $user['role'] === 'ADMIN' &&
        (int) $user['is_active'] === 1 &&
        User::activeAdminCount() <= 1
    ) {
        $_SESSION['flash_error'] =
            'Admin terakhir tidak dapat dihapus.';

        header(
            'Location: /admin/users'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Soft Delete
    |--------------------------------------------------------------------------
    */

    User::softDelete(
        $userId
    );


    AuditLog::write(
        Auth::id(),
        'DELETE_USER',
        'user',
        $userId,
        [
            'email' =>
                $user['email'],

            'role' =>
                $user['role']
        ]
    );


    $_SESSION['flash_success'] =
        'Pengguna berhasil dihapus. Riwayat aktivitas tetap dipertahankan.';


    header(
        'Location: /admin/users'
    );

    exit;
}
}
