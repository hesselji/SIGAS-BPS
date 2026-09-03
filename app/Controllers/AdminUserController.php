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
}
