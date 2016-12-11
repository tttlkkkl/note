<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/login', ['as'=>'login','uses'=>'AdminController@login']);
Route::get('/loginCallback', ['as'=>'loginCallback','uses'=>'AdminController@loginCallback']);
Route::get('/admin', 'AdminController@index');
Route::get('/updateNoteBook', 'AdminController@updateNoteBook');
Route::get('/updateNote', 'AdminController@updateNote');
Route::get('/updateAll', 'AdminController@updateAll');
Route::get('/transformToTag', 'AdminController@transformToTag');
Route::any('/transformOneNote', 'AdminController@transformOneNote');
