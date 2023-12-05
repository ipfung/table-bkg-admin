<?php

namespace App\Http\Controllers;

use App\Facade\OrderService;
use App\Facade\PermissionService;
use App\Http\Controllers\Api\BaseController;
use App\Facade\AppointmentService;
use App\Models\Appointment;
use App\Models\CustomerBooking;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Service;
use App\Models\Holiday;
use App\Models\Timeslot;
use App\Models\TrainerTimeslot;
use App\Models\TrainerWorkdateTimeslot;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    private $appointmentService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AppointmentService $appointmentService, OrderService $orderService, PermissionService $permissionService)
    {
        $this->appointmentService = $appointmentService;
        $this->orderService = $orderService;
        $this->permissionService = $permissionService;
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
        $dates = $this->appointmentService->getDates($user);
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
            $customer = User::find($customerId);  // get price.
            if ($service->price < 0)
                $price = $customer->role->default_price;
        } else {
            if ($service->price < 0)
                $price = $user->role->default_price;              // from Service record, FIXME different user has different price.
        }
        $sessionPrice = $price / $noOfSession;
        $sessionIntervalEpoch = $service->session_minute_epoch;
        $sessionDurationEpoch = $service->duration_epoch;
        $trainerId = 0;
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
            if (config("app.jws.settings.timeslots") == 'trainer_date') {
                $trainerId = $booking->appointment->user_id;
            }
//        echo 'bookId noOfSession, price=' . $price . ', ' . $noOfSession;
        }
        $validOrder = $this->orderService->getValidTokenBasedOrder($user);
        if ($validOrder) {
            if ($validOrder['token_quantity'] > 0) {
                $price = -1;   // -1 mean will be deduct by token.
            }
        }
//        echo 'dayOfWeek_timeslots=' . $dayOfWeek_timeslots;
        // create a TODAY 0:00 epoch.
        $sessionToBeBooked = ($noOfSession * $sessionDurationEpoch);   // client selected session * each session, in epoch.
        // 2-dimension array per week_number.
        $dayOfWeek_timeslots = [];
        $trainer = null;
        if (config("app.jws.settings.timeslots") == 'trainer_date') {
            if ($request->has('trainer_id'))
                $trainerId = $request->trainer_id;
            $dayOfWeek_timeslots = TrainerWorkdateTimeslot::where('location_id', $locationId)
                ->where('trainer_id', $trainerId)
                ->where('work_date', $minDate)   // must get 1 date only, otherwise the day_idx will be confused.
                ->orderBy('from_time', 'asc')
                ->get();
        } else {    // day of week
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

        $rooms = Room::orderBy('name', 'asc')
            ->where('status', 1001);
        if (config("app.jws.settings.required_room")) {
            $rooms->where('id', $roomId);
        }
        $all_rooms = $rooms->get();
        $start_date = strtotime($minDate);   // to epoch.
        $end_date = strtotime($maxDate);
//        echo '<br/>maxDate=' . $maxDate;
        $d = new Carbon($start_date);
        $dateFreeslots = [];
        // ref: https://tecadmin.net/php-loop-between-two-dates/#:~:text=PHP%20Loop%20Between%20Two%20Dates%20with%20Alternate%20Dates%3A&text=%3C%3F-,php%20date_default_timezone_set('UTC')%3B%20%24start_date%20%3D%20'2015%2D01,)))%3B%20%7D%20%3F%3E
        while ($start_date <= $end_date) {
            // TODO check if it's special day/holiday.
            // check office days off.
            $daysoff = Holiday::where('location_id', $locationId)->whereRaw('(? between start_date and end_date)', $d->format(BaseController::$dateFormat))->first();
            if (!empty($daysoff)) {
                $isDayOff = true;
                $freeslots = [];
            } else {
                $bufferSlots = [];
                // TODO check if it's special day/holiday of Trainer.
                // get freeslot from week_number freeslot.
                $freeslots = $freeTimesolts[$d->dayOfWeekIso];    // it contains 'time', 'price'.
                $isDayOff = (sizeof($freeslots) == 0);
                // TODO remove occupied time.
                foreach ($freeslots as $index => $slot) {
//echo "s3=" . ($start_date + $slot["time"]);
                    $dateTimeEpoch = $start_date + $slot["time"];
//echo "<br />dateTimeEpoch0000=" . $dateTimeEpoch;
                    $dt = (new DateTime("@$dateTimeEpoch"))->format('Y-m-d H:i:s');
                    $endTime = $dateTimeEpoch + ($noOfSession * $sessionDurationEpoch);
                    $dt2 = (new DateTime("@$endTime"))->format('Y-m-d H:i:s');
//echo "<br />startTime0=" . $dt . ', en0=' . $dt2;
                    $allRoomOccupied = true;
                    if ($trainerId > 0) {   // use trainer to check occupation.
                        $booked = $this->appointmentService->isTrainerOccupied($trainerId, $dt, $dt2, $booking ? $booking->id : -1);
                        if ($booked) {   // false = not occupied, else return $bookedAppointment.
                            // set buffer time
                            if (!$trainer) {
                                $trainer = User::find($trainerId);  // get buffer time.
                            }
                            if ($trainer->settings) {
                                $buffer = 0;
                                $settings = json_decode($trainer->settings);
                                if (isset($settings->buffer_time)) {
                                    $buffer = $settings->buffer_time * Service::$EPOCH;
                                    if ($buffer > 0) {
                                        $bufferSlot1 = strtotime($booked->start_time) - $buffer;  // start - buffer time
                                        $bufferSlot2 = strtotime($booked->end_time) + $buffer;  // end + buffer time
                                        if (!in_array($bufferSlot1, $bufferSlots)) {
                                            $bufferSlots[] = $bufferSlot1;
                                        }
                                        if (!in_array($bufferSlot2, $bufferSlots)) {
                                            $bufferSlots[] = $bufferSlot2;
                                        }
//                                        echo 'bufferSlots=' . json_encode($bufferSlots);// , '->to:' . $bufferSlot . ', ' . (new DateTime("@$bufferSlot"))->format('Y-m-d H:i:s');
                                    }
                                }
                            }
                        } else {
                            $allRoomOccupied = false;
                        }
                    } else {
                        foreach ($all_rooms as $room) {
//echo "roomid2222=" . $room->id;
                            if (!$this->appointmentService->isRoomOccupied($room->id, $dt, $dt2)) {   // false = not occupied.
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
            // apply buffer time.
            if (sizeof($bufferSlots) > 0) {
                foreach ($freeslots as $index => $slot) {
                    $dateTimeEpoch = $start_date + $slot["time"];
                    $dt = (new DateTime("@$dateTimeEpoch"))->format('Y-m-d H:i:s');
                    $endTime = $dateTimeEpoch + ($noOfSession * $sessionDurationEpoch);
                    $dt2 = (new DateTime("@$endTime"))->format('Y-m-d H:i:s');
//echo "<br />startTime000=" . $dt . ', en0=' . $dt2;
                    foreach ($bufferSlots as &$bufferTime) {
                        if ($bufferTime > $dateTimeEpoch && $bufferTime < $endTime) {
//echo 'bufferTime111=' . (new DateTime("@$bufferTime"))->format('Y-m-d H:i:s') . ', ' . $dt . ', ' . $dt2 . '!!';
                            unset($freeslots[$index]);
                            break;
                        }
                    }
                }
                $freeslots = array_values($freeslots);   // ref: https://stackoverflow.com/questions/369602/deleting-an-element-from-an-array-in-php
            }

            // TODO remove time that is less than selected sessions.
            // the date & its availability.
            $dateFreeslots[] = ['date' => $d->format(BaseController::$dateFormat), 'freeslots' => $freeslots, 'dayoff' => $isDayOff];
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
        $trainerId = 0;
        $endDate = null;
        if ($request->has('trainer_id') && $request->trainer_id > 0) {
            $trainerId = $request->trainer_id;
        }
        if ($request->has('package_id') && $request->package_id > 0) {
            $packages = Package::find($request->package_id);
            $endDate = $packages->end_date;
        }
        return $this->appointmentService->getLessonDates($request->start_date, $request->quantity, $request->dow, $trainerId, $endDate);
    }

    /**
     * Get package dates from Appointment table.
     * because primeng only accepts 'disabledDates' for form Calendar component.
     * @param Request $request
     * @return array
     */
    public function getPackageDatesById(Request $request, $packageId) {
        // get generated appointment dates.
        $appointments = Appointment::orderBy('start_time', 'asc')
            ->where('package_id', $packageId)->get();
        $booking = CustomerBooking::find($request->bookId);
        $data = [];
        // check if lesson date already being appointed.
        foreach ($appointments as $d) {
            $dt = DateTime::createFromFormat(BaseController::$dateTimeFormat, $d->start_time);

            $isUsed = $this->appointmentService->isCustomerInAppointment($d, $booking->customer_id);
            $data[] = ["date" => $dt->format('Y-m-d'), "id" => $d->id, "used" => $isUsed];
        }
        $package = Package::find($packageId);
        return compact('data', 'package');
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
        $packageId = 0;
        $package = null;
        $appointmentDate = $request->date;
        $paymentGatway = $request->paymentMethod;
        $entity = 'appointment';
        $sendNotify = false;
        $order = null;
        if ($request->has('orderNo')) {
            // has orderNo means session will be deducted from orders.
            $order = Order::where('order_number', $request->orderNo)->with('details')->first();
            $saveAsPending = false;
            $sendNotify = true;
            $entity = 'token';
        }
        if (!$order) {
            // onsite appointment, use Customer as user.
            if ($request->paymentMethod == 'onsite') {
                if ($request->has('notify_parties'))
                    $sendNotify = $request->notify_parties;
                $paymentMethod = 'onsite';
                $paymentGatway = 'cash';
                if ($request->has('paymentGateway')) {
                    $paymentGatway = $request->paymentGateway;
                }
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
                    if ($isPackage) {
                        $packageId = $request->package_id;
                        $package = Package::find($packageId);
                    }
                }
                if ($request->has('status')) {
                    $saveAsPending = ($request->status == 'pending');
                }
            } else {
                $sendNotify = true;   // always send
                $paymentMethod = 'electronic';
                if (config("app.jws.settings.required_room")) {
                    $assignRandomRoom = false;
                }
                // check if user is new, make appointment status to 'pending' instead.
                $bookedAppointments = Appointment::orderBy('start_time', 'desc')->where('user_id', $user->id)->limit(10)->get();
                foreach ($bookedAppointments as $bookedAppointment) {
                    if ($bookedAppointment->status == 'approved') {
                        $saveAsPending = false;
                    }
                }
                if ($user->role->max_book > 0) {
                    // check user's booking
                    $counter = $this->appointmentService->getAppointmentCount($user);
                    if ($counter >= $user->role->max_book) {
                        // exceed no. of booking, throw error.
                        $results = ['success' => false, 'error' => 'You have reached the maximum number of booking.', 'param' => $counter];
                        return $results;
                    }
                }
            }
        }
        // get appointment dates.
        $appointmentDates = $this->appointmentService->getAppointmentDates($user, $appointmentDate, $request->time, $request->noOfSession, $request->sessionInterval, $request->roomId, $assignRandomRoom, $packageId);

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

        $appointmentStatus = $saveAsPending ? 'pending' : 'approved';
        // use trainer_id as appointment user, if the appointment is trainer-student relationship.
        $userId = $user->id;
        if ($request->has('trainerId')) {
            if ($request->trainerId > 0) {
                $userId = $request->trainerId;
            }
        }

        // start DB transaction.
        DB::beginTransaction();

        $appointment = new Appointment;
        $appointment->start_time = $appointmentDates['start_time'];
        $appointment->end_time = $appointmentDates['end_time'];
        $appointment->room_id = $appointmentDates['room_id'];
        if ($isPackage && $request->has('package_id')) {
            // get existing package appointment.
            $appointment->package_id = $packageId;
        }
        $appointment->user_id = $userId;
        $appointment->service_id = $request->serviceId;
        $appointment->entity = $entity;
//        $appointment->lesson_space
        $appointment->notify_parties = $sendNotify;
        $appointment->internal_remark = $request->internal_remark;
        $appointment->status = $appointmentStatus;     // get defaults from settings.
        $savedAppointment = $this->appointmentService->saveAppointment($appointment);
        $customerBooking = $this->saveCustomerBooking($request, $savedAppointment, $user, $isPackage);
        $savedAppointment->customer_booking_id = $customerBooking->id;
        $results[] = $savedAppointment;

        $amount = $request->order_total;
        // Packages handling.
        if ($isPackage) {
            $dates = $request->lesson_dates;
            $pkg_count = count($dates);
            for ($i=1; $i<$pkg_count; $i++) {
                // pass 1st appointment's id as parent_id as ref.
                $appointmentDates = $this->appointmentService->getAppointmentDates($user, $dates[$i], $request->time, $request->noOfSession, $request->sessionInterval, $request->roomId, $assignRandomRoom, $packageId);
                $isDup = $appointmentDates['duplicated'];   // for customer!!
                if ($isDup) {
                    DB::rollBack();
                    return ['success' => false, 'error' => 'Found duplicate appointment.'];
                }
                // starts from 2nd appoint, save with parent_id.
                $appointment = new Appointment;
                $appointment->start_time = $appointmentDates['start_time'];
                $appointment->end_time = $appointmentDates['end_time'];
                $appointment->room_id = $appointmentDates['room_id'];
                if ($isPackage && $request->has('package_id')) {
                    // get existing package appointment.
                    $appointment->package_id = $packageId;
                }
                $appointment->user_id = $userId;
                $appointment->service_id = $request->serviceId;
                $appointment->entity = $entity;
//        $appointment->lesson_space
                $appointment->notify_parties = $sendNotify;
                $appointment->parent_id = $savedAppointment->id;
                $appointment->internal_remark = $request->internal_remark;
                $appointment->status = $appointmentStatus;     // get defaults from settings.
                $savedAppointment2 = $this->appointmentService->saveAppointment($appointment);
                $customerBooking2 = $this->saveCustomerBooking($request, $savedAppointment2, $user, $isPackage);
                $savedAppointment2->customer_booking_id = $customerBooking2->id;
                $results[] = $savedAppointment2;
            }
            $order_type = 'package';
            $entity = 'package';
        } else {
            $order_type = 'booking';
            if (!$request->has('order_total')) {
                // order_total normally provided from internal system.
                $amount = $request->price;
            }
        }

        $paymentStatus = $request->paymentStatus;
        $locationId = 1;   //FIXME

        $deduct_order_dtl = null;
        if ($order) {
            $result = $results[0];
            foreach ($order->details as $orderDetail) {
                if ($orderDetail->order_type == 'token' && $orderDetail->booking_id == 0) {
                    // get more objects for invoice printing.
                    $result->room = Room::find($result->room_id);
                    $orderDetail->order_description = json_encode($result);
                    $orderDetail->booking_id = $result->customer_booking_id;
                    $orderDetail->save();
                    $deduct_order_dtl = $orderDetail;
                    break;
                }
            }
        } else {
            $order = new Order;
            $order->order_number = $this->orderService->genOrderNo($locationId);
            $order->order_date = Carbon::today()->format(BaseController::$dateFormat);
            $order->order_total = $amount;
            if ($request->has('discount')) {
                if ($request->discount > 0)
                    $order->discount = $request->discount;
            }
            $order->customer_id = $customerBooking->customer_id;
            $order->user_id = Auth::user()->id;
            $order->paid_amount = $appointmentStatus == 'approved' ? $order->order_total : 0;
            $order->payment_status = $paymentStatus;
            $order->order_status = $appointmentStatus == 'approved' ? 'confirmed' : 'pending';
            $recurring = $request->input('recurring');
            $order->recurring = json_encode($recurring);
            $order->repeatable = $request->has('repeatable') ? $request->repeatable : false;
            if ($request->has('commission')) {
                // note trainerId will be saved only for order that has commission.
                if ($request->commission > 0 && $request->has('trainerId')) {
                    $order->trainer_id = $request->trainerId;
                    $order->commission = $request->commission;
                }
            }
            $order->save();

            $previousPkgId = -1;
            foreach ($results as $result) {
                // get more objects for invoice printing.
                $result->room = Room::find($result->room_id);
                $result->service = Service::find($result->service_id);
                if ($result->package_id && $result->package_id != $previousPkgId) {
                    $result->package = $package;
                    $previousPkgId = $result->package_id;
                }
                $orderDetail = new OrderDetail;
                $orderDetail->order_id = $order->id;
                $orderDetail->order_type = $order_type;
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
        }

        DB::commit();

        $str = $this->orderService->getEncodeOrderNo($order->id, $order->order_number);
        // send notifications.
        if ($sendNotify) {
            $resp = -1;   // default value.
            if ($isPackage) {
                $resOrder = Order::find($order->id);
                $resOrder->package = $package;
                $resp = $this->orderService->sendOrderNotifications('package_approved', $resOrder, Auth::user()->id);
            } else if (config("app.jws.settings.payment_gateway") == false || $deduct_order_dtl) {
                // send if no payment gateway, otherwise notification should be sent after payment.
                $resCustomerBooking = CustomerBooking::find($customerBooking->id);
                if ($deduct_order_dtl) {
                    $resCustomerBooking->order = $order;
                }
                $resp = $this->appointmentService->sendAppointmentNotifications('appointment_approved', $resCustomerBooking, Auth::user()->id);
//                $payload = [
//                    'template' => 'appointment_approved',
//                    'placeholders' => $placeholderService->getAppointmentData($resCustomerBooking),
//                    // extra params.
//                    'data' => [
//                        'page' => 'appointment',
//                        'customer_name' => $resCustomerBooking->customer->name,
//                        'order_number' => $order->order_number,
//                        'booking_id' => $resCustomerBooking->id,
//                        'appointment_date' => $resCustomerBooking->appointment->start_time
//                    ]
//                ];
//                $resp = $notificationService->sendToCustomer($resCustomerBooking->customer, $payload, Auth::user()->id);
//                if ($resCustomerBooking->customer->id != $resCustomerBooking->appointment->user->id) {
//                    $resp2 = $this->appointmentService->sendToEmployee($resCustomerBooking->appointment->user, $payload, Auth::user()->id);
//                }
            }
            if ($resp == -1) {    // no notifications being sent.
                return ['success' => true, 'order_id' => $order->id, 'order_num' => $order->order_number, 'order_str' => $str, 'pay_status' => $paymentStatus, 'package' => $isPackage, 'notifications' => false];
            } else {    // some notifications are sent.
                $resp['success'] = true;
                $resp['order_id'] = $order->id;
                $resp['order_num'] = $order->order_number;
                $resp['order_str'] = $str;
                $resp['pay_status'] = $paymentStatus;
                $resp['package'] = $isPackage;
                $resp['notifications'] = true;
//                    $resp['placeholders'] = $payload['placeholders'];
                return $resp;
            }
        }

        if ($request->expectsJson()) {
            $results = ['success' => true, 'pay_status' => $paymentStatus, 'order_id' => $order->id, 'order_num' => $order->order_number, 'order_str' => $str, 'package' => $isPackage];
            return $results;
        }
        return redirect()->route('orders.index');
    }

    private function saveCustomerBooking(Request $request, Appointment $appointment, User $user, $isPackage) {
        $customerBooking = new CustomerBooking;
        $customerBooking->appointment_id = $appointment->id;
        $customerBooking->customer_id = $user->id;
        $price = $request->order_total;
        if (!$isPackage && !$request->has('order_total')) {
            if ($request->has('orderNo'))
                $price = 0;
            else $price = $request->price;
        }
        $customerBooking->price = $price;
        $customerBooking->info = json_decode($request->personalInformation);    // if any.
        $customerBooking->revised_appointment_id = $appointment->id;
        $customerBooking->revision_counter = 0;
        $customerBooking->save();

        return $customerBooking;
    }

    /**
     * @param Request $request
     * @param $id the booking id, not appointment id
     * @return array|void
     */
    public function reschedule(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id);
//        echo 'reschedule id222=' . json_encode($booking);
//        echo 'reschedule id333=' . $booking->appointment->user_id;

        if (!empty($booking->checkin)) {
            return ['success' => false, 'error' => 'You have checked-in.'];
        }
        if ($booking->revision_counter > 0) {
            return ['success' => false, 'error' => 'You have been rescheduled several times.', 'params' => $booking->revision_counter];
        }
        // only allow user itself to reschedule their own appointment(customer booking maybe pointed to package/course).
        if (!$this->permissionService->isInternalCoachLevel($user)) {
            // only allow user itself to reschedule their own appointment(customer booking maybe pointed to package/course).
            if ($this->permissionService->isExternalCoachLevel($user)
                && $user->id != $booking->appointment->user_id) {
                return ['success' => false, 'error' => 'You cannot reschedule lesson for other student.'];
            }
            else if ($user->id != $booking->appointment->user_id) {
                if ($booking->appointment->entity == 'appointment') {
                    return ['success' => false, 'error' => 'You cannot reschedule for others.'];
                }
            }
        }
        // can amend 48 hours before appointment start time.
        $can_amend_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->modify('-48 hours');
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(config("app.jws.local_timezone")));   // must set timezone, otherwise the punch-in time use UTC(app.php) and can't checkin.
        if ($now < $can_amend_time) {   // now is 48 hours before appointment start time.
            if ($booking->appointment->package_id > 0) {
                // not allow to change package.
                return ['success' => false, 'error' => 'You cannot reschedule appointment of package.'];
            } else {
                // ok to change booking once.
                // get appointment dates.
                $appointmentDates = $this->appointmentService->getAppointmentDates($user, $request->date, $request->time, $request->noOfSession, $request->sessionInterval, $request->room_id, true, $booking->appointment->package_id);
                $isModify = $this->appointmentService->isModifyAppointment($appointmentDates['start_time'], $appointmentDates['end_time'], $id);
                $isDup = $appointmentDates['duplicated'];   // for customer!!
                if ($isDup && !$isModify) {
                    return ['success' => false, 'error' => 'Found duplicate appointment.'];
                }
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

    /**
     * @param Request $request contain appointment_id
     * @param $id the booking id, not appointment id
     * @return array|void
     */
    public function reschedulePackage(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id);

        if ($booking->appointment->package_id <= 0) {
            return ['success' => false, 'error' => 'The lesson is not in a package, please contact administrator.'];
        }

        if (!empty($booking->checkin)) {
            return ['success' => false, 'error' => 'You have checked-in the lesson.'];
        }
        if ($user->id == $booking->customer_id && $booking->revision_counter > 0) {
            return ['success' => false, 'error' => 'You have been rescheduled the lesson several times.', 'params' => $booking->revision_counter];
        }
        if (!$this->permissionService->isInternalCoachLevel($user)) {
            // only allow user itself to reschedule their own appointment(customer booking maybe pointed to package/course).
            if ($this->permissionService->isExternalCoachLevel($user)
                && $user->id != $booking->appointment->user_id) {
                return ['success' => false, 'error' => 'You cannot reschedule lesson for other student.'];
            }
            else if ($user->id != $booking->customer_id) {
                return ['success' => false, 'error' => 'You cannot reschedule lesson for others.'];
            }
        }
        $booking->appointment_id = $request->appointment_id;
        $booking->revision_counter += 1;
        $booking->save();
        $results = ['success' => true, 'reschedule_package' => true];
        // TODO mail
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
}
