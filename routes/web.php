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
    // list providers and their restrictions
    Route::get('/list', 'App\Http\Controllers\Providers\ProviderController@getProviders');

    // upload image for valid providers and validate them as send response
    Route::post('/createImage', 'App\Http\Controllers\Providers\ImageUploadController@uploadImages')->name('image.upload');

    // upload image for valid providers and validate them as send response
    Route::post('/createVideo', 'App\Http\Controllers\Providers\VideoUploadController@uploadVideos')->name('video.upload');

    // list uploaded objects
    Route::get('/getObjects', 'App\Http\Controllers\Providers\ProviderController@getObjects');
});