<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Framework\Service\Facade\Environment;

Route::get('/', function () {

    $urlParts = explode('.', $_SERVER['HTTP_HOST']);
    $subdomain = $urlParts[0];

    if (Environment::isProduction() && $subdomain !== 'admin') {
        abort(404);
    }

    return redirect('/login');
});

Route::get('/account_delete', 'Controller@index')->name('account_delete');
Route::post('/account_delete', 'Controller@delete')->name('account_delete_post');

Auth::routes(['register' => false]);
Broadcast::routes([
    'middleware' => ['api'],
]);
