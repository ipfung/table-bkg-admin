<?php

namespace App\Facade;

use App\Models\Appointment;
use App\Models\CustomerBooking;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\TrainerRate;
use App\Models\User;
use App\Services\NotificationsService;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function sendOrderNotifications($tamplte_name, $order, $userId) {
        $placeholderService = new PlaceholderService();
        $notificationService = new NotificationsService();
        // send mail if notify option enabled.
        $payload = [
            'template' => $tamplte_name,
            'placeholders' => $placeholderService->getOrderData($order),
            // extra params.
            'data' => [
                'page' => 'order',
                'customer_name' => $order->customer->name,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'order_date' => $order->order_date
            ]
        ];
        $resp = $notificationService->sendToCustomer($order->customer, $payload, $userId);
        return $resp;
    }

    public function genOrderNo($location_id) {
        return uniqid();
    }

    public function getEncodeOrderNo($order_num, $order_id) {
        return base64_encode(date("YmdHis", time()) . $order_id . '_' . $order_num);
    }

    public function generateNextPackageLessons($packageId) {
    }

    public function getValidTokenBasedOrder($customer) {
        // check any valid token-based orders.
        $today = Carbon::now();
        $orders = Order::orderBy('order_date', 'DESC')
            ->limit(2)   // current month & last month.
            ->where('order_status', 'confirmed')
            ->where('customer_id', $customer->id)
            ->whereRaw("id in (select order_id from order_details where order_type in ('token', 'free_token'))")
            ->get();
        $order = null;
        $quantity = 0;
        $no_of_session = 0;
        foreach ($orders as $order) {
            $recurring = json_decode($order->recurring);
            if ($today->isBetween($recurring->start_date, $recurring->end_date)) {
                foreach ($order->details as $orderdtl) {
                    if ($orderdtl->order_type == 'token' && $orderdtl->booking_id == 0) {
                        $quantity += $orderdtl->description->quantity;
                        $no_of_session = $orderdtl->description->no_of_session;
                    }
                }
                $customerTrainerRates = TrainerRate::where('student_id', $customer->id)->select('trainer')->get();
                $trainers = [];
                foreach ($customerTrainerRates as $trainerRate) {
                    $trainer = User::find($trainerRate->trainer);
                    $trainers[] = ["id" => $trainer->id, "name" => $trainer->name, "avatar" => $trainer->avatar, "mobile_no" => $trainer->mobile_no];
                }
                if ($quantity > 0)
                    $order = ['trainers' => $trainers, 'order_number' => $order->order_number, 'token_quantity' => $quantity, 'no_of_session' => $no_of_session];
                break;
            }
        }
        return $order;
    }

    public function generateNextOrder($orderId, $createdUserId) {
        // ref: https://stackoverflow.com/questions/18861186/eloquent-eager-load-order-by
        $oldOrder = Order::find($orderId)->with(array('details' => function($query) {
            $query->where('order_type', 'package');
            $query->orderBy('start_time', 'ASC');
        }));

        $recurring = json_decode($oldOrder->recurring);
        // get next lessons
        $lastLesson = end($oldOrder->details);
        $nextAppointments = Appointment::orderBy('start_time')
            ->where('package_id', $lastLesson->package_id)
            ->where('start_time', '>', $lastLesson->start_time)
            ->limit($recurring->quantity)
            ->get();
        if ($recurring->quantity != count($nextAppointments)) {
            // TODO need throw exception if not match no of appointments?
        }

        DB::beginTransaction();

        $order = new Order;
        $order->parent_id = $oldOrder->id;    // this is important.
        $order->order_number = uniqid();
        $order->order_date = Carbon::today()->format('Y-m-d');
        $order->order_total = $oldOrder->order_total;
        $order->discount = $oldOrder->discount;
        $order->customer_id = $oldOrder->customer_id;
        $order->user_id = $createdUserId;
        $order->paid_amount = 0;
        $order->payment_status = 'pending';
        $order->order_status = 'pending';
        $order->recurring = $oldOrder->recurring;
        $order->repeatable = $oldOrder->repeatable;
        $order->trainer_id = $oldOrder->trainerId;
        $order->commission = $oldOrder->commission;
        $order->save();

        foreach ($nextAppointments as $appointment) {
            // find appointment by package id and then create customer booking & order detail.
            $customerBooking = new CustomerBooking;
            $customerBooking->appointment_id = $appointment->id;
            $customerBooking->customer_id = $order->customer_id;
            $customerBooking->price = $lastLesson->price;
            $customerBooking->info = $lastLesson->info;
            $customerBooking->revised_appointment_id = $appointment->id;
            $customerBooking->revision_counter = 0;
            $customerBooking->save();

            //put booking id into appointment for OrderDetail use.
            $appointment->customer_booking_id = $customerBooking->id;

            $orderDetail = new OrderDetail;
            $orderDetail->order_id = $order->id;   // the new order id.
            $orderDetail->order_type = 'package';
            $orderDetail->booking_id = $customerBooking->id;
            $orderDetail->order_description = json_encode($appointment);
            $orderDetail->original_price = $lastLesson->price;
            $orderDetail->discounted_price = $lastLesson->price;
            $orderDetail->coupon_id = $lastLesson->coupon_id;
            $orderDetail->save();
        }

        $payment = new Payment;
        $payment->order_id = $order->id;
        $payment->amount = $order->order_total;
        $payment->payment_date_time = (new DateTime())->format('Y-m-d H:i:s');
        $payment->status = $order->payment_status;
        $payment->payment_method = '';
        $payment->gateway = '';
//        $payment->parent_id = ;
        $payment->entity = 'package';
        $payment->save();

        DB::commit();

        return $order;
    }
}
