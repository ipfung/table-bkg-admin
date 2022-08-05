<?php

namespace App\Http\Controllers;

use App\Models\CustomerBooking;
use Carbon\Carbon;
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

}
