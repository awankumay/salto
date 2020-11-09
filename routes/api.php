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
Route::post('getberita/id_category/{id_category}', 'api\LookController@getberita')->name('getberita');
Route::post('getberita/id_category/{id_category}', 'api\LookController@getberita')->name('getberita');
Route::get('getberitadetail/id/{id}', 'api\LookController@getberitadetail')->name('getberitadetail');
Route::post('clockin', 'api\LookController@clockin')->name('clockin');
Route::post('clockout', 'api\LookController@clockout')->name('clockout');
Route::post('getjurnal', 'api\LookController@getjurnal')->name('getjurnal');
Route::get('getjurnal/tanggal/{date}/id_user/{id_user}', 'api\LookController@getjurnaldetail')->name('getjurnal');
Route::get('getjurnal/tanggal/{date}/id/{id}/id_user/{id_user}', 'api\LookController@getjurnaldetailbyid')->name('getjurnal');
Route::post('inputjurnal', 'api\LookController@inputjurnal')->name('inputjurnal');
Route::post('deletejurnal', 'api\LookController@deletejurnal')->name('deleterjurnal');

Route::post('getsuratizin', 'api\LookController@getsuratizin')->name('getsuratizin');
Route::get('getsuratizin/id/{id}', 'api\LookController@suratizindetailbyid')->name('getsuratizin');
Route::post('inputsuratizin', 'api\LookController@inputsuratizin')->name('inputsuratizin');

Route::group(['middleware' => ['auth:api', 'role:Pengunjung']], function(){
Route::get('logout', 'api\LoginController@logout');
Route::get('/ok', 'api\LookController@test')->name('ok');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
