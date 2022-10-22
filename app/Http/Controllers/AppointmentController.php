<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentApproved;
use App\Mail\PackageApproved;
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
        $noOfSession = $service->no_of_session;         // minimum session, from Service settings.
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
        $sessionPrice = $price / $noOfSession;
        $sessionIntervalEpoch = $service->session_minute_epoch;
        $sessionDurationEpoch = $service->duration_epoch;
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
        $sessionToBeBooked = ($noOfSession * $sessionDurationEpoch);   // client selected session * each session, in epoch.
        $trainerId = 0;
        // 2-dimension array per week_number.
        $dayOfWeek_timeslots = [];
        if ($request->has('trainer_id') && $request->trainer_id > 0) {
            $trainerId = $request->trainer_id;
            $dayOfWeek_timeslots = TrainerTimeslot::where('location_id', $locationId)
                ->where('trainer_id', $trainerId)
                ->orderBy('day_idx', 'asc')
                ->orderBy('from_time', 'asc')
                ->get();
            if (count($dayOfWeek_timeslots) == 0 && config("app.jws.settings.required_trainer")) {
                return ["success" => false, "error" => "No trainer working hours have found."];
            }
        }
        // get office working hours, if no working hours from trainer_id.
        if (count($dayOfWeek_timeslots) == 0) {
            $dayOfWeek_timeslots = Timeslot::where('location_id', $locationId)
                ->orderBy('day_idx', 'asc')
                ->orderBy('from_time', 'asc')
                ->get();
        }
        $freeTimesolts = $this->convertTimeslot($dayOfWeek_timeslots, $sessionToBeBooked, $sessionIntervalEpoch, $price);

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
                // TODO check if it's special day/holiday of Trainer.
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
                    if ($trainerId > 0) {   // use trainer to check occupation.
                        if (!$this->isTrainerOccupied($trainerId, $dt, $dt2)) {   // false = not occupied.
                            $allRoomOccupied = false;
                        }
                    } else {
                        foreach ($all_rooms as $room) {
//echo "roomid2222=" . $room->id;
                            if (!$this->isRoomOccupied($room->id, $dt, $dt2)) {   // false = not occupied.
                                $allRoomOccupied = false;
                                break;
                            }
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
     * @param $trainerId
     * @param $startTime
     * @param $endTime
     * @return bool true = occupied, false = not occupied.
     */
    private function isTrainerOccupied($trainerId, $startTime, $endTime)
    {
        $chkDup = Appointment::where('user_id', $trainerId)
            ->whereIn('status', ['approved', 'pending'])
            // ref: https://stackoverflow.com/questions/6571538/checking-a-table-for-time-overlap
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
//            ->whereRaw('(? between start_time1 and end_time OR ? between start_time and end_time)', [$startTime, $endTime])
            ->get();
        return count($chkDup) > 0;
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

    /**
     * for endpoint /package-timeslots
     * @param Request $request
     * @return array
     */
    public function getPackageTimeslots(Request $request) {
        $service = Service::find($request->service_id);
        $sessionToBeBooked = ($request->noOfSession * $service->durationEpoch);   // client selected session * each session, in epoch.
        $sessionInterval = $service->duration_epoch;
        $data = [];
        // use param date to find week number(1-7)
        if ($request->has('date')) {
            if ($request->date != '') {
                $date = new Carbon($request->date);
                $dow = $date->isoWeekday();
                $timeslots = Timeslot::orderBy('day_idx', 'asc')
                    ->orderBy('from_time', 'asc')
                    ->where('location_id', 1)
                    ->where('day_idx', $dow)
                    ->get();
                $data = $this->convertTimeslot($timeslots, $sessionToBeBooked, $service->sessionMinuteEpoch, 0)[$dow];
            }
        }
        return compact('data', 'sessionInterval');
    }

    /**
     * for endpoint /package-dates
     * @param Request $request
     * @return array
     */
    public function getPackageDates(Request $request) {
        $locationId = 1;
        // use Date and WeekNo to find the timeslot.
        $d1 = Carbon::createFromFormat('Y-m-d', $request->start_date);
        $quantity = $request->quantity;
        $dow_list = $request->dow;   // array day of week.

        // loop once to find the closest dow from start_date.
        $d2 = Carbon::createFromFormat('Y-m-d', $request->start_date);
        $first_dow = null;
        if (sizeof($dow_list) > 1) {
            sort($dow_list);
            while ($first_dow == null) {
                foreach ($dow_list as $dow) {
                    // the start_date is not the first element of dow_list.
                    if ($d2->is(Timeslot::WEEKS[$dow])) {
                        $first_dow = $dow;
                        break;
                    }
                }
                if ($first_dow == null) {
                    $d2->addDay();
                }
            }
            // set d1 = d2.
            $d1 = $d2;
            // reorder array
            $newdow_list = [];
            $start_dow_list = [];
            foreach ($dow_list as $dow) {
                if ($dow == $first_dow || sizeof($newdow_list) > 0) {
                    array_push($newdow_list, $dow);
                    if (sizeof($newdow_list) == sizeof($dow_list)) {
                        break;
                    }
                } else {
                    array_push($start_dow_list, $dow);
                }
            }
            $dow_list = array_merge($newdow_list, $start_dow_list);
//echo 'd2==' . $d2->format('Y-m-d') . ', newdow_list=' . json_encode($dow_list) . ', first_dow=' . $first_dow;
        }

        $i = 0;
        $data = [];
        $holidays = [];
        while ($i < $quantity) {
            $j = 0;    // to compare if no date can be obtained from $dow_list.
            foreach ($dow_list as $dow) {
                $d1 = $d1->is(Timeslot::WEEKS[$dow]) ? $d1 : $d1->next(Timeslot::WEEKS[$dow]);
                // check date is public holiday for the office?
                $daysoff = Holiday::where('location_id', $locationId)->whereRaw('(? between start_date and end_date)', $d1->format('Y-m-d'))->first();
                if (!empty($daysoff)) {
                    // is dayoff, add one day and go to next dow.
                    $holidays[] = ["date" => $d1->format('Y-m-d'), "dow" => $dow];
                    $d1->addDay();
                    continue;
                }
                // check date is working day.
                if ($request->has('trainer_id') && $request->trainer_id > 0) {
                    $trainerId = $request->trainer_id;
                    $dayOfWeek_timeslots = TrainerTimeslot::where('location_id', $locationId)
                        ->where('trainer_id', $trainerId);
                } else {
                    $dayOfWeek_timeslots = Timeslot::where('location_id', $locationId);
                }
                $workingDay = $dayOfWeek_timeslots->where('day_idx', $dow)
                    ->orderBy('day_idx', 'asc')
                    ->orderBy('from_time', 'asc')
                    ->first();
                if (empty($workingDay)) {
                    $j++;
                    // dow is not a working day, go to next dow without date increment.
                    continue;
                }
                $data[] = ["date" => $d1->format('Y-m-d'), "dow" => $dow];
                $d1->addDay();
                $i++;
                if ($i == $quantity) break;
            }
        }
        return compact('data', 'holidays');
    }

    public function store(Request $request)
    {
        // get user's book days in advance.
        $user = Auth::user();
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|integer',
            'noOfSession' => 'required|integer',
            'sessionInterval' => 'required|integer',
            'roomId' => 'required|integer',
            'serviceId' => 'required|integer',
            'price' => 'required',
//            'paymentMethod' => 'required',
//            'order_status' => 'required',
        ]);
        $assignRandomRoom = true;   // can get from Company settings.
        $saveAsPending = true;
        $isPackage = false;
        $appointmentDate = $request->date;
        $paymentGatway = $request->paymentMethod;
        $entity = 'appointment';
        $sendNotify = false;
        // onsite appointment, use Customer as user.
        if ($request->paymentMethod == 'onsite') {
            if ($request->has('notify_parties'))
                $sendNotify = $request->notify_parties;
            $paymentMethod = 'onsite';
            $paymentGatway = 'cash';
            $request->validate([
                'customerId' => 'required|integer',
            ]);
            $user = User::find($request->customerId);
            if ($request->roomId > 0) {
                $assignRandomRoom = false;
            }
            if ($request->has('is_package')) {
                $isPackage = $request->is_package;
                $appointmentDate = $request->lesson_dates[0];
            }
            if ($request->has('status')) {
                $saveAsPending = ($request->status == 'pending');
            }
        } else {
            $sendNotify = true;   // always send
            $paymentMethod = 'electronic';
            // check if user is new, make appointment status to 'pending' instead.
            $bookedAppointments = Appointment::orderBy('start_time', 'desc')->where('user_id', $user->id)->limit(10)->get();
            foreach ($bookedAppointments as $bookedAppointment) {
                if ($bookedAppointment->status == 'approved') {
                    $saveAsPending = false;
                }
            }
        }
        // get appointment dates.
        $appointmentDates = $this->getAppointmentDates($user, $appointmentDate, $request->time, $request->noOfSession, $request->sessionInterval, $request->roomId, $assignRandomRoom, $request->package_id);

        $assignedRoom = $appointmentDates['room_id'];
        $results = [];
        if ($assignedRoom <= 0) {
            // appointment time not available, throw error.
            $results = ['success' => false, 'error' => 'Selected time is not available, please choose different time.', 'param' => $assignedRoom];
            return $results;
        }
        $isDup = $appointmentDates['duplicated'];   // for customer!!
        if ($isDup) {
            return ['success' => false, 'error' => 'Found duplicate appointment.'];
        }

        // start DB transaction.
        DB::beginTransaction();

        $appointmentStatus = $saveAsPending ? 'pending' : 'approved';
        // save appointment, it is 1st appointment if it is package.
        $savedAppointment = $this->saveAppointment($request, $appointmentDates, $user, $appointmentStatus, $sendNotify, 0);
        $customerBooking = $this->saveCustomerBooking($request, $savedAppointment, $user);
        $savedAppointment->customer_booking_id = $customerBooking->id;
        $results[] = $savedAppointment;

        // Packages handling.
        if ($isPackage) {
            $dates = $request->lesson_dates;
            $pkg_count = count($dates);
            for ($i=1; $i<$pkg_count; $i++) {
                // pass 1st appointment's id as parent_id as ref.
                $appointmentDates = $this->getAppointmentDates($user, $dates[$i], $request->time, $request->noOfSession, $request->sessionInterval, $request->roomId, $assignRandomRoom, $request->package_id);
                $isDup = $appointmentDates['duplicated'];   // for customer!!
                if ($isDup) {
                    return ['success' => false, 'error' => 'Found duplicate appointment.'];
                }
                $savedAppointment2 = $this->saveAppointment($request, $appointmentDates, $user, $appointmentStatus, $sendNotify, $savedAppointment->id);
                $customerBooking2 = $this->saveCustomerBooking($request, $savedAppointment2, $user);
                $savedAppointment2->customer_booking_id = $customerBooking2->id;
                $results[] = $savedAppointment2;
            }
            $type_of_apt = 'package';
            $amount = $request->package_amount;
            $entity = 'package';
        } else {
            $type_of_apt = 'booking';
            $amount = $request->price;
        }

        $order = new Order;
        $order->order_number = uniqid();
        $order->order_date = Carbon::today()->format('Y-m-d');
        $order->order_total = $amount;
        if ($request->has('discount')) {
            if ($request->discount > 0)
                $order->discount = $request->discount;
        }
        $order->customer_id = $customerBooking->customer_id;
        $order->user_id = Auth::user()->id;
        $order->paid_amount = $appointmentStatus == 'approved' ? $order->order_total : 0;
        $order->payment_status = $appointmentStatus == 'approved' ? 'paid' : 'pending';       // FIXME get payment status from gateway response.
        $order->order_status = $appointmentStatus == 'approved' ? 'confirmed' : 'pending';
        $recurring = $request->input('recurring');
        $order->recurring = json_encode($recurring);
        $order->repeatable = $request->has('repeatable' ) ? $request->repeatable : false;
        if ($request->has('commission')) {
            // note trainerId will be saved only for order that has commission.
            if ($request->commission > 0 && $request->has('trainerId')) {
                $order->trainer_id = $request->trainerId;
                $order->commission = $request->commission;
                if ($isPackage) {
                    $order->commission = $request->package_commission;
                }
            }
        }
        $order->save();

        foreach ($results as $result) {
            $orderDetail = new OrderDetail;
            $orderDetail->order_id = $order->id;
            $orderDetail->order_type = $type_of_apt;
            $orderDetail->booking_id = $result->customer_booking_id;
            $orderDetail->order_description = json_encode($result);
            $orderDetail->original_price = $request->price;
            $orderDetail->discounted_price = $request->price;
            $orderDetail->coupon_id = $request->coupon_id;
            $orderDetail->save();
        }

        $payment = new Payment;
        $payment->order_id = $order->id;
        $payment->amount = $order->order_total;
        $payment->payment_date_time = (new DateTime())->format('Y-m-d H:i:s');
        $payment->status = $order->payment_status;
        $payment->payment_method = $paymentMethod;
        $payment->gateway = $paymentGatway;
//        $payment->parent_id = ;
        $payment->entity = $entity;
        $payment->save();

        DB::commit();

        // send email.
        if ($sendNotify) {
            if ($isPackage) {
                $resOrder = Order::find($order->id);
                Mail::to($user->email)
                    ->bcc(config('mail.from.address'))
                    ->send(new PackageApproved($resOrder->load('details', 'customer')));
            } else {
                $resCustomerBooking = CustomerBooking::find($customerBooking->id);
                Mail::to($resCustomerBooking->customer->email)
                    ->bcc(config('mail.from.address'))
                    ->send(new AppointmentApproved($resCustomerBooking));
            }
        }

        if ($request->expectsJson()) {
            $results = ['success' => true, 'order_id' => $order->id];
            return $results;
        }
        return redirect()->route('orders.index');
    }

    private function saveAppointment(Request $request, $appointmentDates, User $user, string $appointmentStatus, bool $sendNotify, $parentId) {
        // use trainer_id as appointment user, if the appointment is trainer-student relationship.
        $userId = $user->id;
        if ($request->has('trainerId')) {
            if ($request->trainerId > 0) {
                $userId = $request->trainerId;
            }
        }

        $appointment = new Appointment;
        $appointment->start_time = $appointmentDates['start_time'];
        $appointment->end_time = $appointmentDates['end_time'];
        $appointment->room_id = $appointmentDates['room_id'];
        if ($request->has('package_id')) {
            // get existing package appointment.
            $appointment->package_id = $request->package_id;
            $packageApt = Appointment::where('start_time', $appointment->start_time)
                ->where('end_time', $appointment->end_time)
                ->where('room_id', $appointment->room_id)
                ->where('package_id', $appointment->package_id)
                ->first();
            if (!empty($packageApt)) {
                return $packageApt;
            }
        }
        $appointment->user_id = $userId;
        $appointment->service_id = $request->serviceId;
        $appointment->package_id = $request->package_id;
//        $appointment->lesson_space
        $appointment->notify_parties = $sendNotify;
        $appointment->internal_remark = $request->internal_remark;
        $appointment->status = $appointmentStatus;     // get defaults from settings.
        $appointment->parent_id = $parentId;
        $appointment->save();

        return $appointment;
    }

    private function saveCustomerBooking(Request $request, Appointment $appointment, User $user) {
        $customerBooking = new CustomerBooking;
        $customerBooking->appointment_id = $appointment->id;
        $customerBooking->customer_id = $user->id;
        $customerBooking->price = $request->price;
        $customerBooking->info = json_decode($request->personalInformation);    // if any.
        $customerBooking->revised_appointment_id = $appointment->id;
        $customerBooking->revision_counter = 0;
        $customerBooking->save();

        return $customerBooking;
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
            $appointmentDates = $this->getAppointmentDates($user, $request->date, $request->time, $request->noOfSession, $request->sessionInterval, $request->room_id, true, $booking->appointment->package_id);
            $isDup = $appointmentDates['duplicated'];   // for customer!!
            if ($isDup) {
                return ['success' => false, 'error' => 'Found duplicate appointment.'];
            }
            if ($booking->appointment->package_id > 0) {
                // not allow to change package.
                return ['success' => false, 'error' => 'You cannot reschedule appointment of package.'];
            } else {
                $booking->appointment->start_time = $appointmentDates['start_time'];
                $booking->appointment->end_time = $appointmentDates['end_time'];
                $booking->appointment->room_id = $appointmentDates['room_id'];
                $booking->appointment->save();
                $booking->revision_counter += 1;
                $booking->save();
                $results = ['success' => true, 'room' => Room::find($booking->appointment->room_id)];
            }
            // TODO mail
        } else {
            $results = ['success' => false, 'error' => 'You must reschedule before 48 hours of appointment start time.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }

    }

    private function convertTimeslot($dayOfWeek_timeslots, $sessionToBeBooked, $sessionIntervalEpoch, $price) {
        $today = Carbon::today()->timestamp;
        $DAY_EPOCH = 24 * 60 * 60;
        $freeTimesolts = array(array());
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
        return $freeTimesolts;
    }

    private function getAppointmentDates($user, $date, $time, $noOfSession, $sessionInterval, $room_id, $assignRandomRoom, $package_id) {
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
            if (!$package_id) {
                $isRoomOccupied = $this->isRoomOccupied($assignedRoom, $dt, $dt2);
                if ($isRoomOccupied) {   // reset $assignedRoom to negative number if desired room was occupied.
                    $assignedRoom = -2;
                }
            }
        }
        // check duplicate, in Appointment system should not allow same user book same timeslot.
        $isDup = false;
        for ($i=0; $i<2; $i++) {   // do twice, 1 for start_time, another for end_time.
            $paramDate = $i == 0 ? $dt : $dt2;
            $found = DB::table('customer_bookings')
                ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
                ->where('customer_bookings.customer_id', $user->id)
                ->whereRaw('? between appointments.start_time and appointments.end_time', $paramDate)->first();
            if (!empty($found)) {
                $isDup = true;
                break;
            }
        }
        return [
            'start_time' => $dt,
            'end_time' => $dt2,
            'room_id' => $assignedRoom,
            'duplicated' => $isDup
        ];
    }
}
