<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'Api\AuthController@login');
Route::post('/logout', 'Api\AuthController@logout');

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/appointment', 'AppointmentController@index');
    Route::post('/appointment', 'AppointmentController@store');
    Route::get('/booking', 'BookingController@index');
    Route::post('/booking-checkin/{id}', 'BookingController@punchInBooking');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
