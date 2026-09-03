<?php
declare(strict_types=1);

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function(string $class): void {
    $prefix='App\\';
    if(!str_starts_with($class,$prefix)) return;
    $file=dirname(__DIR__).'/app/'.str_replace('\\','/',substr($class,strlen($prefix))).'.php';
    if(is_file($file)) require $file;
});
require dirname(__DIR__).'/app/Core/helpers.php';

use App\Core\Env;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\OutgoingLetterController;
use App\Controllers\AdminUserController;

try { Env::load(dirname(__DIR__).'/.env'); }
catch(Throwable $e){ http_response_code(500); exit('<h2>Konfigurasi belum siap</h2><p>Copy <code>.env.example</code> menjadi <code>.env</code> dan isi koneksi database.</p>'); }

$router=new Router();
$router->get('/', fn()=>header('Location: /dashboard'));
$router->get('/login',[AuthController::class,'loginForm']);
$router->post('/login',[AuthController::class,'login']);
$router->post('/logout',[AuthController::class,'logout']);
$router->get('/dashboard',[DashboardController::class,'index']);
$router->get('/letters',[OutgoingLetterController::class,'index']);
$router->get('/letters/create',[OutgoingLetterController::class,'create']);
$router->post('/letters',[OutgoingLetterController::class,'store']);
$router->get('/letters/{id}',[OutgoingLetterController::class,'show']);
$router->post('/letters/{id}/cancel',[OutgoingLetterController::class,'cancel']);
$router->get('/api/classifications',[OutgoingLetterController::class,'classifications']);
$router->get('/admin/users',[AdminUserController::class,'index']);
$router->get('/admin/users/create',[AdminUserController::class,'create']);
$router->post('/admin/users',[AdminUserController::class,'store']);
$router->dispatch($_SERVER['REQUEST_METHOD'],$_SERVER['REQUEST_URI']);
