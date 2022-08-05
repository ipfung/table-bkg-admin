<?php

namespace App\Http\Controllers;

use App\Models\CustomerBooking;
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
            ->select('customer_bookings.*', DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'), 'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name')
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
                $can_checkin_time = Carbon::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->subHour();   // 1 hour before appointment start time.
                $booking_end_time = Carbon::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time);
                $now = Carbon::now();
                if ($now->between($can_checkin_time, $booking_end_time)) {
                    $booking->checkin = $now->format('Y-m-d H:i:s');
                    $booking->save();
                    $results = ['success' => true, 'checkin' => $booking->checkin];
                } else if ($now->isBefore($can_checkin_time)) {
                    $results = ['success' => false, 'error' => 'You can checkin within 60 minute before your appointment start time.'];
                } else if ($now->isAfter($booking_end_time)) {
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
}
