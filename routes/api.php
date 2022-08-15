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
    Route::post('/reschedule/{id}', 'AppointmentController@reschedule');
    Route::get('/booking', 'Api\BookingController@index');
    Route::put('/booking-cancel/{id}', 'Api\BookingController@cancelBooking');
    Route::post('/booking-checkin/{id}', 'Api\BookingController@punchInBooking');
    // trainer-student actions
    Route::apiResource('/trainer-students', 'Api\TrainerController');
    Route::get('/availability-students/{id}', 'Api\TrainerController@getNotMyStudents');
    // finance
    Route::get('/finance', 'Api\PaymentController@index');
    // all user actions
    Route::apiResource('users', 'Api\UserController');
    Route::put('/ban-user/{id}', 'Api\UserController@banUser');
    Route::put('/active-user/{id}', 'Api\UserController@activeUser');
    // simple API
    Route::apiResource('/rooms', 'Api\RoomController');
    Route::get('/locations', 'Api\LocationController@index');    //
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
