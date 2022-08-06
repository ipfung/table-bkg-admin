<?php

namespace App\Http\Controllers;

use App\Models\CustomerBooking;
use App\Models\OrderDetail;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // get user's book days in advance.
        $user = Auth::user();
        $fromDate = Carbon::today()->format("Y-m-d");
        if ($request->has('from_date')) {
            $fromDate = $request->from_date;
        }
        $toDate = Carbon::today()->addDays(7)->format("Y-m-d");
        if ($request->has('to_date')) {
            $toDate = $request->to_date;
        }
        $bookings = DB::table('customer_bookings')
            ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
            ->join('rooms', 'appointments.room_id', '=', 'rooms.id')
            ->select('customer_bookings.*',
                DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'),
                DB::raw('(select payments.status from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_status'),
                'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name')
            ->where('customer_id', $user->id)
            ->where('appointments.start_time', '>=', $fromDate )
            ->where('appointments.end_time', '<=', $toDate )
            ->orderBy('appointments.start_time', 'asc')
            ->orderBy('rooms.name', 'asc')
            ->get();

        if ($request->expectsJson()) {
            return $bookings;
        }
        return view("bookings", $bookings);

    }

    /**
     * @param Request $request
     * @param $id
     * @return array|void
     */
    public function punchInBooking(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id);
//        echo 'booking=' . json_encode($booking);
        // only allow user itself to checkin.
        if ($user->id == $booking->customer_id) {
            if (empty($booking->checkin)) {
                $can_checkin_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->modify('-1 hour');   // 1 hour before appointment start time.
                $booking_end_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->end_time);
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone(env("JWS_TIMEZONE")));
//echo 'can_checkin_time=' . $can_checkin_time->format('Y-m-d H:i:s');
//echo ', booking_end_time=' . $booking_end_time->format('Y-m-d H:i:s');
//echo ', now=' . $now->format('Y-m-d H:i:s');
                if ($now > $can_checkin_time && $now < $booking_end_time) {
                    $booking->checkin = $now->format('Y-m-d H:i:s');
                    $booking->save();
                    $results = ['success' => true, 'checkin' => $booking->checkin];
                } else if ($now < $can_checkin_time) {
                    $results = ['success' => false, 'error' => 'You can checkin within 60 minute before your appointment start time.'];
                } else if ($now > $booking_end_time) {
                    $results = ['success' => false, 'error' => 'Your appointment is ended already. No checkin can be done.'];
                }
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot checkin for others.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }

    }

    /**
     * @param Request $request
     * @param $id
     * @return array|void
     */
    public function takeLeave(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id)->with('appointment');
        // only allow user itself to take leave.
        if ($user->id == $booking->customer_id) {
            if (empty($booking->checkin)) {
                $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time);
                $now = Carbon::today();
                if ($now->isBefore($start_time)) {
                    // ok to take leave
                    if ($booking->revision_counter == 0) {
                        $booking->take_leave_time = $now->format('Y-m-d H:i:s');
                        $booking->revision_counter += 1;
                        $booking->save();
                        $results = ['success' => true];
                    }
                } else {
                    $results = ['success' => false, 'error' => ''];
                }
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot take leave for others.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }

    }

    public function isBookingPaid($id) {
        $user = Auth::user();
        $booking = OrderDetail::where('booking_id', $id);
        return false;
    }
}
