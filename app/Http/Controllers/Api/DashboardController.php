<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Models\NotifyMessage;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperUser = $this->isSuperLevel($user);
        $isTrainerUser = $this->isExternalCoachLevel($user);

        // this month.
        $cur_start = (new Carbon('first day of this month'));
        $cur_end = (new Carbon('last day of this month'));


        // general
        $showBookingCount = $isSuperUser;
        $appointment = new Appointment;
        $appointment->end_time = $this->getCurrentDateTime();
        $appointment->status = 'approved';
        $todayApprovedBooking = $this->getTotalBookingCount($appointment, $user, $isSuperUser);

        $appointment->status = 'pending';
        $todayPendingBooking = $this->getTotalBookingCount($appointment, $user, $isSuperUser);

        // current month appointments.
        $appointment->start_time = $cur_start;
        $appointment->end_time = $cur_end;
        $appointment->status = 'approved';
        $totalBooking = $this->getTotalBookingCount($appointment, $user, $isSuperUser);

        $appointment->status = 'pending';
        $totalFutureBooking = $this->getTotalBookingCount($appointment, $user, $isSuperUser);
        //
        $showCustomerCount = $isSuperUser;
        $totalCustomer = $isSuperUser ? $this->getCustomerCount() : -1;
        $totalCustomerLastWeek = $isSuperUser ? $this->getCustomerCount($this->getCurrentDateTime()->modify('-7 days')->format(BaseController::$dateTimeFormat)) : -1;
        //weekly sales.
//        $time = new Carbon(strtotime('sunday this week'));   // testing use.
        $showSalesChart = $isSuperUser;
        $currentWeekSales = $isSuperUser ? $this->getSalesAmounts(strtotime('monday this week'), strtotime('sunday this week'), $user, $isSuperUser) : [];
        $lastWeekSales = $isSuperUser ? $this->getSalesAmounts(strtotime('monday last week'), strtotime('sunday last week'), $user, $isSuperUser) : [];
        //
        $showUpcomingAppointments = true;
        $appointments = $this->getCustomerBookings($user, $isSuperUser);
        //
        $showPayment = $isSuperUser;
        $orderSearch = new Order;
        $orderSearch->order_date = $this->getCurrentDateTime();
        $totalSalesToday = $this->getPaymentAmount($orderSearch, $user, $isSuperUser, 0)->total_sales;
        if (empty($totalSalesToday)) $totalSalesToday = 0;
        $orderSearch->payment_status = 'pending';
        $totalUnpaidToday = $this->getPaymentAmount($orderSearch, $user, $isSuperUser, 0)->total_paid;
        if (empty($totalUnpaidToday)) $totalUnpaidToday = 0;

        //
        $noOfNewNotifications = $this->getNotificationCount($user);
        $role = $user->role->name;
        $reminingPackages = $this->getRemainingPackages($user);
        $showFinance = config("app.jws.settings.finance");
        $checkInBeforeMinute = config("app.jws.settings.checkin_before_minute");
        if ($request->expectsJson()) {
            return compact(
                'showBookingCount',
                'todayApprovedBooking',
                'todayPendingBooking',
                'totalBooking',
                'totalFutureBooking',
                'showCustomerCount',
                'totalCustomer',
                'totalCustomerLastWeek',
                'showPayment',
                'role',
                'totalSalesToday',
                'totalUnpaidToday',
                'showSalesChart',
                'currentWeekSales',
                'lastWeekSales',
                'noOfNewNotifications',
                'showUpcomingAppointments',
                'appointments',
                'isTrainerUser',
                'showFinance',
                'checkInBeforeMinute',
                'reminingPackages'
//                'time'
            );
        }
        return view("dashboard.index", compact('showBookingCount', 'totalBooking', 'totalFutureBooking'));
    }

    private function getTotalBookingCount($appointment, $user, bool $superUser) {
        $booking = DB::table('customer_bookings')
            ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
            ->whereIn('appointments.status',  ['approved', 'pending']);
        if (!$superUser) {
            $booking->where('customer_id', $user->id);
        }
        if ($appointment) {
            if ($appointment->start_time && $appointment->end_time) {  // between
                $booking->whereRaw('date(appointments.start_time) between ? and ?', [$appointment->start_time->format(BaseController::$dateFormat), $appointment->end_time->format(BaseController::$dateFormat)]);
            }
            else if ($appointment->end_time) {   // equal to
                $booking->whereRaw('date(appointments.start_time)=?', $appointment->end_time->format(BaseController::$dateFormat));
            }
            else if ($appointment->start_time) {  // greater than
                $booking->whereRaw('date(appointments.start_time)>?', $appointment->start_time->format(BaseController::$dateFormat));
            }
            if ($appointment->status) {
                $booking->where('appointments.status', $appointment->status);
            }
        }
        return $booking->count();
    }

    private function getPaymentAmount($orderSearch, $user, bool $superUser, $day) {
        $orders = DB::table('orders')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->select(DB::raw('SUM(order_total-discount) as total_sales, SUM(payments.amount) as total_paid'))
            ->whereBetween('order_date', [$orderSearch->order_date->format(BaseController::$dateFormat), $orderSearch->order_date->modify($day . ' days')->format(BaseController::$dateFormat)])
            ->whereIn('order_status',  ['confirmed', 'pending']);
        if (!$superUser) {
            $orders->where('customer_id', $user->id);
        }
        if ($orderSearch) {
            if ($orderSearch->payment_status == 'pending') {
                $orders->whereIn('payment_status', ['pending', 'partially']);
            }
        }
        return $orders->first();
    }

    private function getSalesAmounts($fromDate, $toDate, $user, bool $superUser) {
        $orders = DB::table('orders')
            ->selectRaw('SUM(order_total-discount) as total_sales, order_date')
            ->whereBetween('order_date', [new Carbon($fromDate), new Carbon($toDate)])
            ->whereIn('order_status', ['confirmed', 'pending'])
            ->orderBy('order_date', 'asc')
            ->groupBy('order_date');
        if (!$superUser) {
            $orders->where('customer_id', $user->id);
        }
        $weekdata = $orders->get();
        $result = [];
        while ($fromDate <= $toDate) {
            $found = false;
            foreach ($weekdata as $d) {
//                echo 'order_date' . $d->order_date . ', ' . date(BaseController::$dateFormat, $fromDate);
                if ($d->order_date == date(BaseController::$dateFormat, $fromDate)) {
                    $found = true;
                    $result[] = $d->total_sales;
                    break;
                }
            }
            if (!$found) {   // if day total sales not found from DB.
                $result[] = 0;
            }
            $fromDate = strtotime("+1 days", $fromDate);
        }
        return $result;
    }

    private function getCustomerCount($fromDate = null) {
        $booking = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.name',  ['user', 'member']);
        if ($fromDate != null) {
            $booking->where('users.created_at', '>=', $fromDate);
        }
        return $booking->count();
    }

    /**
     * Shall we get Appointments(only) or Customer Booking?
     *
     * @param $user
     * @param $isSuperUser
     * @return \Illuminate\Support\Collection
     */
    private function getCustomerBookings($user, $isSuperUser)
    {
        $filterDate = $this->getCurrentDateTime();
        $bookings = DB::table('customer_bookings')
            ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
            ->join('rooms', 'appointments.room_id', '=', 'rooms.id')
            ->select('customer_bookings.*',
                DB::raw("(select a.name from users a, roles b where a.id=appointments.user_id and a.role_id=b.id and b.name in ('manager', 'internal_coach', 'external_coach')) as user_name"),
                DB::raw('(select name from users where id=customer_bookings.customer_id) as customer_name'),
                DB::raw('(select name from packages where id=appointments.package_id) as package_name'),
                DB::raw('(select color_name from roles where id=appointments.user_id) as role_color_name'),
                DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'),
                DB::raw('(select payments.status from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_status'),
                'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name', 'rooms.color')
            ->whereIn('appointments.status', ['pending', 'approved'])
            ->where('appointments.start_time', '>=',  $filterDate)
            ->orderBy('appointments.start_time', 'asc')
            ->orderBy('rooms.name', 'asc');
        if ($isSuperUser) {
            // manager or above.
//        } else if ($this->isInternalCoachLevel($user)) {

        } else if ($this->isExternalCoachLevel($user)) {
            $bookings->where('user_id', $user->id);
        } else {
            $bookings->where('customer_id', $user->id);
        }

        return $bookings->get();

    }

    private function getNotificationCount($user) {
        return NotifyMessage::where('customer_id', $user->id)
            ->where('has_read', 0)
            ->count();
    }

    private function getRemainingPackages($user) {
        $filterDate = $this->getCurrentDateTime();
        $results = [];
        // fixed schedule packages.
        $order_packages = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('customer_bookings', 'order_details.booking_id', '=', 'customer_bookings.id')
            ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
            ->join('packages', 'appointments.package_id', '=', 'packages.id')
            ->selectRaw('count(*) as remaining, max(date(appointments.start_time)) as last_lesson_date, package_id, packages.name, order_id, orders.recurring')
            ->where('order_details.order_type', 'package')   // package only orders.
            ->where('appointments.status', 'approved')
            ->where('appointments.package_id', '>', 0)
            ->where('appointments.start_time', '>',  $filterDate)
            ->where('orders.customer_id', $user->id)
            ->groupBy('package_id')
            ->groupBy('packages.name')
            ->groupBy('order_id')
            ->groupBy('orders.recurring')
            ->orderBy('packages.name', 'asc')
            ->get();

        foreach ($order_packages as $order) {
            $results[] = ['order_id' => $order->order_id, 'name' => $order->name, 'remaining' => $order->remaining, 'last_lesson_date' => $order->last_lesson_date, 'recurring' => $order->recurring];
        }

        // calculate hourly.
        $order_courses = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
//            ->selectRaw("(select sum(TIMESTAMPDIFF(minute, z.start_time, z.end_time)) from order_details x, customer_bookings y, appointments z where x.order_id=orders.id and x.order_type='used_token' and x.booking_id=y.id and y.appointment_id=z.id and z.start_time < ?) as used_minutes, (select max(date(z.start_time)) from order_details x, customer_bookings y, appointments z where x.order_id=orders.id) as last_lesson_date, orders.recurring, order_details.order_id", [$filterDate])
            ->selectRaw("(select sum(JSON_EXTRACT(order_description, '$.no_of_session')) from order_details x, customer_bookings y, appointments z where x.order_id=orders.id and x.order_type='used_token' and x.booking_id=y.id and y.appointment_id=z.id and z.start_time < ?) as used_sessions, (select max(date(z.start_time)) from order_details x, customer_bookings y, appointments z where x.order_id=orders.id) as last_lesson_date, orders.recurring, order_details.order_id", [$filterDate])
            ->where('order_details.order_type', 'token')
            ->where('orders.customer_id', $user->id)
            ->get();
        foreach ($order_courses as $order) {
            $recurring = json_decode($order->recurring);
            // $recurring->quantity is in terms of 'hour'.
            $order_remaining = $recurring->quantity;
            if ($order->used_sessions > 0)
                $order_remaining -= ($order->used_sessions / $recurring->no_of_session);
            $results[] = ['order_id' => $order->order_id, 'name' => $recurring->package->name, 'remaining' => $order_remaining, 'last_lesson_date' => $order->last_lesson_date, 'recurring' => $order->recurring];
        }
        return $results;
    }
}
