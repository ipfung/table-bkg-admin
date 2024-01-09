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
use Illuminate\Support\Facades\Auth;
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

    public function getValidTokenBasedOrder($customer, $order_id) {
        // check any valid token-based orders.
        $user = Auth::user();
        $isInternalCoach = PermissionService::isExternalCoachLevel($user);
        DB::enableQueryLog(); // Enable query log
        $today = Carbon::today();
        $orders = Order::orderBy('order_date', 'DESC');
        if ($order_id) {
            $orders->where('id', $order_id);
        } else {
            $orders->limit(2);   // current month & last month.
        }
        $orders = $orders->where('order_status', 'confirmed')
            ->where('customer_id', $customer->id)
            ->whereRaw("id in (select order_id from order_details where order_type in (?, ?))", [OrderDetail::$TYPE_TOKEN, OrderDetail::$TYPE_FREE_TOKEN])
            ->get();
//      $orders->get()    // debug
//        ;
//        $aaa = DB::getQueryLog(); // debug, Show results of log
//        $results = end($aaa);    // debug
//        return $this->sendResponse($results, "ok");    // debug
        $result = null;
        $quantity = 0;
        $no_of_session = 0;
        $free_quantity = 0;
        $free_no_of_session = 0;
        $usedTokenSession = 0;
        foreach ($orders as $order) {
            $recurring = json_decode($order->recurring);
            if ($today->isBefore($recurring->end_date)) {   // valid order which end_date still greater than today.
                foreach ($order->details as $orderdtl) {
                    if ($orderdtl->order_type == OrderDetail::$TYPE_TOKEN && !$orderdtl->booking_id) {
                        // token-based order detail stores the total token that customer can book.
                        $quantity = $orderdtl->description->quantity;
                        $no_of_session = $orderdtl->description->no_of_session;
                    } else if ($orderdtl->order_type == OrderDetail::$TYPE_FREE_TOKEN && $orderdtl->booking_id == 0) {
                        // free-token order detail stores each free-session per record.
                        $free_quantity += $orderdtl->description->quantity;
                        $free_no_of_session = $orderdtl->description->no_of_session;
                    } else if ($orderdtl->order_type == OrderDetail::$TYPE_USED_TOKEN) {
                        $usedTokenSession += $orderdtl->description->no_of_session;
                    }
                }
                if ($usedTokenSession > 0) {
                    $quantity -= ($usedTokenSession / $no_of_session);
                }
                if ($quantity > 0) {
                    $customerTrainerRates = TrainerRate::where('student_id', $customer->id)
                        ->where('rate_type', TrainerRate::ONE_TO_ONE_MONTHLY)  // shall get ONE_TO_ONE_MONTHLY only?
                        ->get();
                    $trainers = [];
                    foreach ($customerTrainerRates as $trainerRate) {
                        $trainer = User::where('id', $trainerRate->trainer)->with('role')->first();
                        $trainer->rate_type = $trainerRate->rate_type;
                        if ($isInternalCoach) {
                            // don't disclose charge to student.
                            $trainer->trainer_charge = $trainerRate->trainer_charge;
                            $trainer->trainer_commission = $trainerRate->trainer_commission;
                            $trainer->company_income = $trainerRate->company_income;
                        }
                        $trainers[] = $trainer;
                    }
                    $result = ['trainers' => $trainers, 'customer_id' => $customer->id, 'order_number' => $order->order_number, 'token_quantity' => $quantity, 'no_of_session' => $no_of_session, 'free_quantity' => $free_quantity, 'free_no_of_session' => $free_no_of_session, 'start_date' => $recurring->start_date, 'end_date' => $recurring->end_date];
                } else if ($free_quantity > 0) {
                    // don't return token-based qty
                    $result = ['trainers' => null, 'customer_id' => $customer->id, 'order_number' => $order->order_number, 'free_quantity' => $free_quantity, 'free_no_of_session' => $free_no_of_session, 'start_date' => $recurring->start_date, 'end_date' => $recurring->end_date];
                }
                break;
            }
        }
//        echo "found333," . json_encode($result);
        return $result;
    }

    /**
     * generate next Monthly order by specific order Id, update old order 'repeatable' to 11 once done.
     * @param $orderId
     * @param $createdUserId
     * @return mixed
     */
    public function generateNextMonthlyOrder($orderId, $createdUserId) {
        // ref: https://stackoverflow.com/questions/18861186/eloquent-eager-load-order-by
        $oldOrder = Order::where('id', $orderId)
            ->whereExists(function ($query) {
                $renew_date = Carbon::now()->addDays(7);
                $query->select(DB::raw(1))
                    ->from('order_details')
                    ->whereColumn('order_details.order_id', 'orders.id')
                    ->where('order_type', 'token')
                ->where('order_description->end_date', '<=', $renew_date);   // ref: https://stackoverflow.com/questions/69336034/laravel-eloquent-filter-from-a-value-of-json-column
            })
//            ->whereRaw('id in (select order_id from order_details where order_type=? and JSON_CONTAINS(order_description)->end_date<=?)', [OrderDetail::$TYPE_TOKEN, $renew_date])
//        with(array('details' => function($query) {
//            $query->where('order_type', 'token')
//                ->where('order_description->end_date', '<=', $renew_date);   // ref: https://stackoverflow.com/questions/69336034/laravel-eloquent-filter-from-a-value-of-json-column
//        }))
            ->first();
        if (empty($oldOrder)) {
            return false;
        }

        DB::beginTransaction();

        $order = $oldOrder->replicate();
        $order->parent_id = $oldOrder->id;    // this is important.
        $order->order_number = $this->genOrderNo($oldOrder->location_id);
        $order->order_date = Carbon::today()->format('Y-m-d');
        $order->paid_amount = 0;
        $order->payment_status = 'pending';
        $recurring = json_decode($oldOrder->recurring);
        $new_start_date = (new Carbon($recurring->end_date))->addDay();
        $recurring->start_date = $new_start_date->format('Y-m-d');
        $recurring->end_date = ($new_start_date->addMonth()->subDay())->format('Y-m-d');
        $order->recurring = json_encode($recurring);
        $order->save();

        //make old order repeatable to other value than 1.
        $oldOrder->repeatable = 1 + 10;
        $oldOrder->save();

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

        foreach ($oldOrder->details as $orderdtl) {
            if ($orderdtl->order_type == OrderDetail::$TYPE_TOKEN) {
                $dtl = new OrderDetail;
                $dtl->order_type = OrderDetail::$TYPE_TOKEN;
                $dtl->order_id = $order->id;
                $recurring = json_decode($orderdtl->order_description);
                $new_start_date = (new Carbon($recurring->end_date))->addDay();
                $recurring->start_date = $new_start_date->format('Y-m-d');
                $recurring->end_date = ($new_start_date->addMonth()->subDay())->format('Y-m-d');
                $dtl->order_description = json_encode($recurring);
                $dtl->original_price = 0;
                $dtl->discounted_price = 0;
                $dtl->save();
                if ($recurring->free)  {
                    $free_qty = $recurring->free->quantity;
                    for ($i=0; $i<$free_qty; $i++) {
                        $free_data = ["quantity" => 1, "no_of_session" => $recurring->free->no_of_session];
                        $orderDetail = new OrderDetail;
                        $orderDetail->order_id = $order->id;
                        $orderDetail->order_type = OrderDetail::$TYPE_FREE_TOKEN;
                        $orderDetail->booking_id = 0;
                        $orderDetail->order_description = json_encode($free_data);
                        $orderDetail->original_price = 0;
                        $orderDetail->discounted_price = 0;
                        $orderDetail->save();
                    }
                }
            }
        }
        DB::commit();

//        $this->sendOrderNotifications('order_created', $order, 99);

        return $order;
    }
}
