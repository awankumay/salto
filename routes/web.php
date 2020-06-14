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
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('role', 'RoleController');
    Route::resource('user', 'UserController');
    Route::resource('post-category', 'PostCategoryController');
    Route::resource('post', 'PostController');
    Route::post('deleteExistImageUser', 'UserController@deleteExistImageUser')->name('deleteExistImageUser');
});
