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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'MessageController@index');
Route::post('/message', 'MessageController@messageSent');


Route::get('chat', 'MessageController@chat');
Route::get('messages', 'MessageController@fetch');
Route::post('messages', 'MessageController@sentMessage');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
