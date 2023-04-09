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
    return view('welcome');
});

Route::get('/appointment', 'AppointmentController@index');

Route::get('/checkout/{orderNum}', 'PaymentGatewayController@paymentPage');
Route::post('/checkout/feedback', 'PaymentGatewayController@returnPage');
Route::post('/checkout/notify', 'PaymentGatewayController@notifyPage');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
