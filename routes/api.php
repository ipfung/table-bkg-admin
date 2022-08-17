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
    Route::get('/menu', 'Api\MenuController@index');

    Route::get('/dashboard', 'Api\DashboardController@index');
    Route::get('/appointment', 'AppointmentController@index');
    Route::post('/appointment', 'AppointmentController@store');
    Route::post('/reschedule/{id}', 'AppointmentController@reschedule');
    Route::get('/booking', 'Api\BookingController@index');
    Route::put('/booking-cancel/{id}', 'Api\BookingController@cancelBooking');
    Route::post('/booking-checkin/{id}', 'Api\BookingController@punchInBooking');
    // trainer-student actions
    Route::apiResource('/trainer-students', 'Api\TrainerController');
    Route::get('/availability-students/{id}', 'Api\TrainerController@getNotMyStudents');
    // calendar data
    Route::get('/appointments', 'Api\CalendarAppointmentController@index');
    // finance
    Route::get('/finance', 'Api\PaymentController@index');
    Route::put('/payment-reminder/{id}', 'Api\PaymentController@sendBillReminder');
    // all user actions
    Route::apiResource('users', 'Api\UserController');
    Route::put('/ban-user/{id}', 'Api\UserController@banUser');
    Route::put('/active-user/{id}', 'Api\UserController@activeUser');
    Route::post('/register-push', 'Api\UserController@registerPush');

    // notifications
    Route::get('/notifications', 'Api\NotificationController@index');
    // simple API
    Route::apiResource('/timeslots', 'Api\TimeslotController');
    Route::apiResource('/rooms', 'Api\RoomController');
    Route::get('/locations', 'Api\LocationController@index');    //
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
