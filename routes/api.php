<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('login', 'api\LoginController@login');
Route::get('getprofile/{id}', 'api\LookController@getprofile')->name('getprofile');
Route::post('getdata', 'api\LookController@getdata')->name('getdata');
Route::get('getslider', 'api\LookController@getslider')->name('getslider');
Route::get('getsliderdetail/id/{id}', 'api\LookController@getsliderdetail')->name('getsliderdetail');
Route::get('getberita', 'api\LookController@getberita')->name('getberita');
Route::get('getberitadetail/id/{id}', 'api\LookController@getberitadetail')->name('getberitadetail');
Route::group(['middleware' => ['auth:api', 'role:Pengunjung']], function(){
Route::get('logout', 'api\LoginController@logout');
Route::get('/ok', 'api\LookController@test')->name('ok');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
