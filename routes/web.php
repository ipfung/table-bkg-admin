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

Route::get('/test-gw', 'PaymentGatewayController@test');

// checkout
Route::get('/checkout/{orderNum}', 'PaymentGatewayController@paymentPage');
Route::post('/checkout/feedback', 'PaymentGatewayController@returnPage');
Route::post('/checkout/notify', 'PaymentGatewayController@notifyPage');

// pay for outstanding amount.
Route::get('/pay/{orderNum}', 'PaymentGatewayController@paymentPage');
Route::post('/pay/feedback', 'PaymentGatewayController@returnPage');
Route::post('/pay/notify', 'PaymentGatewayController@notifyPage');


//reporting
Route::get('/report-order', 'ReportController@orderReport')->name('report.order');
Route::get('/export-report-order', 'ReportController@orderReportExport')->name('export.report.order');
Route::get('/export-report-order1', 'ReportController@orderReportExport1');
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
