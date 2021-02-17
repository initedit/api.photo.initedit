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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/account/auth', "Api\AccountController@Authenticate");
Route::post('/account/create', "Api\AccountController@Create");
Route::post('/account/delete', "Api\AccountController@Delete");
Route::post('/account/info', "Api\AccountController@Info");

Route::post('/photo/get', "Api\PhotoController@Get");
Route::get('/photo/download', "Api\PhotoController@Download");
Route::middleware('photo')->post('/photo/upload', "Api\PhotoController@Upload");
Route::middleware('photo')->post('/photo/update', "Api\PhotoController@Update");
Route::middleware('photo')->post('/photo/delete', "Api\PhotoController@DeletePhoto");