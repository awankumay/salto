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
        Route::resource('post-category', 'PostCategoryController');
        Route::resource('permission', 'PermissionCategoryController');
        Route::resource('content', 'ContentController');
        Route::resource('slider', 'SliderController');
        Route::resource('report', 'ReportController');
        Route::resource('grade', 'GradeController');
        Route::resource('keluarga-asuh', 'KeluargaAsuhController');
        Route::resource('pembina-keluarga-asuh', 'PembinaKeluargaAsuhController');
        Route::resource('waliasuh-keluarga-asuh', 'WaliasuhKeluargaAsuhController');
        Route::resource('taruna-keluarga-asuh', 'TarunaKeluargaAsuhController');
        Route::resource('surat-izin', 'SuratIzinController');
        Route::resource('absensi', 'AbsensiController');
        Route::resource('jurnal', 'JurnalController');
        Route::resource('pengasuhan', 'PengasuhanController');
        Route::resource('suket', 'SuketController');
        Route::get('jurnaldetail', 'JurnalController@jurnaldetail')->name('jurnaldetail');
        Route::get('editprofile', 'SaltoController@editprofile')->name('editprofile');
        Route::get('gettaruna', 'SaltoController@gettaruna')->name('gettaruna');
        Route::post('getregencies', 'SaltoController@getregencies')->name('getregencies');
        Route::post('deleteExistImageUser', 'UserController@deleteExistImageUser')->name('deleteExistImageUser');
        Route::post('deleteExistImagePost', 'ContentController@deleteExistImagePost')->name('deleteExistImagePost');
        Route::post('deleteExistImageCampaign', 'CampaignController@deleteExistImageCampaign')->name('deleteExistImageCampaign');
        Route::post('deleteExistImageAuction', 'AuctionController@deleteExistImageAuction')->name('deleteExistImageAuction');
        Route::post('deleteExistImageConvict', 'ConvictController@deleteExistImageConvict')->name('deleteExistImageConvict');
        Route::post('deleteExistImageProduct', 'ProductController@deleteExistImageProduct')->name('deleteExistImageProduct');
        Route::post('deleteExistImagePayment', 'PaymentController@deleteExistImagePayment')->name('deleteExistImagePayment');
        Route::post('deleteExistImageSlider', 'SliderController@deleteExistImageSlider')->name('deleteExistImageSlider');
        Route::post('deleteExistImageSurat', 'SuratIzinController@deleteExistImageSurat')->name('deleteExistImageSurat');
        Route::post('deleteExistImageSuket', 'SuketController@deleteExistImageSuket')->name('deleteExistImageSuket');
        Route::post('deleteItem', 'TransactionController@deleteItem')->name('deleteItem');
        Route::post('updatedItem', 'TransactionController@updatedItem')->name('updatedItem');
        Route::post('clockin', 'SaltoController@clockin')->name('clockin');
        Route::post('clockout', 'SaltoController@clockout')->name('clockout');
        Route::post('inputjurnal', 'SaltoController@inputjurnal')->name('inputjurnal');
        Route::post('disposisisuratizin', 'SaltoController@disposisisuratizin')->name('disposisisuratizin');
        Route::post('disposisisuket', 'SaltoController@disposisisuket')->name('disposisisuket');
        Route::post('approvesuratizin', 'SaltoController@approvesuratizin')->name('approvesuratizin');
        Route::post('approvesuket', 'SaltoController@approvesuket')->name('approvesuket');
        Route::get('cetaksurat/id/{id}/id_user/{id_user}/cetak/{cetak}', 'SaltoController@cetaksurat')->name('cetaksurat');
    });
});
