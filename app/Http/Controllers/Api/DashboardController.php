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

        $futureApt = new Appointment;
        $futureApt->start_time = $this->getCurrentDateTime();
        // general
        $showBookingCount = $isSuperUser;
        $totalBooking = $this->getTotalBookingCount(null, $user, $isSuperUser);
        $totalFutureBooking = $this->getTotalBookingCount($futureApt, $user, $isSuperUser);
        //
        $showCustomerCount = $isSuperUser;
        $totalCustomer = $isSuperUser ? $this->getCustomerCount() : -1;
        $totalCustomerLastWeek = $isSuperUser ? $this->getCustomerCount($this->getCurrentDateTime()->modify('-7 days')->format($this->dateTimeFormat)) : -1;
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
        $totalSales = $this->getPaymentAmount(null, $user, $isSuperUser)->total_sales;
        $orderSearch = new Order;
        $orderSearch->payment_status = 'pending';
        $totalUnpaid = $this->getPaymentAmount($orderSearch, $user, $isSuperUser)->total_paid;
        //
        $noOfNewNotifications = $this->getNotificationCount($user);
        if ($request->expectsJson()) {
            return compact(
                'showBookingCount',
                'totalBooking',
                'totalFutureBooking',
                'showCustomerCount',
                'totalCustomer',
                'totalCustomerLastWeek',
                'showPayment',
                'totalSales',
                'totalUnpaid',
                'showSalesChart',
                'currentWeekSales',
                'lastWeekSales',
                'noOfNewNotifications',
                'showUpcomingAppointments',
                'appointments',
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
            if ($appointment->start_time) {
                $booking->where('appointments.start_time', '>', $appointment->start_time);
            }
        }
        return $booking->count();
    }

    private function getPaymentAmount($orderSearch, $user, bool $superUser) {
        $orders = DB::table('orders')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->select(DB::raw('SUM(order_total-discount) as total_sales, SUM(payments.amount) as total_paid'))
            ->where('order_date', '>', $this->getCurrentDateTime()->modify('-30 days')->format($this->dateTimeFormat))
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
//                echo 'order_date' . $d->order_date . ', ' . date($this->dateFormat, $fromDate);
                if ($d->order_date == date($this->dateFormat, $fromDate)) {
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

    private function getCustomerBookings($user, $isSuperUser)
    {
        $bookings = DB::table('customer_bookings')
            ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
            ->join('rooms', 'appointments.room_id', '=', 'rooms.id')
            ->select('customer_bookings.*',
                DB::raw('(select color_name from roles where id=appointments.user_id) as role_color_name'),
                DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'),
                DB::raw('(select payments.status from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_status'),
                'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name')
            ->whereIn('appointments.status', ['pending', 'approved'])
            ->where('appointments.start_time', '>=', $this->getCurrentDateTime() )
            ->orderBy('appointments.start_time', 'asc')
            ->orderBy('rooms.name', 'asc');
        if (!$isSuperUser) {
            $bookings->where('customer_id', $user->id);
        }

        return $bookings->get();

    }

    private function getNotificationCount($user) {
        return NotifyMessage::where('customer_id', $user->id)
            ->where('has_read', 0)
            ->count();
    }
}
