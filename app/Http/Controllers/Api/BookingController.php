<?php

namespace App\Http\Controllers\Api;

use App\Mail\AppointmentApproved;
use App\Mail\AppointmentCanceled;
use App\Models\CustomerBooking;
use App\Services\UserDeviceService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingController extends BaseController
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
                DB::raw("(select a.name from users a, roles b where a.id=appointments.user_id and a.role_id=b.id and b.name in ('manager', 'internal_coach', 'external_coach')) as user_name"),
                DB::raw('(select roles.color_name from users, roles where users.id=customer_bookings.customer_id and users.role_id=roles.id) as role_color_name'),
                DB::raw('(select name from users where id=customer_bookings.customer_id) as customer_name'),
                DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'),
                DB::raw('(select payments.status from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_status'),
                'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name', 'rooms.color')
            ->where('appointments.start_time', '>=', $fromDate )
            ->where('appointments.end_time', '<=', $toDate )
            ->orderBy('appointments.start_time', 'asc')
            ->orderBy('rooms.name', 'asc');
        $results = [];
        if ($this->isInternalCoachLevel($user)) {
            $results['newable'] = true;
            if ($request->has('customer_id')) {
                $bookings->where('customer_id', $request->customer_id);
            }
            $results['showCustomer'] = true;
            $results['showTrainer'] = true;
        } else if ($this->isExternalCoachLevel($user)) {
            $results['newable'] = false;
            if ($request->has('customer_id')) {
                $bookings->where('customer_id', $request->customer_id);
            }
            // trainer and coach could see student appointments.
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
            $results['data'] = $bookings->get();
            return $results;
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
                // check payment status.
                $osAmount = $this->paidAmount($id) - $booking->price;
                if ($osAmount < 0) {
                    $results = ['success' => false, 'error' => 'Please pay the outstanding amount HK$' . abs($osAmount)];
                    goto output;    // break
                }

                $can_checkin_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->start_time)->modify('-1 hour');   // 1 hour before appointment start time.
                $booking_end_time = DateTime::createFromFormat('Y-m-d H:i:s', $booking->appointment->end_time);
                $now = $this->getCurrentDateTime();
//echo 'can_checkin_time=' . $can_checkin_time->format('Y-m-d H:i:s');
//echo ', booking_end_time=' . $booking_end_time->format('Y-m-d H:i:s');
//echo ', now=' . $now->format('Y-m-d H:i:s');
                if ($now > $can_checkin_time && $now < $booking_end_time) {
                    $booking->checkin = $now->format('Y-m-d H:i:s');
                    $booking->save();
                    // inform parties concerned(e.g. parent APP & email).
                    $payload = [
                        'title' => 'Check in',
                        'body' => 'You have just checked-in the class.',
                        // extra params.
                        'data' => [
                            'page' => 'none',
                            'customer_name' => $booking->customer->name,
                            'time' => $booking->checkin,
                            'id' => $booking->id
                        ]
                    ];
                    $responseCode = UserDeviceService::sendToCustomer($booking->customer_id, $payload, $user->id);
                    $results = ['success' => true, 'checkin' => $booking->checkin, 'pushed' => (200 == $responseCode)];
                } else if ($now < $can_checkin_time) {
                    $results = ['success' => false, 'error' => 'You can checkin within 60 minute before your appointment start time.'];
                } else if ($now > $booking_end_time) {
                    $results = ['success' => false, 'error' => 'Your appointment is ended already. No checkin can be done.'];
                }
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot checkin for others.'];
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
        // only allow user to cancel unpaid booking.
        if ($this->isExternalCoachLevel($user)) {
            $booking = CustomerBooking::find($id);
            if (!$this->isSuperLevel($user)) {
                if ($booking->appointment->user_id != $user->id) {
                    return ['success' => false, 'error' => 'You cannot reject appointment that is not belong to you.'];
                }
            }
            // ok to cancel booking once.
            $booking->appointment->status = 'rejected';
            $booking->appointment->save();
            $booking->save();
            $results = ['success' => true, 'status' => $booking->appointment->status];
            // send mail if notify option enabled.
//                if ($booking->appointment->status == 'reject') {   // FIXME check option.
//                    Mail::to($user->email)
//                        ->bcc(config('mail.from.address'))
//                        ->send(new AppointmentRejected($booking));
//                }
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
                // ok to cancel booking once.
                $booking->appointment->status = 'approved';
                $booking->appointment->save();
                $booking->save();
                $results = ['success' => true, 'status' => $booking->appointment->status];
                // send mail if notify option enabled.
//                if ($booking->appointment->status == 'approved') {   // FIXME check option.
//                    Mail::to($user->email)
//                        ->bcc(config('mail.from.address'))
//                        ->send(new AppointmentApproved($booking));
//                }
            }
        } else {
            $results = ['success' => false, 'error' => 'You cannot approve appointment.'];
        }
        if ($request->expectsJson()) {
            return $results;
        }
    }

    /**
     * cancel booking.
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
                        $results = ['success' => true, 'status' => 'canceled'];
                        // send mail if notify option enabled.
                        if ($booking->appointment->status == 'canceled') {   // FIXME check option.
                            Mail::to($user->email)
                                ->bcc(config('mail.from.address'))
                                ->send(new AppointmentCanceled($booking));
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
