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
Auth::routes();
Route::group(['middleware' => ['auth:web']], function(){
    Route::prefix('dashboard')->group(function () {
        Route::get('/home', 'HomeController@index')->name('home');
        Route::resource('role', 'RoleController');
        Route::resource('user', 'UserController');
        Route::resource('post-category', 'PostCategoryController');
        Route::resource('product-category', 'ProductCategoryController');
        Route::resource('content', 'ContentController');
        Route::resource('convict', 'ConvictController');
        Route::resource('campaign', 'CampaignController');
        Route::resource('auction', 'AuctionController');
        Route::resource('tags', 'TagsController');
        Route::post('deleteExistImageUser', 'UserController@deleteExistImageUser')->name('deleteExistImageUser');
        Route::post('deleteExistImagePost', 'ContentController@deleteExistImagePost')->name('deleteExistImagePost');
        Route::post('deleteExistImageCampaign', 'CampaignController@deleteExistImageCampaign')->name('deleteExistImageCampaign');
        Route::post('deleteExistImageAuction', 'AuctionController@deleteExistImageAuction')->name('deleteExistImageAuction');
        Route::post('deleteExistImageConvict', 'ConvictController@deleteExistImageConvict')->name('deleteExistImageConvict');
    });
});
