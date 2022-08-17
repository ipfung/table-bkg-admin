<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
use App\Services\UserDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $fromDate = Carbon::today()->format("Y-m-d");
        if ($request->has('from_date')) {
            $fromDate = $request->from_date;
        }
        $toDate = Carbon::today()->addDays(7)->format("Y-m-d");
        if ($request->has('to_date')) {
            $toDate = $request->to_date;
        }
        //
        $payments = Order::orderBy('order_date', 'desc')
            ->where('order_date', '>=', $fromDate )
            ->where('order_date', '<=', $toDate );
//            ->whereBetween('order_date', [$fromDate, $toDate]);

        if ($request->has('payment_status')) {
            if (!empty($request->payment_status))
                $payments->where('payment_status', $request->payment_status);
        }
        $showCustomer = false;
        if ($this->isSuperLevel($user)) {
            if ($request->has('customer_id')) {
                $payments->where('customer_id', $request->customer_id);
            }
            $showCustomer = true;
        } else {
            $payments->where('customer_id', $user->id);
        }

        if ($request->expectsJson()) {
//            return $payments->get();
            // ref: https://stackoverflow.com/questions/52559732/how-to-add-custom-properties-to-laravel-paginate-json-response
            $data = $payments->with('customer', 'details', 'payments')->paginate()->toArray();
            $data['showCustomer'] = $showCustomer;   // append to paginate()
            return $data;
        }
        return view("finance.list", $payments);
    }

    /**
     * Store both new or update of student list of trainer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required',
        ]);
        $teammates = $request->teammates;
        if ($request->counter == count($teammates)) {
            //
            $result = ["success" => true];
        } else {
            $result = ["success" => false, "error" => "Payment issue."];
        }
        return $result;
    }

    /**
     * Display the trainer resource with his/her student list.
     *
     * @param  int  $id the trainer id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trainer = User::find($id);
        return $trainer->with('teammates');
    }

    public function sendBillReminder($id) {
        $order = Order::find($id);
        if ($order->payment_status != 'paid') {
            $payload = [
                'title' => 'Payment Reminder',
                'body' => 'You have an unpaid invoice.',
                // extra params.
                'data' => [
                    'page' => 'finance',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]
            ];
            $responseCode = UserDeviceService::sendToCustomer($order->customer_id, $payload, Auth::user()->id);
            if ($responseCode <= 0)
                return ['success' => false, 'reason' => false];
            return ['success' => true, 'pushed' => true];
        }
    }
}
