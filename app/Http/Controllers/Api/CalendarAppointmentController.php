<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarAppointmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // date range search.
        $fromDate = Carbon::today()->subDays(30)->format($this->dateFormat);
        if ($request->has('from_date')) {
            $fromDate = $request->from_date;
        }
        $toDate = Carbon::today()->addDays(30)->format($this->dateFormat);
        if ($request->has('to_date')) {
            $toDate = $request->to_date;
        }

        DB::enableQueryLog(); // Enable query log
        $appointments = Appointment::orderBy('start_time', 'asc')
            ->join('rooms', 'appointments.room_id', '=', 'rooms.id')
            ->select('appointments.id',
                DB::raw('rooms.name as title'),
                DB::raw('rooms.color'),
                DB::raw('appointments.start_time as start'),
                DB::raw('appointments.end_time as end')
            )
            ->where('start_time', '>=', $fromDate )
            ->where('end_time', '<=', $toDate )
            ->whereIn("appointments.status", ['approved', 'pending']);   // pending also classify as booked.
        if ($this->isSuperLevel($user)) {
            if ($request->has('user_id')) {
                $appointments->where('user_id', $request->user_id);
            }
        } else {
            $appointments->where('user_id', $user->id);
        }

        if ($request->has('room_id')) {
            if ($request->room_id > 0)
                $appointments->where('room_id', $request->room_id);
        }

        if ($request->has('service_id')) {
            if ($request->service_id > 0)
                $appointments->where('service_id', $request->service_id);
        }

        if ($request->has('package_id')) {
            if ($request->package_id > 0)
                $appointments->where('package_id', $request->package_id);
        }

        // always return JSON type for calendar.
        return $appointments->paginate();
    }

}
