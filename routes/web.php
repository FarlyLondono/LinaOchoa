<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

//RUTAS API

//Rutas controlador usuario
Route::get('/api/user', 'UserController@index')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('/api/login', 'UserController@login');
Route::post('/api/register','UserController@register');
Route::put('/api/user/update', 'UserController@update');
Route::get('/api/user/detail/{id}', 'UserController@detail');

//Rutas Servicios
Route::resource('/api/servicios', 'ServiciosController');
Route::post('/api/servicios/upload', 'ServiciosController@upload');

//Rutas Citas
Route::resource('/api/citas', 'CitasController')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
