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
Route::post('/getdata', 'api\LookController@getdata')->name('getdata');
Route::post('surveyikm', 'api\LookController@surveyikm')->name('surveyikm');
Route::group(['middleware' => ['auth:api', 'role:Pengunjung']], function(){
Route::get('logout', 'api\LoginController@logout');
Route::get('/ok', 'api\LookController@test')->name('ok');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
