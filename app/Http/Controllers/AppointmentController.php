<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentApproved;
use App\Models\Appointment;
use App\Models\CustomerBooking;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Service;
use App\Models\Holiday;
use App\Models\Timeslot;
use App\Models\TrainerTimeslot;
use App\Models\User;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
     * Display a listing of the available timeslots, minDate & maxDate of booking based on user role.
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
        $EPOCH = 60;
        $roomId = -1;
        if ($request->has('room_id')) {
            $roomId = $request->room_id;
        }
        if ($request->has('the_date')) {
            $minDate = $request->the_date;
            $maxDate = $request->the_date;
        }
        $locationId = 1;          // FIXME from roomId
        $serviceId = 1;
        $appointmentId = 0;       // for reschedule use, bypass the booked appointment so user could book +/- one session time.
        $booking = false;
        $tableSessions = false;
        if ($request->has('bookId')) {    // reschedule.
            $booking = CustomerBooking::find($request->bookId);
            $serviceId = $booking->appointment->service_id;
            // for bypass reschedule.
            $appointmentId = $booking->appointment->id;
            $tableSessions = $booking->appointment->service->sessions;
        }
        $service = Service::find($serviceId);
        // support passing service_id from client side, use service's duration, price...etc.
        if ($request->has('service_id')) {
            if ($request->service_id > 1) {
                $service = Service::find($request->service_id);
            }
        } else {
            // TODO throw error if no service id?
        }
        $sessionMinute = $service->session_min;        // each session minute, from Service settings.
        $sessionDuration = $service->duration;        // default duration, from Service settings.
        $serviceTime = $service->min_duration;        // min minute duration, from Service record.
        $noOfSession = $serviceTime / $sessionDuration;         // minimum session, from Service settings.
        $customerId = -1;
        $price = $service->price;
        if ($request->has('customer_id')) {    // custom appointment(means it's not from appointment wizard)
            $customerId = $request->customer_id;
            $customer = User::find($customerId)->first();  // get price.
            if ($service->price < 0)
                $price = $customer->role->default_price;
        } else {
            if ($service->price < 0)
                $price = $user->role->default_price;              // from Service record, FIXME different user has different price.
        }
        if ($price <= 0) {
            $price = 999;
        }
        $sessionPrice = $price / ($serviceTime / $sessionDuration);
        $sessionIntervalEpoch = $sessionMinute * $EPOCH;
        $sessionDurationEpoch = $sessionDuration * $EPOCH;
        if ($request->has('noOfSession')) {
            if ($request->noOfSession < $noOfSession) {
                // FIXME prompt error if selected sessions less than default session.
            }
            $noOfSession = $request->noOfSession;
            $price = $sessionPrice * $noOfSession;
        } else if ($request->has('bookId') && $booking) {    // reschedule.
            // find number of session.
            $appointedTime = Carbon::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->timestamp;
            $appointedEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $booking->appointment->end_time)->timestamp;
            $noOfSession = ($appointedEndTime - $appointedTime) / $sessionDurationEpoch;
            $price = $sessionPrice * $noOfSession;
//        echo 'bookId noOfSession, price=' . $price . ', ' . $noOfSession;
        }
//        echo 'dayOfWeek_timeslots=' . $dayOfWeek_timeslots;
        // create a TODAY 0:00 epoch.
        $today = Carbon::today()->timestamp;
        $sessionToBeBooked = ($noOfSession * $sessionDurationEpoch);   // client selected session * each session, in epoch.
        $trainerId = 0;
        // 2-dimension array per week_number.
        $freeTimesolts = array(array());
        if ($request->has('trainer_id')) {
            $trainerId = $request->trainer_id;
            $dayOfWeek_timeslots = TrainerTimeslot::where('location_id', $locationId)
                ->where('trainer_id', $trainerId)
                ->orderBy('day_idx', 'asc')
                ->orderBy('from_time', 'asc')
                ->get();
        } else {
            $dayOfWeek_timeslots = Timeslot::where('location_id', $locationId)
                ->orderBy('day_idx', 'asc')
                ->orderBy('from_time', 'asc')
                ->get();
        }
        foreach ($dayOfWeek_timeslots as $key => $dow) {
//            echo 'key=' . $key;
//            echo '<br />' . $dow->day_idx . ': ' . $dow->from_time . ' to ' . $dow->to_time;
            // office start & end.
            $sTime = Carbon::createFromTimeString($dow->from_time);
            $eTime = Carbon::createFromTimeString($dow->to_time);
            if ($eTime->timestamp < $sTime->timestamp) {    // end time is after midnight(next day of start time).
                $eTime = Carbon::createFromTimeString($dow->to_time)->addDay();
//                echo '<br />' . $sTime->timestamp . ' ------ ' . $eTime->timestamp;
            }

            $startTime = ($sTime->timestamp - $today);
            // support last session. $noOfSession * $sessionInterval * $EPOCH = office end - last session.
            $endTime = ($eTime->timestamp - $today) - $sessionToBeBooked;
//            echo '<br />stime=' . $startTime . ', etime=' . $endTime;
            // get timeslot session
            $DAY_EPOCH = 24 * 60 * 60;
            while ($startTime <= $endTime) {
                $freeTimesolts[$dow->day_idx][] = ['time' => $startTime, 'price' => $price];
                if (($startTime / $DAY_EPOCH) >= 1) {   // after midnight, add to next day as well.
                    $nextDayOfWeek = $dow->day_idx + 1;
                    if ($dow->day_idx > 7) {   // Sunday
                        $nextDayOfWeek = 1;    // Monday
                    }
                    // add to next day.
//            echo '<br />nextDayOfWeek=' . $nextDayOfWeek . ', etime=' . ($startTime - $DAY_EPOCH);
                    $freeTimesolts[$nextDayOfWeek][] = ['time' => ($startTime - $DAY_EPOCH), 'price' => $price];
                }
                $startTime += $sessionIntervalEpoch;
//                echo ', starttime=' . $startTime . '!';
            }
//            echo ', $freeTimesolts[$dow->day_idx]=' . json_encode($freeTimesolts[$dow->day_idx]) . '!';
        }
//
//        // TODO get appointed timeslot by minDate and maxDate.
//        $nextDayOfMaxDate = Carbon::parse($maxDate)->addDay();
//        $appointments = Appointment::orderBy('start_time', 'asc')
//            ->whereIn('status', ['approved', 'pending'])
//            ->where('start_time', '>=', $minDate)
//            ->where('end_time', '<=', $nextDayOfMaxDate);
//        if ($roomId > 0) {
//            $appointments->where('room_id', $roomId);
//        }
//        if ($appointmentId > 0) {   // bypass for reschedule.
//            $appointments->where('id', '<>', $appointmentId);
//        }
//        if ($trainerId > 0) {   // bypass trainer.
//            $appointments->where('user_id', '<>', $trainerId);
//        }
//        $appointments = $appointments->get();
////echo 'appointed=' . $nextDayOfMaxDate . json_encode($appointments);
//
//        // convert appointed to time epoch.
//        $appointedEpoch = [];
//        foreach ( $appointments as $appointed ) {
//            $appointedTime = Carbon::createFromFormat('Y-m-d H:i:s', $appointed->start_time)->timestamp;
//            $appointedEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $appointed->end_time)->timestamp;
//            $totalAppointedSessions = (($appointedEndTime - $appointedTime) / $sessionIntervalEpoch);
////echo 'appointed totalAppointedSessions=' . $totalAppointedSessions . '....';
//            // convert appointed start_time to end_time to each session time epoch.
//            for ($i = 0; $i < $totalAppointedSessions; $i++) {
////echo ', =' . ($appointedTime + ($i * $sessionIntervalEpoch));
//                $appointedEpoch[] = ($appointedTime + ($i * $sessionIntervalEpoch));
//            }
//            // also need to block time of appointed' start_time - $sessionToBeBooked.
//            for ($i = 1; $i < $noOfSession; $i++) {
////echo ', ===' . ($appointedTime + ($i * $sessionIntervalEpoch));
//                $appointedEpoch[] = ($appointedTime - ($i * $sessionIntervalEpoch));
//            }
//        }
////echo 'appointed epoch=' . json_encode($appointedEpoch);

        $all_rooms = Room::orderBy('name', 'asc')
            ->where('status', 1001)
//            ->limit(2)   // FIXME debug use only.
            ->get();
        $start_date = strtotime($minDate);   // to epoch.
        $end_date = strtotime($maxDate);
//        echo '<br/>maxDate=' . $maxDate;
        $d = new Carbon($start_date);
        $dateFreeslots = [];
        // ref: https://tecadmin.net/php-loop-between-two-dates/#:~:text=PHP%20Loop%20Between%20Two%20Dates%20with%20Alternate%20Dates%3A&text=%3C%3F-,php%20date_default_timezone_set('UTC')%3B%20%24start_date%20%3D%20'2015%2D01,)))%3B%20%7D%20%3F%3E
        while ($start_date <= $end_date) {
            // TODO check if it's special day/holiday.
            // check office days off.
            $daysoff = Holiday::where('location_id', $locationId)->whereRaw('(? between start_date and end_date)', $d->format('Y-m-d'))->first();
            if (!empty($daysoff)) {
                $isDayOff = true;
                $freeslots = [];
            } else {
                // get freeslot from week_number freeslot.
                $freeslots = $freeTimesolts[$d->dayOfWeek];    // it contains 'time', 'price'.
                $isDayOff = (sizeof($freeslots) == 0);
                // TODO remove occupied time.
                foreach ($freeslots as $index => $slot) {
//echo "s3=" . ($start_date + $slot["time"]);
                    $dateTimeEpoch = $start_date + $slot["time"];
//echo "<br />dateTimeEpoch0000=" . $dateTimeEpoch;
                    $dt = (new DateTime("@$dateTimeEpoch"))->format('Y-m-d H:i:s');
                    $endTime = $dateTimeEpoch + ($noOfSession * $sessionMinute);
                    $dt2 = (new DateTime("@$endTime"))->format('Y-m-d H:i:s');
//echo "<br />startTime0=" . $dt . ', en0=' . $dt2;
                    $allRoomOccupied = true;
                    foreach ($all_rooms as $room) {
//echo "roomid2222=" . $room->id;
                        if (!$this->isRoomOccupied($room->id, $dt, $dt2)) {   // false = not occupied.
                            $allRoomOccupied = false;
                            break;
                        }
                    }
                    if ($allRoomOccupied) {
                        unset($freeslots[$index]);
                    }
//echo "slot_time====" . $slot_time;
                }
                $freeslots = array_values($freeslots);   // ref: https://stackoverflow.com/questions/369602/deleting-an-element-from-an-array-in-php
            }
            // TODO remove time that is less than selected sessions.
            // the date & its availability.
            $dateFreeslots[] = ['date' => $d->format('Y-m-d'), 'freeslots' => $freeslots, 'dayoff' => $isDayOff];
            // increment 1 day for next iterate.
            $d->addDay();
            $start_date = $d->timestamp;
        }
        $results = ['minDate' => $minDate, 'maxDate' => $maxDate, 'noOfSession' => $noOfSession, 'sessionInterval' => $sessionDurationEpoch, 'tableSessions' => $tableSessions, 'data' => $dateFreeslots];
//        $results['role'] = $user;   // debug use only
//        echo '<br/>result=' . json_encode($results) . '!';
        if ($request->expectsJson()) {
            return $results;
        }
//        $appointmentDate = new Carbon("2022-08-02");
//        echo "epoch=" . $appointmentDate->timestamp;
//        echo "<br />format=" . $appointmentDate->format('Y-m-d');
//        $startTime2 = $appointmentDate->timestamp + 72000;
//        $dt = new DateTime("@$startTime2");
//        echo "<br />startTime2=" . $dt->format('Y-m-d H:i:s');
        return view("appointment", $results);

    }

    /**
     * @param $user get minDate & maxDate based on user level.
     * @return array
     */
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

    /**
     * @param $roomId
     * @param $startTime
     * @param $endTime
     * @return bool true = occupied, false = not occupied.
     */
    private function isRoomOccupied($roomId, $startTime, $endTime)
    {
        $chkDup = Appointment::where('room_id', $roomId)
            ->whereIn('status', ['approved', 'pending'])
            // ref: https://stackoverflow.com/questions/6571538/checking-a-table-for-time-overlap
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
//            ->whereRaw('(? between start_time1 and end_time OR ? between start_time and end_time)', [$startTime, $endTime])
            ->get();
        return count($chkDup) > 0;
    }

    public function store(Request $request)
    {
        // get user's book days in advance.
        $user = Auth::user();
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|integer',
            'noOfSession' => 'required|integer',
            'roomId' => 'required|integer',
            'serviceId' => 'required|integer',
            'price' => 'required',
//            'paymentMethod' => 'required',
//            'order_status' => 'required',
        ]);
        $assignRandomRoom         = true;   // can get from Company settings.
        // get appointment dates.
        $appointmentDates = $this->getAppointmentDates($user, $request->date, $request->time, $request->noOfSession, $request->sessionInterval, $request->room_id, $assignRandomRoom);

        $assignedRoom = $appointmentDates['room_id'];
        $results = [];
        if ($assignedRoom <= 0) {
            // appointment time not available, throw error.
            $results = ['success' => false, 'error' => 'Time not available, please choose different time.', 'param' => $assignedRoom];
            return $results;
        }

        // use trainer_id as appointment user, if the appointment is trainer-student relationship.
        $userId = $user->id;
        if ($request->has('trainerId')) {
            if ($request->trainerId > 0) {
                $userId = $request->trainerId;
            }
        }
        // check if user is new, make appointment status to 'pending' instead.
        $bookedAppointments = Appointment::orderBy('start_time', 'desc')->where('user_id', $user->id)->limit(10)->get();
        $isFirstTimeBookUser = true;
        foreach ($bookedAppointments as $bookedAppointment) {
            if ($bookedAppointment->status == 'approved') {
                $isFirstTimeBookUser = false;
            }
        }

        // start DB transaction.
        DB::beginTransaction();

        $appointment = new Appointment();
        $appointment->start_time = $appointmentDates['start_time'];
        $appointment->end_time = $appointmentDates['end_time'];
        $appointment->room_id = $appointmentDates['room_id'];
        $appointment->user_id = $userId;
        $appointment->service_id = $request->serviceId;
//        $appointment->package_id
//        $appointment->lesson_space
//        $appointment->internal_remark
        $appointment->status = $isFirstTimeBookUser ? 'pending' : 'approved';     // get defaults from settings.
//        $appointment->parent_id

        // check duplicate, in Appointment system should not allow same user book same timeslot.
        $isDup = false;
        for ($i=0; $i<2; $i++) {
            $paramDate = $i == 0 ? $appointmentDates['start_time'] : $appointmentDates['end_time'];
            $found = DB::table('customer_bookings')
                ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
                ->where('customer_bookings.customer_id', $user->id)
                ->whereRaw('? between appointments.start_time and appointments.end_time', $paramDate)->first();
            if (!empty($found)) {
                $isDup = true;
                break;
            }
        }
        if ($isDup) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return ['success' => false, 'error' => 'Found duplicate appointment.'];
            }
        }
        $appointment->save();

        $customerBooking = new CustomerBooking();
        $customerBooking->appointment_id = $appointment->id;
        $customerBooking->customer_id = $user->id;
        $customerBooking->price = $request->price;
        $customerBooking->info = json_decode($request->personalInformation);    // if any.
        $customerBooking->revised_appointment_id = $appointment->id;
        $customerBooking->revision_counter = 0;
        $customerBooking->save();

        $order = new Order();
        $order->order_number = uniqid();
        $order->order_date = Carbon::today()->format('Y-m-d');
        $order->order_total = $request->price;
        if ($request->has('discount')) {
            if ($request->discount > 0)
                $order->discount = $request->discount;
        }
        $order->customer_id = $customerBooking->customer_id;
        $order->user_id = $user->id;
        $order->paid_amount = $appointment->status == 'approved' ? $order->order_total : 0;
        $order->payment_status = $appointment->status == 'approved' ? 'paid' : 'pending';       // FIXME get payment status from gateway response.
        $order->order_status = $appointment->status == 'approved' ? 'confirmed' : 'pending';
        $order->save();

        $orderDetail = new OrderDetail();
        $orderDetail->order_id = $order->id;
        $orderDetail->order_type = 'booking';
        $orderDetail->booking_id = $customerBooking->id;
        $orderDetail->order_description = json_encode($appointment);
        $orderDetail->original_price = $request->price;
        $orderDetail->discounted_price = $request->price;
        $orderDetail->coupon_id = $request->coupon_id;
        $orderDetail->save();

        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->amount = $order->order_total;
        $payment->payment_date_time = (new DateTime())->format('Y-m-d H:i:s');
        $payment->status = $order->payment_status;
        $payment->payment_method = 'electronic';
        $payment->gateway = $request->paymentMethod;
//        $payment->parent_id = ;
        $payment->entity = 'appointment';
        $payment->save();

        Mail::to($user->email)
            ->bcc(config('mail.from.address'))
            ->send(new AppointmentApproved(CustomerBooking::find($customerBooking->id)));

        DB::commit();

        if ($request->expectsJson()) {
            $results = ['success' => true];
            return $results;
        }
        return redirect()->route('orders.index');
    }

    /**
     * @param Request $request
     * @param $id
     * @return array|void
     */
    public function reschedule(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id);
//        echo 'reschedule id222=' . json_encode($booking);
//        echo 'reschedule id333=' . $booking->appointment->user_id;

        // only allow user itself to reschedule their own appointment(customer booking maybe pointed to package/course).
        if ($user->id != $booking->appointment->user_id) {
            if ($booking->appointment->entity == 'appointment') {
                return ['success' => false, 'error' => 'You cannot reschedule for others.'];
            }
        }
        if (!empty($booking->checkin)) {
            return ['success' => false, 'error' => 'You have checked-in.'];
        }
        if ($booking->revision_counter > 0) {
            return ['success' => false, 'error' => 'You have been rescheduled several times.', 'params' => $booking->revision_counter];
        }
        // can amend 48 hours before appointment start time.
        $can_amend_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->modify('-48 hours');
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(config("app.jws.local_timezone")));   // must set timezone, otherwise the punch-in time use UTC(app.php) and can't checkin.
        if ($now < $can_amend_time) {   // now is 48 hours before appointment start time.
            // ok to change booking once.
            // get appointment dates.
            $appointmentDates = $this->getAppointmentDates($user, $request->date, $request->time, $request->noOfSession, $request->sessionInterval, $request->room_id, true);
            // check duplicate, in Appointment system should not allow same user book same timeslot.
            $isDup = false;
            for ($i=0; $i<2; $i++) {
                $paramDate = $i == 0 ? $appointmentDates['start_time'] : $appointmentDates['end_time'];
                $found = DB::table('customer_bookings')
                    ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
                    ->where('customer_bookings.customer_id', $user->id)
                    ->whereRaw('? between appointments.start_time and appointments.end_time', $paramDate)->first();
                if (!empty($found)) {
                    $isDup = true;
                    break;
                }
            }
            if ($isDup) {
                if ($request->expectsJson()) {
                    return ['success' => false, 'error' => 'Found duplicate appointment.'];
                }
            }
            $booking->appointment->start_time = $appointmentDates['start_time'];
            $booking->appointment->end_time = $appointmentDates['end_time'];
            $booking->appointment->room_id = $appointmentDates['room_id'];
            $booking->appointment->save();
            $booking->revision_counter += 1;
            $booking->save();
            $results = ['success' => true, 'room' => Room::find($booking->appointment->room_id)];
            // TODO mail
        } else {
            $results = ['success' => false, 'error' => 'You must reschedule before 48 hours of appointment start time.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }

    }

    private function getAppointmentDates($user, $date, $time, $noOfSession, $sessionInterval, $room_id, $assignRandomRoom) {
        // get min & max dates by user
        $dates = $this->getDates($user);
        $minDate = $dates[0];
        $maxDate = $dates[1];
        $appointmentDate = new Carbon($date);
        $dateOk = $appointmentDate->between($minDate, $maxDate);
        if (!$dateOk) {
            // FIXME throw error in case someone hack the appointment date.

        }
        $startTime = $appointmentDate->timestamp + $time;
        $dt = (new DateTime("@$startTime"))->format('Y-m-d H:i:s');
//        echo "<br />startTime2=" . $dt;
        $endTime = $appointmentDate->timestamp + $time + ($noOfSession * $sessionInterval);
        $dt2 = (new DateTime("@$endTime"))->format('Y-m-d H:i:s');
//        echo "<br />startTime3=" . $dt2;

        // Room availability checking.
        $assignedRoom = -1;
        if ($assignRandomRoom) {
            // support to assign dynamic room.
            $rooms = Room::inRandomOrder()->where('status', 1001)->get();   // no need to orderBy, let it return randomly.
            foreach ($rooms as $room) {
                $isRoomOccupied = $this->isRoomOccupied($room->id, $dt, $dt2);
                if (!$isRoomOccupied) {
                    $assignedRoom = $room->id;
                    break;   // exit foreach rooms.
                }
            }
        } else {
            // check duplicate by roomId and appointment time.
            $assignedRoom = $room_id;   // param from client side.
            $isRoomOccupied = $this->isRoomOccupied($assignedRoom, $dt, $dt2);
            if ($isRoomOccupied) {   // reset $assignedRoom to negative number if desired room was is occupied.
                $assignedRoom = -2;
            }
        }
        return [
            'start_time' => $dt,
            'end_time' => $dt2,
            'room_id' => $assignedRoom
        ];
    }
}
