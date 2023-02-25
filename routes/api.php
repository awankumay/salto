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
| is assigned the "API" middleware group. Enjoy building your API!
|
*/
// Route::get('/ok', 'API\LookController@test')->name('ok');
Route::get('cetaksurat/id/{id}/id_user/{id_user}/cetak/{cetak}', 'API\LookController@cetaksurat')->name('cetaksurat');
Route::post('clockin', 'API\LookController@clockin')->name('clockin');
Route::post('clockout', 'API\LookController@clockout')->name('clockout');
Route::post('login', 'API\LoginController@login');
Route::group(['middleware' => ['CheckUser:API']], function(){
    /* Route::get('getprofile/{id}', 'API\LookController@getprofile')->name('getprofile');

    Route::post('getdata', 'API\LookController@getdata')->name('getdata');
    Route::get('getslider', 'API\LookController@getslider')->name('getslider');
    Route::post('getberita/id_category/{id_category}', 'API\LookController@getberita')->name('getberita');
    Route::get('getberitadetail/id/{id}', 'API\LookController@getberitadetail')->name('getberitadetail');

    Route::get('getabsen/id_user/{id_user}', 'API\LookController@getabsen')->name('getabsen');
    Route::post('clockin', 'API\LookController@clockin')->name('clockin');
    Route::post('clockout', 'API\LookController@clockout')->name('clockout');

    Route::get('getjurnal/tanggal/{date}/id_user/{id_user}', 'API\LookController@getjurnaldetail')->name('getjurnal');
    Route::get('getjurnal/tanggal/{date}/id/{id}/id_user/{id_user}', 'API\LookController@getjurnaldetailbyid')->name('getjurnal');
    Route::post('inputjurnal', 'API\LookController@inputjurnal')->name('inputjurnal');
    Route::post('deletejurnal', 'API\LookController@deletejurnal')->name('deleterjurnal');

    Route::post('getsuratizin', 'API\LookController@getsuratizin')->name('getsuratizin');
    Route::get('getsuratizincategory/id_user/{id_user}', 'API\LookController@getsuratizincategory')->name('getsuratizincategory');
    Route::get('getsuratizin/id/{id}/id_user/{id_user}', 'API\LookController@suratizindetailbyid')->name('getsuratizin');
    Route::post('inputsuratizin', 'API\LookController@inputsuratizin')->name('inputsuratizin');
    Route::post('deletesuratizin', 'API\LookController@deletesuratizin')->name('deletesuratizin');
    Route::post('disposisisuratizin', 'API\LookController@disposisisuratizin')->name('disposisisuratizin');
    Route::post('approvesuratizin', 'API\LookController@approvesuratizin')->name('approvesuratizin');
    Route::get('cetaksuratizin/id/{id}/id_user/{id_user}', 'API\LookController@cetaksurat')->name('cetaksuratizin');
    Route::get('triggercetak', 'API\LookController@triggercetak')->name('triggercetak');

    Route::post('getprestasi', 'API\PrestasiController@getprestasi')->name('getprestasi');
    Route::get('getprestasi/id/{id}/id_user/{id_user}', 'API\PrestasiController@prestasidetail')->name('getprestasi');
    Route::post('inputprestasi', 'API\PrestasiController@inputprestasi')->name('inputprestasi');
    Route::post('deleteprestasi', 'API\PrestasiController@deleteprestasi')->name('deleteprestasi');
    Route::post('disposisiprestasi', 'API\PrestasiController@disposisiprestasi')->name('disposisiprestasi');
    Route::post('approveprestasi', 'API\PrestasiController@approveprestasi')->name('approveprestasi');
    Route::get('cetakprestasi/id/{id}/id_user/{id_user}', 'API\PrestasiController@cetakprestasi')->name('cetakprestasi');

    Route::post('getsuket', 'API\SuketController@getsuket')->name('getsuket');
    Route::get('getsuket/id/{id}/id_user/{id_user}', 'API\SuketController@suketdetail')->name('getsuket');
    Route::post('inputsuket', 'API\SuketController@inputsuket')->name('inputsuket');
    Route::post('deletesuket', 'API\SuketController@deletesuket')->name('deletesuket');
    Route::post('disposisisuket', 'API\SuketController@disposisisuket')->name('disposisisuket');
    Route::post('approvesuket', 'API\SuketController@approvesuket')->name('approvesuket');
    Route::get('cetaksuket/id/{id}/id_user/{id_user}', 'API\SuketController@cetaksuket')->name('cetaksuket');

    Route::post('getpengasuhan', 'API\PengasuhanController@getpengasuhan')->name('getpengasuhan');
    Route::get('getpengasuhan/id/{id}/id_user/{id_user}', 'API\PengasuhanController@pengasuhandetail')->name('getpengasuhan');
    Route::post('inputpengasuhan', 'API\PengasuhanController@inputpengasuhan')->name('inputpengasuhan');
    Route::post('deletepengasuhan', 'API\PengasuhanController@deletepengasuhan')->name('deletepengasuhan');
    
    Route::post('gethukdis', 'API\HukumanDinasController@gethukdis')->name('gethukdis');
    Route::get('gethukdis/id/{id}/id_user/{id_user}', 'API\HukumanDinasController@hukdisdetail')->name('gethukdis');
    Route::post('inputhukdis', 'API\HukumanDinasController@inputhukdis')->name('inputhukdis');
    Route::post('deletehukdis', 'API\HukumanDinasController@deletehukdis')->name('deletehukdis');
    Route::post('approvehukdis', 'API\HukumanDinasController@approvehukdis')->name('approvehukdis');
    Route::get('cetakhukdis/id/{id}/id_user/{id_user}', 'API\HukumanDinasController@cetakhukdis')->name('cetakhukdis'); */
});
Route::group(['middleware' => ['auth:API', 'CheckUser:API']], function(){
    Route::get('logout', 'API\LoginController@logout');
    Route::get('/ok', 'API\LookController@test')->name('ok');
    Route::get('getprofile/{id_user}', 'API\LookController@getprofile')->name('getprofile');
    Route::post('setprofile', 'API\LookController@setprofile')->name('setprofile');
    Route::post('setfcm', 'API\LookController@settoken')->name('setfcm');
   
    Route::post('getdata', 'API\LookController@getdata')->name('getdata');
    Route::get('getslider', 'API\LookController@getslider')->name('getslider');
    Route::post('getberita/id_category/{id_category}', 'API\LookController@getberita')->name('getberita');
    Route::get('getberitadetail/id/{id}', 'API\LookController@getberitadetail')->name('getberitadetail');

    Route::get('getjurnal/tanggal/{date}/id_user/{id_user}/id_login/{id_login}', 'API\LookController@getjurnaldetail')->name('getjurnal');
    Route::get('getjurnal/tanggal/{date}/id/{id}/id_user/{id_user}', 'API\LookController@getjurnaldetailbyid')->name('getjurnal');
    Route::get('getabsen/id_user/{id_user}', 'API\LookController@getabsen')->name('getabsen');
    Route::post('inputjurnal', 'API\LookController@inputjurnal')->name('inputjurnal');
    Route::post('deletejurnal', 'API\LookController@deletejurnal')->name('deleterjurnal');
    Route::get('gettaruna/id_user/{id_user}', 'API\LookController@gettaruna')->name('gettaruna');
    Route::get('searchtaruna/id_user/{id_user}/name', 'API\LookController@gettarunaname')->name('searchtaruna');
    Route::post('getjurnal', 'API\LookController@getjurnal')->name('getjurnal');

    Route::post('getsuratizin', 'API\LookController@getsuratizin')->name('getsuratizin');
    Route::get('getsuratizincategory/id_user/{id_user}', 'API\LookController@getsuratizincategory')->name('getsuratizincategory');
    Route::get('getsuratizin/id/{id}/id_user/{id_user}', 'API\LookController@suratizindetailbyid')->name('getsuratizin');
    Route::post('inputsuratizin', 'API\LookController@inputsuratizin')->name('inputsuratizin');
    Route::post('deletesuratizin', 'API\LookController@deletesuratizin')->name('deletesuratizin');
    Route::post('disposisisuratizin', 'API\LookController@disposisisuratizin')->name('disposisisuratizin');
    Route::post('approvesuratizin', 'API\LookController@approvesuratizin')->name('approvesuratizin');

    Route::get('triggercetak', 'API\LookController@triggercetak')->name('triggercetak');

    Route::post('getprestasi', 'API\PrestasiController@getprestasi')->name('getprestasi');
    Route::get('getprestasi/id/{id}/id_user/{id_user}', 'API\PrestasiController@prestasidetail')->name('getprestasi');
    Route::post('inputprestasi', 'API\PrestasiController@inputprestasi')->name('inputprestasi');
    Route::post('deleteprestasi', 'API\PrestasiController@deleteprestasi')->name('deleteprestasi');
    Route::post('disposisiprestasi', 'API\PrestasiController@disposisiprestasi')->name('disposisiprestasi');
    Route::post('approveprestasi', 'API\PrestasiController@approveprestasi')->name('approveprestasi');
    Route::get('cetakprestasi/id/{id}/id_user/{id_user}', 'API\PrestasiController@cetakprestasi')->name('cetakprestasi');

    Route::post('getsuket', 'API\SuketController@getsuket')->name('getsuket');
    Route::get('getsuket/id/{id}/id_user/{id_user}', 'API\SuketController@suketdetail')->name('getsuket');
    Route::post('inputsuket', 'API\SuketController@inputsuket')->name('inputsuket');
    Route::post('deletesuket', 'API\SuketController@deletesuket')->name('deletesuket');
    Route::post('disposisisuket', 'API\SuketController@disposisisuket')->name('disposisisuket');
    Route::post('approvesuket', 'API\SuketController@approvesuket')->name('approvesuket');
    Route::get('cetaksuket/id/{id}/id_user/{id_user}', 'API\SuketController@cetaksuket')->name('cetaksuket');

    Route::post('getpengasuhan', 'API\PengasuhanController@getpengasuhan')->name('getpengasuhan');
    Route::get('getpengasuhan/id/{id}/id_user/{id_user}', 'API\PengasuhanController@pengasuhandetail')->name('getpengasuhan');
    Route::post('inputpengasuhan', 'API\PengasuhanController@inputpengasuhan')->name('inputpengasuhan');
    Route::post('deletepengasuhan', 'API\PengasuhanController@deletepengasuhan')->name('deletepengasuhan');
    
    Route::post('gethukdis', 'API\HukumanDinasController@gethukdis')->name('gethukdis');
    Route::get('gethukdis/id/{id}/id_user/{id_user}', 'API\HukumanDinasController@hukdisdetail')->name('gethukdis');
    Route::post('inputhukdis', 'API\HukumanDinasController@inputhukdis')->name('inputhukdis');
    Route::post('deletehukdis', 'API\HukumanDinasController@deletehukdis')->name('deletehukdis');
    Route::post('approvehukdis', 'API\HukumanDinasController@approvehukdis')->name('approvehukdis');
    Route::get('cetakhukdis/id/{id}/id_user/{id_user}', 'API\HukumanDinasController@cetakhukdis')->name('cetakhukdis');

    Route::post('getpengaduan', 'API\PengaduanController@getpengaduan')->name('getpengaduan');
    Route::get('getpengaduan/id/{id}/id_user/{id_user}', 'API\PengaduanController@pengaduandetail')->name('getpengaduan');
    Route::post('inputpengaduan', 'API\PengaduanController@inputpengaduan')->name('inputpengaduan');
    Route::post('deletepengaduan', 'API\PengaduanController@deletepengaduan')->name('deletepengaduan');
    Route::post('tanggapanpengaduan', 'API\PengaduanController@tanggapan')->name('tanggapanpengaduan');

    Route::post('getwbs', 'API\WBSController@getwbs')->name('getwbs');
    Route::get('getwbs/id/{id}/id_user/{id_user}', 'API\WBSController@wbsdetail')->name('getwbs');
    Route::post('inputwbs', 'API\WBSController@inputwbs')->name('inputwbs');
    Route::post('deletewbs', 'API\WBSController@deletewbs')->name('deletewbs');
    Route::post('tanggapanwbs', 'API\WBSController@tanggapan')->name('tanggapanwbs');
    Route::get('getcategorywbs', 'API\WBSController@category')->name('getcategorywbs');

    Route::post('settoken', 'API\LookController@settoken')->name('settoken');
    
    Route::get('getgrade', 'API\LookController@getgrade')->name('getgrade');
    Route::get('checkversion/{version}', 'API\LookController@checkversion')->name('checkversion');
});
Route::group([    
    'namespace' => 'Auth',    
    'middleware' => 'API',    
    'prefix' => 'password'
], function () {    
    Route::post('forgot', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
/*Route::middleware('auth:API')->get('/user', function (Request $request) {
    return $request->user();
});*/
