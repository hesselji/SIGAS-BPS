<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\User;

final class AuthController
{
    public function loginForm(): void { if (Auth::check()) { header('Location: /dashboard'); exit; } View::render('auth/login'); }
    public function login(): void
    {
        Csrf::validate($_POST['_token'] ?? null);
        $email=trim($_POST['email'] ?? '');$password=$_POST['password'] ?? '';
        $user=User::byEmail($email);
        if (!$user || !password_verify($password,$user['password_hash'])) { $_SESSION['flash_error']='Email atau password salah.'; $_SESSION['old']=['email'=>$email]; header('Location: /login'); exit; }
        Auth::login($user);AuditLog::write((int)$user['id'],'LOGIN','user',(int)$user['id']);header('Location: /dashboard');exit;
    }
    public function logout(): void { Csrf::validate($_POST['_token'] ?? null); $id=Auth::id(); if($id) AuditLog::write($id,'LOGOUT','user',$id); Auth::logout(); header('Location: /login'); exit; }
}
