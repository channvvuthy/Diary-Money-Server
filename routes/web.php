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

Route::get('/', function () {
    return view('welcome');
});
Route::get('send', 'SendSMSController@getSend');
Route::get('user/verify/{verification_code}', 'AuthController@verifyUser');
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@authenticate');
Route::post('recover', 'AuthController@recover');
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('test', function () {
        return response()->json(['foo' => 'bar']);
    });
});
