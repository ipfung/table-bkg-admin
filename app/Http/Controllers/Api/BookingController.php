<?php

namespace App\Http\Controllers\Api;

use App\Facade\AppointmentService;
use App\Facade\PermissionService;
use App\Models\CustomerBooking;
use App\Models\Leave;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends BaseController
{
    private $appointmentService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PermissionService $permissionService, AppointmentService $appointmentService)
    {
        parent::__construct($permissionService);
        $this->appointmentService = $appointmentService;
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
                DB::raw("(select a.name from users a, roles b where a.id=appointments.user_id and a.role_id=b.id and b.name in ('manager', 'internal_coach', 'external_coach')) as user_name, appointments.user_id as trainer_id"),
                DB::raw('(select roles.color_name from users, roles where users.id=customer_bookings.customer_id and users.role_id=roles.id) as role_color_name'),
                DB::raw('(select name from users where id=customer_bookings.customer_id) as customer_name'),
                DB::raw('(select name from packages where id=appointments.package_id) as package_name, package_id'),
                DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'),
                DB::raw('(select order_id from order_details where order_details.booking_id=customer_bookings.id) as order_id'),
                DB::raw('(select order_number from order_details, orders where order_details.booking_id=customer_bookings.id and order_details.order_id=orders.id) as order_num'),
                DB::raw('(select payments.amount from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_amount'),
                DB::raw('(select payments.status from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_status'),
                'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name', 'rooms.color')
            ->orderBy('appointments.start_time', 'asc')
            ->orderBy('rooms.name', 'asc');
        if ($request->has('appointmentId')) {
            $bookings->where('appointment_id', $request->appointmentId);
        } else {
            $bookings->whereRaw('CAST(appointments.start_time AS DATE)>=?', $fromDate )
                ->whereRaw('CAST(appointments.end_time AS DATE)<=?', $toDate );

        }
        if ($request->has('customerId')) {
            $bookings->where('customer_id', $request->customerId);
        }
        if ($request->has('trainerId')) {
            $bookings->where('user_id', $request->trainerId);
        }
        if ($request->has('ownerId')) {
            $ownerId = $request->ownerId;
            $bookings->where(function ($query) use ($ownerId){
                $query->where('customer_id', $ownerId)
                    ->orWhere('user_id', $ownerId);
            });
        }
        if ($request->has('status')) {
            if ($request->status != '')
                $bookings->where('appointments.status', $request->status);
        }


        $results = [];
        if ($this->isInternalCoachLevel($user)) {
            $results['newable'] = true;
            if ($request->has('customer_id')) {
                $bookings->where('customer_id', $request->customer_id);
            }
            $results['showCustomer'] = true;
            $results['showTrainer'] = true;
        } else if ($this->isExternalCoachLevel($user)) {
            $results['newable'] = true;
            if ($request->has('customer_id')) {
                $bookings->where('customer_id', $request->customer_id);
            }
            // trainer/coach could see their student appointments only.
            $bookings->where('user_id', $user->id);
            $results['showCustomer'] = true;
            $results['showTrainer'] = false;
        } else {
            $results['newable'] = false;
            $bookings->where('customer_id', $user->id);
            $results['showCustomer'] = false;
            $results['showTrainer'] = true;
        }

        if ($request->expectsJson()) {
            $results['success'] = true;
            $results['manager'] = $this->isInternalCoachLevel($user);
            $results['requiredTrainer'] = config("app.jws.settings.required_trainer");
            $results['supportPackages'] = config("app.jws.settings.packages");
            $results['supportFinance'] = config("app.jws.settings.finance");
            $results['timeslotSetting'] = config("app.jws.settings.timeslots");
            $results['checkInBeforeMinute'] = config("app.jws.settings.checkin_before_minute");
            $results['checkInAfterMinute'] = config("app.jws.settings.checkin_after_minute");
            $results['data'] = $bookings->get();
            return $results;
        }
        return view("bookings", $bookings);

    }

    /**
     * @param Request $request
     * @param $id appointment id.
     * @return array|void
     */
    public function punchInCourse(Request $request, $id) {
        $user = Auth::user();
        $results = [];
        // only allow user itself to checkin.
        if ($this->isExternalCoachLevel($user)) {  // included Internal Coach.
            if ($request->has('data')) {
                $arr = $request->input('data');
//                $arr = json_decode($request->input('data'));
                $now = $this->getCurrentDateTime();
                foreach ($arr as $value) {
                    $booking = CustomerBooking::where('appointment_id', $id)
                        ->where('id', $value['booking_id'])
                        ->first();
                    $can_checkin_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time, new DateTimeZone(config("app.jws.local_timezone")))->modify('-' . config("app.jws.settings.checkin_before_minute") . 'minute');   // minute before appointment start time.
                    if ($now < $can_checkin_time) {
                        $results = ['success' => false, 'error' => 'You cannot checkin before your appointment start time.', 'params' => ['can_checkin' => $can_checkin_time, 'now' => $now]];
                        break;
                    }
                    $booking->attendance = $value['attendance'];
                    $booking->attendance_remark = $value['remark'] ?: null;
                    $booking->checkin = $now->format('Y-m-d H:i:s');
                    $booking->checkin_by = $user->id;
                    $booking->save();
                    // send notification during save may cause performance issue
                    $resp = $this->appointmentService->sendAppointmentNotifications('check_in', $booking, $user->id);
                    if ($resp == -1) {    // no notifications being sent.
                        $results[] = ['booking_id' => $value['booking_id'], 'notifications' => false];
                    } else {    // some notifications are sent.
                        $results[] = ['booking_id' => $value['booking_id'], 'notifications' => true];
                    }
                }
            }

//echo 'can_checkin_time=' . $can_checkin_time->format('Y-m-d H:i:s');
//echo ', booking_end_time=' . $booking_end_time->format('Y-m-d H:i:s');
//echo ', now=' . $now->format('Y-m-d H:i:s');
        }

        if ($request->expectsJson()) {
            return $results;
        }
    }

    /**
     * @param Request $request
     * @param $id customer booking id
     * @return array|void
     */
    public function punchInBooking(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id);
//        echo 'booking=' . json_encode($booking);
        // only allow user itself to checkin.
        if (empty($booking->checkin)) {
            if ($user->id != $booking->customer_id) {
                if ($this->isSuperLevel($user)) {   // it is necessary to do isSuperLevel() here.
                    // can checkin anytime.
                } else if ($this->isExternalCoachLevel($user)) {  // included Internal Coach.
                    // check if = appointment's owner.
                    if ($user->id != $booking->appointment->user_id) {
                        $results = ['success' => false, 'error' => 'You cannot checkin for others class.'];
                        goto output;    // break
                    }
                } else {
                    // other user level.
                    $results = ['success' => false, 'error' => 'You cannot checkin for others.'];
                    goto output;    // break
                }
            }
            if (config("app.jws.settings.finance")) {
                // check payment status.
                $osAmount = $this->paidAmount($id) - $booking->price;
                if ($osAmount < 0) {
                    $results = ['success' => false, 'error' => 'Please pay the outstanding amount HK$' . abs($osAmount)];
                    goto output;    // break
                }
            }

            $now = $this->getCurrentDateTime();
//echo 'can_checkin_time=' . $can_checkin_time->format('Y-m-d H:i:s');
//echo ', booking_end_time=' . $booking_end_time->format('Y-m-d H:i:s');
//echo ', now=' . $now->format('Y-m-d H:i:s');
            $canCheckIn = false;
            if ($this->isSuperLevel($user)) {
                // can checkin anytime.
                $canCheckIn = true;
            } else {
                $can_checkin_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time, new DateTimeZone(config("app.jws.local_timezone")))->modify('-' . config("app.jws.settings.checkin_before_minute") . 'minute');   // minute before appointment start time.
                $booking_end_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->end_time, new DateTimeZone(config("app.jws.local_timezone")))->modify(config("app.jws.settings.checkin_after_minute") . 'minute');
                if ($now > $can_checkin_time && $now < $booking_end_time) {
                    $canCheckIn = true;
                } else if ($now < $can_checkin_time) {
                    $results = ['success' => false, 'error' => 'You can checkin within before your appointment start time.', 'params' => ['can_checkin' => $can_checkin_time, 'end_time' => $booking_end_time, 'now' => $now]];
                    goto output;    // break
                } else if ($now > $booking_end_time) {
                    $results = ['success' => false, 'error' => 'Your appointment is ended already, no checkin can be done.', 'params' => ['end_time' => $booking_end_time, 'now' => $now]];
                    goto output;    // break
                }
            }
            if ($canCheckIn) {
                $booking->checkin = $now->format('Y-m-d H:i:s');
                $booking->checkin_by = $user->id;
                $booking->attendance = 'attend';
                $booking->save();
//                // inform parties concerned(e.g. parent APP & email).
//                $payload = [
//                    'title' => 'Check in',
//                    'body' => $user->name . ' has checked-in the class at ' . $booking->checkin . '.',
//                    'template' => 'check_in',
//                    'placeholder' => null,
//                    // extra params.
//                    'data' => [
//                        'page' => 'none',
//                        'customer_name' => $booking->customer->name,
//                        'time' => $booking->checkin,
//                        'id' => $booking->id
//                    ]
//                ];
                $resp = $this->appointmentService->sendAppointmentNotifications('check_in', $booking, $user->id);
                if ($resp == -1) {    // no notifications being sent.
                    return ['success' => true, 'checkin' => $booking->checkin, 'notifications' => false];
                } else {    // some notifications are sent.
                    $resp['success'] = true;
                    $resp['checkin'] =  $booking->checkin;
                    return $resp;
                }
            }
        } else {
            $results = ['success' => false, 'error' => "The appointment has been checked-in already at.", 'params' => ['checked' => $booking->check_in, 'now' => $now]];
        }

        output:
        if ($request->expectsJson()) {
            return $results;
        }

    }

    /**
     * reject booking by trainer or above.
     *
     * @param Request $request
     * @param $id
     * @return array|void
     */
    public function rejectBooking(Request $request, $id) {
        $user = Auth::user();
        // only allow user to reject unpaid booking.
        if ($this->isExternalCoachLevel($user)) {
            $booking = CustomerBooking::find($id);
            if (!$this->isSuperLevel($user)) {
                if ($booking->appointment->user_id != $user->id) {
                    return ['success' => false, 'error' => 'You cannot reject appointment that is not belong to you.'];
                }
            }
            // ok to reject booking once.
            $booking->appointment->status = 'rejected';
            $booking->appointment->save();
            $booking->save();
            // send mail if notify option enabled.
            $resp = $this->appointmentService->sendAppointmentNotifications('appointment_rejected', $booking, $user->id);
            if ($resp == -1) {    // no notifications being sent.
                return ['success' => true, 'status' => $booking->appointment->status, 'notifications' => false];
            } else {    // some notifications are sent.
                $resp['success'] = true;
                $resp['status'] =  $booking->appointment->status;
                return $resp;
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot reject appointment.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }
    }

    /**
     * approve booking by trainer or above.
     *
     * @param Request $request
     * @param $id
     * @return array|void
     */
    public function approveBooking(Request $request, $id) {
        $user = Auth::user();
        // only allow user to cancel unpaid booking.
        if ($this->isExternalCoachLevel($user)) {
            $booking = CustomerBooking::find($id);
            if ($booking->appointment->status == 'pending') {
                if (!$this->isSuperLevel($user)) {
                    if ($booking->appointment->user_id != $user->id) {
                        return ['success' => false, 'error' => 'You cannot approve appointment that is not belong to you.'];
                    }
                }
                // ok to approve booking once.
                $booking->appointment->status = 'approved';
                $booking->appointment->save();
                $booking->save();
                $resp = $this->appointmentService->sendAppointmentNotifications('appointment_approved', $booking, $user->id);
                if ($resp == -1) {    // no notifications being sent.
                    return ['success' => true, 'status' => $booking->appointment->status, 'notifications' => false];
                } else {    // some notifications are sent.
                    $resp['success'] = true;
                    $resp['status'] = $booking->appointment->status;
                    return $resp;
                }
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot approve appointment.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }
    }

    /**
     * cancel booking by customer.
     *
     * @param Request $request
     * @param $id
     * @return array|void
     */
    public function cancelBooking(Request $request, $id) {
        $user = Auth::user();
        $booking = CustomerBooking::find($id);

        // only allow user to cancel unpaid booking.
        if ($user->id == $booking->customer_id) {
            $osAmount = $this->paidAmount($id) - $booking->price;
            if ($osAmount < 0) {   // not paid, can cancel.
                // can amend 48 hours before appointment start time.
                $can_amend_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->modify('-48 hours');
                $now = $this->getCurrentDateTime();
                if ($now < $can_amend_time) {   // now is 48 hours before appointment start time.
                    if ($booking->revision_counter == 0) {
                        // ok to cancel booking once.
                        $booking->appointment->status = 'canceled';
                        $booking->appointment->save();
                        $booking->revision_counter += 1;
                        $booking->save();
                        $results = ['success' => true, 'status' => $booking->appointment->status];
                        // send mail if notify option enabled.
                        if ($booking->appointment->status == 'canceled') {   // FIXME check option.
                            $resp = $this->appointmentService->sendAppointmentNotifications('appointment_canceled', $booking, $user->id);
                            if ($resp == -1) {    // no notifications being sent.
                                return ['success' => true, 'checkin' => $booking->checkin, 'notifications' => false];
                            } else {    // some notifications are sent.
                                $resp['success'] = true;
                                $resp['checkin'] =  $booking->checkin;
                                return $resp;
                            }
                        }
                    } else {
                        $results = ['success' => false, 'error' => 'You have been modified several times.', 'params' => $booking->revision_counter];
                    }
                } else {
                    $results = ['success' => false, 'error' => 'You must cancel before 48 hours of appointment start time.'];
                }
            } else {
                $results = ['success' => false, 'error' => 'Cancellation is not suitable for paid booking.'];
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot reschedule for others.'];
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
        $booking = CustomerBooking::find($id);
        // only allow user itself to take leave.
        if ($user->id == $booking->customer_id || $this->isExternalCoachLevel($user)) {
            if (empty($booking->checkin) && empty($booking->take_leave_at)) {
                $can_amend_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time);
                $now = $this->getCurrentDateTime();
                if ($now < $can_amend_time) {   // now is just before appointment start time.
                    // ok to take leave
                    if ($booking->revision_counter == 0) {
                        $booking->take_leave_at = $now->format('Y-m-d H:i:s');
                        $booking->take_leave_by = $user->id;
                        $booking->attendance = 'take_leave';
                        DB::beginTransaction();
                        $booking->save();
                        $leave = new Leave;
                        $leave->booking_id = $booking->id;
                        $leave->take_leave_at = $booking->take_leave_at;
                        $leave->flag = 1;
                        $leave->created_by = $user->id;
                        $leave->updated_by = $user->id;
                        $leave->save();
                        DB::commit();
                        $resp = $this->appointmentService->sendAppointmentNotifications('appointment_leave', $booking, $user->id);
                        if ($resp == -1) {    // no notifications being sent.
                            return ['success' => true, 'take_leave_at' => $booking->take_leave_at, 'notifications' => false];
                        } else {    // some notifications are sent.
                            $resp['success'] = true;
                            $resp['take_leave_at'] =  $booking->take_leave_at;
                            return $resp;
                        }
                    }
                } else {
                    $results = ['success' => false, 'error' => 'You cannot take leave after appointment start time %.', 'params' => $booking->appointment->start_time];
                }
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot take leave for others.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }

    }

    /**
     * Get paid amount of booking.
     * @param $bookingId customer booking id.
     * @return double the total paid amount.
     */
    public function paidAmount($bookingId) {
        $payments = DB::table('payments')
            ->join('order_details', 'payments.order_id', '=', 'order_details.order_id')
            ->select('payments.*', 'order_details.original_price')
            ->where('booking_id', $bookingId)
            ->get();

        //
        $totalPaid = 0;
        foreach ($payments as $paid) {
            if ($paid->status == 'paid') {
                $totalPaid += $paid->amount;
            }
        }

        return $totalPaid;
    }
}
