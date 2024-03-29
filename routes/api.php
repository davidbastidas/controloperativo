<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
], function () {
});
Route::post('login', 'ApiController@login');
Route::post('servicios/getServicios', 'ApiController@getServicios');
Route::post('servicios/actualizarAuditoria', 'ApiController@actualizarAuditoria');
Route::post('servicios/actualizarPci', 'ApiController@actualizarPci');
