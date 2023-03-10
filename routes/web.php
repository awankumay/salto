<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/templatesurat', function () {
    return view('templatesurat');
});
Route::get('/', function () {
    if (Auth::check()) {
        return view('home');
    }
    return view('auth.login');
});

Route::get('/triggercetak', function () {
    return view('triggercetak');
});

Route::get('cetaksurat/{hash}', 'CetakSuratController@cetaksurat')->name('cetaksurat');
Auth::routes();
Route::group(['middleware' => ['auth:web']], function(){
    Route::prefix('dashboard')->group(function () {
        Route::get('/home', 'HomeController@index')->name('home');
        Route::resource('role', 'RoleController');
        Route::resource('user', 'UserController');
        Route::post('user/import_excel', 'UserController@import_excel');
        Route::resource('post-category', 'PostCategoryController');
        Route::resource('permission', 'PermissionCategoryController');
        // Route::resource('materi-wbs', 'MateriWBSController');
        // Route::get('materi-wbs/data', 'MateriWBSController@loadData')->name('data.load');
        Route::resource('content', 'ContentController');
        Route::resource('slider', 'SliderController');
        Route::resource('report', 'ReportController');
        Route::resource('reportwbs', 'ReportwbsController');
        Route::resource('grade', 'GradeController');
        Route::resource('keluarga-asuh', 'KeluargaAsuhController');
        Route::resource('pembina-keluarga-asuh', 'PembinaKeluargaAsuhController');
        Route::resource('waliasuh-keluarga-asuh', 'WaliasuhKeluargaAsuhController');
        Route::resource('taruna-keluarga-asuh', 'TarunaKeluargaAsuhController');
        Route::resource('surat-izin', 'SuratIzinController');
        Route::resource('absensi', 'AbsensiController');
        Route::resource('jurnal', 'JurnalController');
        Route::resource('pengasuhan', 'PengasuhanController');
        Route::resource('prestasi', 'PrestasiController');
        Route::resource('suket', 'SuketController');
        Route::resource('hukuman-dinas', 'HukumanDinasController');
        Route::get('jurnaldetail', 'JurnalController@jurnaldetail')->name('jurnaldetail');
        Route::get('editprofile', 'SaltoController@editprofile')->name('editprofile');
        Route::patch('setprofile', 'SaltoController@setprofile')->name('setprofile');
        Route::get('gettaruna', 'SaltoController@gettaruna')->name('gettaruna');
        Route::post('getregencies', 'SaltoController@getregencies')->name('getregencies');
        Route::get('totaluser', 'SaltoController@totaluser')->name('totaluser');
        Route::get('totalSurat', 'SaltoController@totalSurat')->name('totalSurat');
        Route::post('deleteExistImageUser', 'UserController@deleteExistImageUser')->name('deleteExistImageUser');
        Route::post('deleteExistImagePost', 'ContentController@deleteExistImagePost')->name('deleteExistImagePost');
        Route::post('deleteExistImageSlider', 'SliderController@deleteExistImageSlider')->name('deleteExistImageSlider');
        Route::post('deleteExistImageSurat', 'SuratIzinController@deleteExistImageSurat')->name('deleteExistImageSurat');
        Route::post('deleteExistImageSuket', 'SuketController@deleteExistImageSuket')->name('deleteExistImageSuket');
        Route::post('deleteExistImagePrestasi', 'PrestasiController@deleteExistImagePrestasi')->name('deleteExistImagePrestasi');
        Route::post('deleteItem', 'TransactionController@deleteItem')->name('deleteItem');
        Route::post('updatedItem', 'TransactionController@updatedItem')->name('updatedItem');
        Route::post('clockin', 'SaltoController@clockin')->name('clockin');
        Route::post('clockout', 'SaltoController@clockout')->name('clockout');
        Route::post('inputjurnal', 'SaltoController@inputjurnal')->name('inputjurnal');
        Route::post('disposisisuratizin', 'SaltoController@disposisisuratizin')->name('disposisisuratizin');
        Route::post('disposisisuket', 'SaltoController@disposisisuket')->name('disposisisuket');
        Route::post('disposisiprestasi', 'SaltoController@disposisiprestasi')->name('disposisiprestasi');
        Route::post('approvesuratizin', 'SaltoController@approvesuratizin')->name('approvesuratizin');
        Route::post('approvesuket', 'SaltoController@approvesuket')->name('approvesuket');
        Route::post('approveprestasi', 'SaltoController@approveprestasi')->name('approveprestasi');
        Route::post('approvehukdis', 'SaltoController@approvehukdis')->name('approvehukdis');
        Route::post('exportdata', 'SaltoController@exportdata')->name('exportdata');
        Route::get('cetaksurat/id/{id}/id_user/{id_user}/cetak/{cetak}', 'SaltoController@cetaksurat')->name('cetaksurat');
    });
});
