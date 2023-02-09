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
            'appointment_date'       => $start_date->format(BaseController::$dateFormat),       // yyyy-MM-dd
            'appointment_date_time'  => $start_date->format(BaseController::$dateTimeFormat),   // 24-hour format
            'appointment_start_time' => $start_date->format(BaseController::$timeFormat),       // 12-hour format with am/pm
            'appointment_end_time'   => $end_date->format(BaseController::$timeFormat),
            'company_name'   => $appointment->room->location->company->name,
            'service_name'   => $appointment->service->name,
            'room_name'   => $appointment->room->name,
            'location_name'   => $appointment->room->location->name,
            'location_address'   => $appointment->room->location->name,
            'customer_checkin'   => $customerBooking->checkin,
            'customer_checkout'   => $customerBooking->checkout,
            'customer_take_leave_time'   => $customerBooking->take_leave_at,
            'customer_name'   => $customerBooking->customer->name,
            'customer_second_name'   => $customerBooking->customer->second_name,
            'customer_mobile'   => $customerBooking->customer->mobile_no,
            'customer_email'   => $customerBooking->customer->email,
            'trainer_name'   => $appointment->user ? $appointment->user->name : '',
            'trainer_second_name'   => $appointment->user ? $appointment->user->second_name : '',
            'trainer_mobile'   => $appointment->user ? $appointment->user->mobile_no : '',
            'trainer_email'   => $appointment->user ? $appointment->user->email : '',
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

    public function getOrderData($order, $type = 'email') {
        if ($order->package) {
            $liStartTag = $type === 'email' ? '<li>' : '';
            $liEndTag = $type === 'email' ? '</li>' : ($type === 'whatsapp' ? '; ' : PHP_EOL);
            $ulStartTag = $type === 'email' ? '<ul>' : '';
            $ulEndTag = $type === 'email' ? '</ul>' : '';

            $lesson_dates = [];
            $lesson_date_times = [];
            foreach ($order->details as $detail) {
                $description = json_decode($detail->order_description);
                $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $description->start_time);
                $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $description->end_time);

                $startDateString = $startDateTime->format(BaseController::$dateFormat);
                $endDateString   = $endDateTime->format(BaseController::$dateFormat);

                $startDateTimeString = $startDateTime->format(BaseController::$dateTimeFormat);
                $endDateTimeString   = $endDateTime->format(BaseController::$dateTimeFormat);

                $packageStartTime = $startDateTime->format(BaseController::$timeFormat);
                $packageEndTime   = $endDateTime->format(BaseController::$timeFormat);

                $dateString = $startDateString === $endDateString ?
                    $startDateString :
                    $startDateString . ' - ' . $endDateString;
                $dateTimeString = $startDateString === $endDateString ?
                    $startDateString . ' (' . $packageStartTime . ' - ' . $packageEndTime . ')' :
                    $startDateString . ' - ' . $endDateString . ' (' . $packageStartTime . ' - ' . $packageEndTime . ')';

                $lesson_dates[]     = "$liStartTag{$dateString}$liEndTag";
                $lesson_date_times[]     = "$liStartTag{$dateTimeString}$liEndTag";

            }
        }
        return [
            'order_id'         => $order->id,
            'order_number'     => $order->order_number,
            'order_status'     => $order->order_status,
            'order_total'     => $order->order_total,
            'payment_status'     => $order->payment_status,
            'order_date'         => $order->order_date,       // yyyy-MM-dd
            'company_name'   => $order->location->company->name,
            'location_name'   => $order->location->name,
            'location_address'   => $order->location->name,
            'customer_name'   => $order->customer->name,
            'customer_second_name'   => $order->customer->second_name,
            'customer_mobile'   => $order->customer->mobile_no,
            'customer_email'   => $order->customer->email,
            'package_name'     => $order->package ? $order->package->name : '',
            'lesson_period_dates'     => $ulStartTag . implode('', $lesson_dates) . $ulEndTag,
            'lesson_period_date_time'   => $ulStartTag . implode('', $lesson_date_times) . $ulEndTag,
        ];
    }

    public function getPackageData(Order $order) {

        return $order;
    }

    /**
     * @param string $text
     * @param array  $data
     *
     * @return mixed
     */
    public function applyPlaceholders($text, $data)
    {
        unset($data['icsFiles']);

        $placeholders = array_map(
            function ($placeholder) {
                return "%{$placeholder}%";
            },
            array_keys($data)
        );

        return str_replace($placeholders, array_values($data), $text);
    }
}
