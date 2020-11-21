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
Route::get('gettaruna/id_user/{id_user}', 'api\LookController@gettaruna')->name('gettaruna');
Route::post('getdata', 'api\LookController@getdata')->name('getdata');
Route::get('getslider', 'api\LookController@getslider')->name('getslider');
Route::post('getberita/id_category/{id_category}', 'api\LookController@getberita')->name('getberita');
Route::get('getberitadetail/id/{id}', 'api\LookController@getberitadetail')->name('getberitadetail');

Route::get('getabsen/id_user/{id_user}', 'api\LookController@getabsen')->name('getabsen');
Route::post('clockin', 'api\LookController@clockin')->name('clockin');
Route::post('clockout', 'api\LookController@clockout')->name('clockout');

Route::post('getjurnal', 'api\LookController@getjurnal')->name('getjurnal');
Route::get('getjurnal/tanggal/{date}/id_user/{id_user}', 'api\LookController@getjurnaldetail')->name('getjurnal');
Route::get('getjurnal/tanggal/{date}/id/{id}/id_user/{id_user}', 'api\LookController@getjurnaldetailbyid')->name('getjurnal');
Route::post('inputjurnal', 'api\LookController@inputjurnal')->name('inputjurnal');
Route::post('deletejurnal', 'api\LookController@deletejurnal')->name('deleterjurnal');

Route::post('getsuratizin', 'api\LookController@getsuratizin')->name('getsuratizin');
Route::get('getsuratizincategory/id_user/{id_user}', 'api\LookController@getsuratizincategory')->name('getsuratizincategory');
Route::get('getsuratizin/id/{id}/id_user/{id_user}', 'api\LookController@suratizindetailbyid')->name('getsuratizin');
Route::post('inputsuratizin', 'api\LookController@inputsuratizin')->name('inputsuratizin');
Route::post('deletesuratizin', 'api\LookController@deletesuratizin')->name('deletesuratizin');
Route::post('disposisisuratizin', 'api\LookController@disposisisuratizin')->name('disposisisuratizin');
Route::post('approvesuratizin', 'api\LookController@approvesuratizin')->name('approvesuratizin');
Route::get('cetaksuratizin/id/{id}/id_user/{id_user}', 'api\LookController@cetaksurat')->name('cetaksuratizin');
Route::get('triggercetak', 'api\LookController@triggercetak')->name('triggercetak');

Route::post('getprestasi', 'api\PrestasiController@getprestasi')->name('getprestasi');
Route::get('getprestasi/id/{id}/id_user/{id_user}', 'api\PrestasiController@prestasidetail')->name('getprestasi');
Route::post('inputprestasi', 'api\PrestasiController@inputprestasi')->name('inputprestasi');
Route::post('deleteprestasi', 'api\PrestasiController@deleteprestasi')->name('deleteprestasi');
Route::post('disposisiprestasi', 'api\PrestasiController@disposisiprestasi')->name('disposisiprestasi');
Route::post('approveprestasi', 'api\PrestasiController@approveprestasi')->name('approveprestasi');
Route::get('cetakprestasi/id/{id}/id_user/{id_user}', 'api\PrestasiController@cetakprestasi')->name('cetakprestasi');

Route::post('getsuket', 'api\SuketController@getsuket')->name('getsuket');
Route::get('getsuket/id/{id}/id_user/{id_user}', 'api\SuketController@suketdetail')->name('getsuket');
Route::post('inputsuket', 'api\SuketController@inputsuket')->name('inputsuket');
Route::post('deletesuket', 'api\SuketController@deletesuket')->name('deletesuket');
Route::post('disposisisuket', 'api\SuketController@disposisisuket')->name('disposisisuket');
Route::post('approvesuket', 'api\SuketController@approvesuket')->name('approvesuket');
Route::get('cetaksuket/id/{id}/id_user/{id_user}', 'api\SuketController@cetaksuket')->name('cetaksuket');

Route::group(['middleware' => ['auth:api', 'role:Pengunjung']], function(){
Route::get('logout', 'api\LoginController@logout');
Route::get('/ok', 'api\LookController@test')->name('ok');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
