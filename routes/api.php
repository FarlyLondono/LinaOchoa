<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiciosController;
use App\Http\Controllers\CitasController;
use App\Http\Middleware\ApiAuthMiddleware;


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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//RUTAS API

Route::get('prueba', 'UserController@prueba');

//Route::get('serviciosimagen/{id}', [ServiciosController::class, 'show'])->name('servicios.show');

//Rutas controlador usuario
Route::get('user', [UserController::class, 'index'])->middleware(ApiAuthMiddleware::class);
Route::post('login', [UserController::class, 'login']);

Route::post('registerUser', [UserController::class, 'registerUser']);//->middleware(ApiMiddleware::class);

Route::post('registerAdmin', [UserController::class, 'registerAdmin'])->middleware(ApiAuthMiddleware::class);

Route::put('user/update', [UserController::class, 'update']);
Route::get('user/detail/{id}', [UserController::class, 'detail'])->middleware(ApiAuthMiddleware::class);

//Rutas Servicios
Route::resource('servicios', 'ServiciosController');



Route::post('servicios/upload', [ServiciosController::class, 'upload']);

//Rutas Citas
Route::resource('citas',  'CitasController')->middleware(ApiAuthMiddleware::class);

