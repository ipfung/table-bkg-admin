<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Services\UserDeviceService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderCommissionController extends PaymentController
{
    /**
     * update amount and payment status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_total' => 'required',
            'commission' => 'required',
            'order_status' => 'required',
            'payment_status' => 'required',
        ]);
        $user = Auth::user();

        DB::beginTransaction();
        $order = new Order;
        $order->order_number = uniqid();
        $order->order_date = Carbon::today()->format('Y-m-d');
        if ($request->has('discount')) {
            if ($request->discount > 0)
                $order->discount = $request->discount;
        }
        $order->customer_id = $request->customer_id;
        $order->order_total = $request->order_total;
        $order->paid_amount = $request->order_total;
        $order->trainer_id = $request->trainer_id;
        $order->commission = $request->commission;
        $order->payment_status = $request->payment_status;
        $order->order_status = $request->order_status;
        $order->user_id = $user->id;
        if ($request->has('recurring')) {
            $recurring = $request->input('recurring');
            $order->recurring = json_encode($recurring);
        }
        $order->repeatable = $request->has('repeatable' ) ? $request->repeatable : false;
        $order->save();

        $orderDetail = new OrderDetail;
        $orderDetail->order_id = $order->id;
        $orderDetail->order_type = $request->order_type;
        $orderDetail->order_description = "";
        $orderDetail->original_price = $order->order_total;
        $orderDetail->discounted_price = $order->order_total;
        $orderDetail->save();

        $payment = new Payment;
        $payment->order_id = $order->id;
        $payment->amount = $order->order_total;
        $payment->payment_date_time = (new DateTime())->format('Y-m-d H:i:s');
        $payment->status = $order->payment_status;
        $payment->payment_method = 'onsite';
        $payment->gateway = 'cash';
//        $payment->parent_id = ;
        $payment->entity = 'commission';
        $payment->save();

        DB::commit();
        $result = ["success" => true, "data" => $order];
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_total' => 'required',
            'commission' => 'required',
        ]);
        $user = Auth::user();
        $order = Order::find($id);

        DB::beginTransaction();
        $order->customer_id = $request->customer_id;
        $order->order_total = $request->order_total;
        $order->paid_amount = $request->order_total;
        $order->commission = $request->commission;
        $order->user_id = $user->id;
        $order->save();

        $orderDetail = $order->details[0];
        $orderDetail->original_price = $order->order_total;
        $orderDetail->discounted_price = $order->order_total;
        $orderDetail->save();

        $payment = $order->payments[0];
        $payment->amount = $order->order_total;
        $payment->save();
        DB::commit();
        $success = true;

        return compact('success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!empty($order)) {
            $order->delete();
            return response()->json(['success'=>true]);
        } else {
            return response()->json(['success'=>false, 'message' => 'Order not found.']);
        }
    }
}
