<?php

use Illuminate\Support\Facades\Route;
/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the 'api' middleware group. Enjoy building your API!
  |
 */

Route::get('/version', function () use ($router) {
    return $router->app->version();
});
Route::post('/account/auth', ['uses' => 'Api\AccountController@Authenticate']);
Route::post('/account/create', ['uses' => 'Api\AccountController@Create']);
Route::post('/account/delete', ['middleware' => 'photo.auth:write', 'uses' => 'Api\AccountController@Delete']);
Route::post('/account/info', ['middleware' => 'photo.auth:read', 'uses' => 'Api\AccountController@Info']);

Route::post('/photo/get', ['middleware' => 'photo.auth:read', 'uses' => 'Api\PhotoController@Get']);
Route::get('/photo/download', ['middleware' => ['photo.auth:read', 'photo.meta.auth'], 'uses' => 'Api\PhotoController@Download']);
Route::post('/photo/upload', ['middleware' => 'photo.auth:write', 'uses' => 'Api\PhotoController@Upload']);
Route::post('/photo/update', ['middleware' => ['photo.auth:write', 'photo.meta.auth'], 'uses' => 'Api\PhotoController@Update']);
Route::post('/photo/delete', ['middleware' => ['photo.auth:write', 'photo.meta.auth'], 'uses' => 'Api\PhotoController@DeletePhoto']);
