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

/** 
 * Laravel 8 routing changed a bit
 * See @link https://laravel.com/docs/8.x/upgrade#routing
 */

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'providers'], function () {
    Route::get('/list', 'App\Http\Controllers\Providers\ProviderController@getProviders');
});