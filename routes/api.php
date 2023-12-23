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

Route::get('/lemonade', function () {
    $values = [
        'name' => config('app.name')
    ];
    return json_encode($values);
});

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/menu', 'Api\MenuController@index');

    Route::get('/dashboard', 'Api\DashboardController@index');
    Route::get('/appointment', 'AppointmentController@index');
    Route::post('/appointment', 'AppointmentController@store');
    Route::get('/group-event-packages', 'Api\PackageController@getGroupEventTimeslot');
    Route::post('/package-lesson-date/{id}', 'Api\PackageController@createLessonDate');
    Route::put('/package-lesson-date/{id}', 'Api\PackageController@updateLessonDate');
    Route::delete('/package-lesson-date/{id}', 'Api\PackageController@deleteLessonDate');
    Route::post('/package-dates', 'AppointmentController@getPackageDates');
    Route::get('/package-timeslots', 'AppointmentController@getPackageTimeslots');
    Route::get('/gen-package-lessons/{id}', 'Api\PackageController@generateMoreLessons');
    Route::post('/reschedule/{id}', 'AppointmentController@reschedule');
    Route::get('/reschedule-package/{packageId}', 'AppointmentController@getPackageDatesById');
    Route::post('/reschedule-package/{id}', 'AppointmentController@reschedulePackage');
    Route::get('/booking', 'Api\BookingController@index');
    Route::put('/booking-approve/{id}', 'Api\BookingController@approveBooking');
    Route::put('/booking-reject/{id}', 'Api\BookingController@rejectBooking');
    Route::put('/booking-cancel/{id}', 'Api\BookingController@cancelBooking');
    Route::post('/booking-checkin/{id}', 'Api\BookingController@punchInBooking');
    Route::post('/course-checkin/{id}', 'Api\BookingController@punchInCourse');
    Route::put('take-leave/{id}', 'Api\BookingController@takeLeave');
    // user-profile
    Route::get('/user', 'Api\UserController@getUserProfile');
    Route::put('/user-password/{id}', 'Api\UserController@changePwd');
    Route::put('/user-notifications/{id}', 'Api\UserController@changeNotifications');
    // trainer-student actions
    Route::apiResource('/trainers', 'Api\TrainerController');
    Route::get('/availability-students/{id}', 'Api\TrainerController@getNotMyStudents');
    // calendar data
    Route::get('/appointments', 'Api\CalendarAppointmentController@index');
    // finance
    Route::get('/payment/{id}', 'Api\PaymentController@show');
    Route::apiResource('/orders', 'Api\PaymentController');
    Route::get('/finance', 'Api\PaymentController@index');
    Route::put('/payment-reminder/{id}', 'Api\PaymentController@sendBillReminder');
    Route::put('/payment/{id}', 'Api\PaymentController@update');
    Route::get('/invoice/{id}', 'Api\PaymentController@showInvoice');
    Route::get('/receipt/{id}', 'Api\PaymentController@showInvoice');
    Route::apiResource('order-commission', 'Api\OrderCommissionController');
    // all user actions
    Route::get('/get-roles', 'Api\RoleController@getRoles');
    Route::get('/roles', 'Api\RoleController@index');
    Route::apiResource('users', 'Api\UserController');
    Route::get('/student-qr/{id}', 'Api\UserController@generatStudentQr');
    Route::put('/ban-user/{id}', 'Api\UserController@banUser');
    Route::put('/active-user/{id}', 'Api\UserController@activeUser');
    Route::post('/register-push', 'Api\UserController@registerPush');

    Route::get('/user-service', 'Api\ServiceController@getUserService');
    // notifications
    Route::apiResource('/notifications', 'Api\NotificationController');
    // simple API
    Route::apiResource('/services', 'Api\ServiceController');
    Route::apiResource('/timeslots', 'Api\TimeslotController');
    Route::apiResource('/packages', 'Api\PackageController');
    Route::apiResource('/daysoff', 'Api\HolidayController');
    Route::post('/copy-timeslots', 'Api\TimeslotController@copyMonday');
    Route::apiResource('/trainer-timeslots', 'Api\TrainerTimeslotController');
    Route::apiResource('/trainer-workdate-timeslots', 'Api\TrainerWorkDateTimeslotController');
    Route::get('/trainer-non-workdate/{trainer_id}', 'Api\TrainerWorkDateTimeslotController@getTrainerNonWorkDates');
    Route::apiResource('/rooms', 'Api\RoomController');
    Route::get('/locations', 'Api\LocationController@index');    //

    //trainer rate
    Route::apiResource('/trainerrates', 'Api\TrainerRateController');
    Route::get('/trainerrates-bystudentid/{user_id}', 'Api\TrainerRateController@listByStudentId');
    // Reporting
    Route::get('/report-sales', 'Api\ReportController@salesReport');
    Route::post('/export-report-sales-xlsx', 'Api\ReportController@exportXlsxSalesReport1');
    Route::get('/report-trainer-commission', 'Api\ReportController@trainersCommissionReport');
    

    Route::get('/export-report-order', 'ReportController@orderReportExport');
});
