<?php

declare(strict_types=1);

session_start();

/*
|--------------------------------------------------------------------------
| Error Reporting
|--------------------------------------------------------------------------
|
| Secara default error tidak ditampilkan ke browser.
| Jika APP_DEBUG=true, error akan ditampilkan setelah .env dibaca.
|
*/

error_reporting(E_ALL);
ini_set('display_errors', '0');


/*
|--------------------------------------------------------------------------
| Autoloader
|--------------------------------------------------------------------------
*/

spl_autoload_register(
    function (string $class): void {

        $prefix = 'App\\';

        if (!str_starts_with(
            $class,
            $prefix
        )) {
            return;
        }

        $relativeClass = substr(
            $class,
            strlen($prefix)
        );

        $file =
            dirname(__DIR__) .
            '/app/' .
            str_replace(
                '\\',
                '/',
                $relativeClass
            ) .
            '.php';

        if (is_file($file)) {
            require $file;
        }
    }
);


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

require dirname(__DIR__) .
    '/app/Core/helpers.php';


/*
|--------------------------------------------------------------------------
| Import Class
|--------------------------------------------------------------------------
*/

use App\Core\Env;
use App\Core\Router;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\OutgoingLetterController;
use App\Controllers\AdminUserController;
use App\Controllers\AdminMasterController;


/*
|--------------------------------------------------------------------------
| Load Environment
|--------------------------------------------------------------------------
*/

try {

    Env::load(
        dirname(__DIR__) . '/.env'
    );

} catch (Throwable $e) {

    http_response_code(500);

    exit(
        '<h2>Konfigurasi belum siap</h2>' .
        '<p>File <code>.env</code> belum tersedia atau tidak dapat dibaca.</p>'
    );
}


/*
|--------------------------------------------------------------------------
| Application Timezone
|--------------------------------------------------------------------------
*/

date_default_timezone_set(
    Env::get(
        'APP_TIMEZONE',
        'Asia/Jakarta'
    )
);


/*
|--------------------------------------------------------------------------
| Debug Mode
|--------------------------------------------------------------------------
*/

if (
    Env::bool(
        'APP_DEBUG',
        false
    )
) {
    ini_set(
        'display_errors',
        '1'
    );
}


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

$router = new Router();


/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
|
| User yang belum login diarahkan ke login.
| User yang sudah login akan diteruskan oleh AuthController
| dari /login ke /dashboard.
|
*/

$router->get(
    '/',
    function (): void {

        header(
            'Location: /login'
        );

        exit;
    }
);


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

$router->get(
    '/login',
    [
        AuthController::class,
        'loginForm'
    ]
);

$router->post(
    '/login',
    [
        AuthController::class,
        'login'
    ]
);

$router->post(
    '/logout',
    [
        AuthController::class,
        'logout'
    ]
);


/*
|--------------------------------------------------------------------------
| Account
|--------------------------------------------------------------------------
*/

$router->get(
    '/account/password',
    [
        AuthController::class,
        'changePasswordForm'
    ]
);

$router->post(
    '/account/password',
    [
        AuthController::class,
        'updatePassword'
    ]
);


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

$router->get(
    '/dashboard',
    [
        DashboardController::class,
        'index'
    ]
);


/*
|--------------------------------------------------------------------------
| Agenda Surat
|--------------------------------------------------------------------------
*/

$router->get(
    '/letters',
    [
        OutgoingLetterController::class,
        'index'
    ]
);

$router->get(
    '/letters/create',
    [
        OutgoingLetterController::class,
        'create'
    ]
);

$router->post(
    '/letters',
    [
        OutgoingLetterController::class,
        'store'
    ]
);

$router->get(
    '/letters/{id}',
    [
        OutgoingLetterController::class,
        'show'
    ]
);

$router->post(
    '/letters/{id}/cancel',
    [
        OutgoingLetterController::class,
        'cancel'
    ]
);


/*
|--------------------------------------------------------------------------
| API Klasifikasi / KKA
|--------------------------------------------------------------------------
*/

$router->get(
    '/api/classifications',
    [
        OutgoingLetterController::class,
        'classifications'
    ]
);

$router->get(
    '/api/classification-groups',
    [
        OutgoingLetterController::class,
        'classificationGroups'
    ]
);

$router->get(
    '/api/classification-items',
    [
        OutgoingLetterController::class,
        'classificationItems'
    ]
);


/*
|--------------------------------------------------------------------------
| Admin - User
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/users',
    [
        AdminUserController::class,
        'index'
    ]
);

$router->get(
    '/admin/users/create',
    [
        AdminUserController::class,
        'create'
    ]
);

$router->post(
    '/admin/users',
    [
        AdminUserController::class,
        'store'
    ]
);

$router->get(
    '/admin/users/{id}/edit',
    [
        AdminUserController::class,
        'edit'
    ]
);

$router->post(
    '/admin/users/{id}/update',
    [
        AdminUserController::class,
        'update'
    ]
);

$router->post(
    '/admin/users/{id}/toggle-status',
    [
        AdminUserController::class,
        'toggleStatus'
    ]
);

$router->post(
    '/admin/users/{id}/delete',
    [
        AdminUserController::class,
        'delete'
    ]
);


/*
|--------------------------------------------------------------------------
| Admin - Aturan Penomoran
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/numbering-rules',
    [
        AdminMasterController::class,
        'numbering'
    ]
);

$router->post(
    '/admin/numbering-rules/{id}',
    [
        AdminMasterController::class,
        'updateRule'
    ]
);

$router->post(
    '/admin/letter-types/{id}',
    [
        AdminMasterController::class,
        'updateLetterType'
    ]
);


/*
|--------------------------------------------------------------------------
| Admin - Katalog KKA
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/classifications',
    [
        AdminMasterController::class,
        'classifications'
    ]
);


/*
|--------------------------------------------------------------------------
| Dispatch
|--------------------------------------------------------------------------
*/

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);