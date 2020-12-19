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
Route::get('cetaksurat/id/{id}/id_user/{id_user}/cetak/{cetak}', 'api\LookController@cetaksurat')->name('cetaksurat');
Route::post('clockin', 'api\LookController@clockin')->name('clockin');
Route::post('clockout', 'api\LookController@clockout')->name('clockout');
Route::post('login', 'api\LoginController@login');
Route::group(['middleware' => ['CheckUser:api']], function(){
    /* Route::get('getprofile/{id}', 'api\LookController@getprofile')->name('getprofile');

    Route::post('getdata', 'api\LookController@getdata')->name('getdata');
    Route::get('getslider', 'api\LookController@getslider')->name('getslider');
    Route::post('getberita/id_category/{id_category}', 'api\LookController@getberita')->name('getberita');
    Route::get('getberitadetail/id/{id}', 'api\LookController@getberitadetail')->name('getberitadetail');

    Route::get('getabsen/id_user/{id_user}', 'api\LookController@getabsen')->name('getabsen');
    Route::post('clockin', 'api\LookController@clockin')->name('clockin');
    Route::post('clockout', 'api\LookController@clockout')->name('clockout');

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

    Route::post('getpengasuhan', 'api\PengasuhanController@getpengasuhan')->name('getpengasuhan');
    Route::get('getpengasuhan/id/{id}/id_user/{id_user}', 'api\PengasuhanController@pengasuhandetail')->name('getpengasuhan');
    Route::post('inputpengasuhan', 'api\PengasuhanController@inputpengasuhan')->name('inputpengasuhan');
    Route::post('deletepengasuhan', 'api\PengasuhanController@deletepengasuhan')->name('deletepengasuhan');
    
    Route::post('gethukdis', 'api\HukumanDinasController@gethukdis')->name('gethukdis');
    Route::get('gethukdis/id/{id}/id_user/{id_user}', 'api\HukumanDinasController@hukdisdetail')->name('gethukdis');
    Route::post('inputhukdis', 'api\HukumanDinasController@inputhukdis')->name('inputhukdis');
    Route::post('deletehukdis', 'api\HukumanDinasController@deletehukdis')->name('deletehukdis');
    Route::post('approvehukdis', 'api\HukumanDinasController@approvehukdis')->name('approvehukdis');
    Route::get('cetakhukdis/id/{id}/id_user/{id_user}', 'api\HukumanDinasController@cetakhukdis')->name('cetakhukdis'); */
});
Route::group(['middleware' => ['auth:api', 'CheckUser:api']], function(){
    Route::get('logout', 'api\LoginController@logout');
    Route::get('/ok', 'api\LookController@test')->name('ok');
    Route::get('getprofile/{id_user}', 'api\LookController@getprofile')->name('getprofile');
    Route::post('setprofile', 'api\LookController@setprofile')->name('setprofile');
    Route::post('setfcm', 'api\LookController@settoken')->name('setfcm');
   
    Route::post('getdata', 'api\LookController@getdata')->name('getdata');
    Route::get('getslider', 'api\LookController@getslider')->name('getslider');
    Route::post('getberita/id_category/{id_category}', 'api\LookController@getberita')->name('getberita');
    Route::get('getberitadetail/id/{id}', 'api\LookController@getberitadetail')->name('getberitadetail');

    Route::get('getjurnal/tanggal/{date}/id_user/{id_user}/id_login/{id_login}', 'api\LookController@getjurnaldetail')->name('getjurnal');
    Route::get('getjurnal/tanggal/{date}/id/{id}/id_user/{id_user}', 'api\LookController@getjurnaldetailbyid')->name('getjurnal');
    Route::get('getabsen/id_user/{id_user}', 'api\LookController@getabsen')->name('getabsen');
    Route::post('inputjurnal', 'api\LookController@inputjurnal')->name('inputjurnal');
    Route::post('deletejurnal', 'api\LookController@deletejurnal')->name('deleterjurnal');
    Route::get('gettaruna/id_user/{id_user}', 'api\LookController@gettaruna')->name('gettaruna');
    Route::get('searchtaruna/id_user/{id_user}/name', 'api\LookController@gettarunaname')->name('searchtaruna');
    Route::post('getjurnal', 'api\LookController@getjurnal')->name('getjurnal');

    Route::post('getsuratizin', 'api\LookController@getsuratizin')->name('getsuratizin');
    Route::get('getsuratizincategory/id_user/{id_user}', 'api\LookController@getsuratizincategory')->name('getsuratizincategory');
    Route::get('getsuratizin/id/{id}/id_user/{id_user}', 'api\LookController@suratizindetailbyid')->name('getsuratizin');
    Route::post('inputsuratizin', 'api\LookController@inputsuratizin')->name('inputsuratizin');
    Route::post('deletesuratizin', 'api\LookController@deletesuratizin')->name('deletesuratizin');
    Route::post('disposisisuratizin', 'api\LookController@disposisisuratizin')->name('disposisisuratizin');
    Route::post('approvesuratizin', 'api\LookController@approvesuratizin')->name('approvesuratizin');

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

    Route::post('getpengasuhan', 'api\PengasuhanController@getpengasuhan')->name('getpengasuhan');
    Route::get('getpengasuhan/id/{id}/id_user/{id_user}', 'api\PengasuhanController@pengasuhandetail')->name('getpengasuhan');
    Route::post('inputpengasuhan', 'api\PengasuhanController@inputpengasuhan')->name('inputpengasuhan');
    Route::post('deletepengasuhan', 'api\PengasuhanController@deletepengasuhan')->name('deletepengasuhan');
    
    Route::post('gethukdis', 'api\HukumanDinasController@gethukdis')->name('gethukdis');
    Route::get('gethukdis/id/{id}/id_user/{id_user}', 'api\HukumanDinasController@hukdisdetail')->name('gethukdis');
    Route::post('inputhukdis', 'api\HukumanDinasController@inputhukdis')->name('inputhukdis');
    Route::post('deletehukdis', 'api\HukumanDinasController@deletehukdis')->name('deletehukdis');
    Route::post('approvehukdis', 'api\HukumanDinasController@approvehukdis')->name('approvehukdis');
    Route::get('cetakhukdis/id/{id}/id_user/{id_user}', 'api\HukumanDinasController@cetakhukdis')->name('cetakhukdis');

    Route::post('getpengaduan', 'api\PengaduanController@getpengaduan')->name('getpengaduan');
    Route::get('getpengaduan/id/{id}/id_user/{id_user}', 'api\PengaduanController@pengaduandetail')->name('getpengaduan');
    Route::post('inputpengaduan', 'api\PengaduanController@inputpengaduan')->name('inputpengaduan');
    Route::post('deletepengaduan', 'api\PengaduanController@deletepengaduan')->name('deletepengaduan');
    Route::post('tanggapanpengaduan', 'api\PengaduanController@tanggapan')->name('tanggapanpengaduan');

    Route::post('getwbs', 'api\WBSController@getwbs')->name('getwbs');
    Route::get('getwbs/id/{id}/id_user/{id_user}', 'api\WBSController@wbsdetail')->name('getwbs');
    Route::post('inputwbs', 'api\WBSController@inputwbs')->name('inputwbs');
    Route::post('deletewbs', 'api\WBSController@deletewbs')->name('deletewbs');
    Route::post('tanggapanwbs', 'api\WBSController@tanggapan')->name('tanggapanwbs');
    Route::get('getcategorywbs', 'api\WBSController@category')->name('getcategorywbs');

    Route::post('settoken', 'api\LookController@settoken')->name('settoken');
    
    Route::get('getgrade', 'api\LookController@getgrade')->name('getgrade');
});
Route::group([    
    'namespace' => 'Auth',    
    'middleware' => 'api',    
    'prefix' => 'password'
], function () {    
    Route::post('forgot', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
