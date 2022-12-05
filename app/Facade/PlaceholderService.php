<?php

namespace App\Facade;

use App\Http\Controllers\Api\BaseController;
use App\Models\Appointment;
use App\Models\CustomerBooking;
use App\Models\Order;
use DateTime;
use Illuminate\Support\Carbon;

class PlaceholderService
{
    const placeholders = [
        'service_name',
        'trainer_name',
        'trainer_mobile',
        'trainer_email',
        'customer_name',
        'customer_second_name',
        'customer_mobile',
        'customer_email',
        'appointment_date',
        'appointment_start_time',
        'appointment_end_time'
    ];

    public function getAppointmentData($customerBooking) {
        $appointment = $customerBooking->appointment;
        $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $appointment->start_time);
        $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $appointment->end_time);
        return [
            'appointment_id'         => !empty($appointment['id']) ? $appointment['id'] : '',
            'appointment_status'     => $appointment['status'],
            'appointment_notes'      => !empty($appointment['internalNotes']) ? $appointment['internalNotes'] : '',
            'appointment_date'       => $start_date->format(BaseController::$dateFormat),
            'appointment_date_time'  => $start_date->format(BaseController::$dateTimeFormat),
            'appointment_start_time' => $start_date->format(BaseController::$timeFormat),
            'appointment_end_time'   => $end_date->format(BaseController::$timeFormat),
            'customer_name'   => $customerBooking->customer->name,
            'customer_second_name'   => $customerBooking->customer->second_name,
            'customer_mobile'   => $customerBooking->customer->mobile_no,
            'customer_email'   => $customerBooking->customer->email,
            'trainer_name'   => $appointment->trainer ? $appointment->trainer->name : '',
            'trainer_second_name'   => $appointment->trainer ? $appointment->trainer->second_name : '',
            'trainer_mobile'   => $appointment->trainer ? $appointment->trainer->mobile_no : '',
            'trainer_email'   => $appointment->trainer ? $appointment->trainer->email : '',
//            'lesson_space_url'       => $lessonSpaceLink,
//            'zoom_host_url'          => $zoomStartUrl && $type === 'email' ?
//                '<a href="' . $zoomStartUrl . '">' . BackendStrings::getCommonStrings()['zoom_click_to_start'] . '</a>'
//                : $zoomStartUrl,
//            'zoom_join_url'          => $zoomJoinUrl && $type === 'email' ?
//                '<a href="' . $zoomJoinUrl . '">' . BackendStrings::getCommonStrings()['zoom_click_to_join'] . '</a>'
//                : $zoomJoinUrl,
//            'google_meet_url'        => $googleMeetUrl,
        ];
    }

    public function getPackageData(Order $order) {

        return $order;
    }
}
