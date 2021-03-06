<?php

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

Route::get('guest', 'Auth\LoginController@guestLogin')->name('login.guest');


Route::get('/', 'EffortController@index')->name('home');

Route::get('/efforts/index', 'EffortController@index')->name('efforts.index');

Auth::routes();

Route::resource('/goals', 'GoalController')->except(['index, show'])->middleware('auth');
Route::resource('/goals', 'GoalController')->only(['show']);

Route::resource('/efforts', 'EffortController');



Route::get('/mypage/edit/{id}', 'ProfileController@edit')->name('mypage.edit')->middleware('auth');

Route::post('/mypage/update', 'ProfileController@update')->name('mypage.update')->middleware('auth');

Route::get('/mypage/{id}', 'ProfileController@show')->name('mypage.show'); // 追記


