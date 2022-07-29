<?php

namespace App\Http\Controllers;

use App\Models\Timeslot;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
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
        // get min & max dates by user
        $dates = $this->getDates($user);
        $minDate = $dates[0];
        $maxDate = $dates[1];
        // get timeslot
        $EPOCH = env("JWS_EPOCH");
        $freeTimesolts = array();
        $dayOfWeek_timeslots = Timeslot::all();
        $sessionInterval = 30;    // minute, from Location settings.
        $noOfSession = 2;         // minimum session, from Location settings.
        $serviceTime = 60;        // minute, from Service(Room/Table) record.
        if ($request->has('noOfSession')) {
            $noOfSession = $request->noOfSession;
        }
//        echo 'dayOfWeek_timeslots=' . $dayOfWeek_timeslots;
        // create a TODAY 0:00 epoch.
        $today = Carbon::today()->timestamp;
        foreach ($dayOfWeek_timeslots as $key => $dow) {
//            echo 'key=' . $key;
//            echo '<br />' . $dow->day_idx . ': ' . $dow->from_time . ' to ' . $dow->to_time;
            // 2-dimension array per week_number.
            $freeTimesolts[$dow->day_idx] = array();
            // office start
            $sTime = Carbon::createFromTimeString($dow->from_time);
            $startTime = ($sTime->timestamp - $today);
            // office end
            $eTime = Carbon::createFromTimeString($dow->to_time);
            // support last session. $noOfSession * $sessionInterval * $EPOCH = office end - last session.
            $endTime = ($eTime->timestamp - $today) - ($noOfSession * $sessionInterval * $EPOCH);
//            echo '<br />stime=' . $startTime . ', etime=' . $endTime;
            // get timeslot session
            while ($startTime <= $endTime) {
                $freeTimesolts[$dow->day_idx][] = $startTime;
                $startTime += ($sessionInterval * $EPOCH);
//                echo ', starttime=' . $startTime . '!';
            }
//            echo ', $freeTimesolts[$dow->day_idx]=' . json_encode($freeTimesolts[$dow->day_idx]) . '!';
        }

        // TODO get appointed timeslot by minDate and maxDate.


        $start_date = strtotime($minDate);
        $end_date = strtotime($maxDate);
//        echo '<br/>maxDate=' . $maxDate;
        $d = new Carbon($start_date);
        $dateFreeslots = [];
        // ref: https://tecadmin.net/php-loop-between-two-dates/#:~:text=PHP%20Loop%20Between%20Two%20Dates%20with%20Alternate%20Dates%3A&text=%3C%3F-,php%20date_default_timezone_set('UTC')%3B%20%24start_date%20%3D%20'2015%2D01,)))%3B%20%7D%20%3F%3E
        while ($start_date <= $end_date) {
            // TODO check if it's special day/holiday.
            // get freeslot from week_number freeslot.
            $freeslots = $freeTimesolts[$d->dayOfWeek];
            // TODO remove occupied time.
            // TODO remove time that is less than selected sessions.
            // the date & its availability.
            $dateFreeslots[] = ['date' => $d->format('Y-m-d'), 'freeslots' => $freeslots];
            // increment 1 day for next iterate.
            $d->addDay();
            $start_date = $d->timestamp;
        }
        $results = ['minDate' => $minDate, 'maxDate' => $maxDate, 'data' => $dateFreeslots];
//        $results['role'] = $user;   // debug use only
//        echo '<br/>result=' . json_encode($results) . '!';
        if ($request->expectsJson()) {
            return $results;
        }
        return view("appointment", $results);

    }

    private function getDates($user) {
        $today = CarbonImmutable::now();
        if ($user) {
            $minDate = $today->add(1, 'day')->format('Y-m-d');
            $maxDate = $today->add($user->role->book_days_in_adv, 'day')->format('Y-m-d');
        } else {
            $minDate = $today->add(1, 'day')->format('Y-m-d');
            $maxDate = $today->add(1, 'day')->format('Y-m-d');
        }
        return [$minDate, $maxDate];
    }
}
