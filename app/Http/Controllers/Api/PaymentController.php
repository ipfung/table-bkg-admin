<?php

namespace App\Http\Controllers\Api;

use App\Facade\OrderService;
use App\Facade\PermissionService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PermissionService $permissionService, OrderService $orderService)
    {
        parent::__construct($permissionService);
        $canAccess = config("app.jws.settings.finance");
        if (!$canAccess) {
            abort(404);
        }
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $withRelationship = ['customer.role', 'details', 'payment'];

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
            if ($request->payment_status == 'unpaid') {
                $payments->whereIn('payment_status', ['pending', 'partially']);
            }
            else if (!empty($request->payment_status))
                $payments->where('payment_status', $request->payment_status);
        }
        if ($request->has('order_type')) {
            if ($request->order_type == 'commission') {
                $payments->whereRaw('id in (select order_id from order_details where order_type=?)', 'commission' . ($this->isSuperLevel($user) ? '' : '167'));
                $withRelationship[] = 'trainer';
            }
        }
        if ($request->has('trainer_id')) {
            $payments->where('trainer_id', $request->trainer_id);
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
            $data = $payments->with($withRelationship)->paginate()->toArray();
            $data['showCustomer'] = $showCustomer;   // append to paginate()
            return $data;
        }
        return view("finance.list", $payments);
    }

    /**
     * should be created from Appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $user = Auth::user();
//        $request->validate([
//            'order_total' => 'required',
//            'order_status' => 'required',
//            'payment_status' => 'required',
//        ]);
//
//        DB::beginTransaction();
//        $order = new Order;
//        $order->order_number = uniqid();
//        $order->order_date = Carbon::today()->format('Y-m-d');
//        if ($request->has('discount')) {
//            if ($request->discount > 0)
//                $order->discount = $request->discount;
//        }
//        $order->customer_id = $request->customer_id;
//        $order->user_id = $user->id;
//        $order->order_total = $request->order_total;
//        $order->paid_amount = $request->order_total;
//        $order->payment_status = $request->payment_status;
//        $order->order_status = $request->order_status;
//        if ($request->has('recurring')) {
//            $recurring = $request->input('recurring');
//            $order->recurring = json_encode($recurring);
//        }
//        $order->repeatable = $request->has('repeatable' ) ? $request->repeatable : false;
//        $order->save();
//
//        $orderDetail = new OrderDetail;
//        $orderDetail->order_id = $order->id;
//        $orderDetail->order_type = $request->order_type;
//        $orderDetail->original_price = $order->order_total;
//        $orderDetail->discounted_price = $order->order_total;
//        $orderDetail->save();
//
//        $payment = new Payment;
//        $payment->order_id = $order->id;
//        $payment->amount = $order->order_total;
//        $payment->payment_date_time = (new DateTime())->format('Y-m-d H:i:s');
//        $payment->status = $order->payment_status;
//        $payment->payment_method = 'onsite';
//        $payment->gateway = 'cash';
////        $payment->parent_id = ;
//        $payment->entity = 'commission';
//        $payment->save();
//
//        DB::commit();
//        $result = ["success" => true, "data" => $order];
//        return $result;
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
            'amount' => 'required',
            'gateway' => 'required',
        ]);
        $payment = Payment::find($id);
        $payment->amount = $request->amount;
        $payment->gateway = $request->gateway;
        $payment->status = ($payment->amount > 0) ? 'paid' : 'pending';
        DB::beginTransaction();
        $payment->save();
        $paymentStatus = $this->updatePaymentStatusByOrderId($payment->order_id);
        DB::commit();
        $success = true;

        return compact('success', 'paymentStatus');
    }

    /**
     * update orders table payment_status and order_status.
     * @param $order_id
     * @return string
     */
    private function updatePaymentStatusByOrderId($order_id) {
        $order = Order::find($order_id);
        $payment = Payment::where('order_id', $order_id)
//            ->where('status', 'paid')
//            ->selectRaw(DB::raw('sum(amount) as total_paid'))
            ->first();
        if ($payment) {
            $order_amount = $order->order_total - $order->discount;;
            if ($payment->status == 'paid') {
                $order->paid_amount = $payment->amount;
            }
            if ($order->paid_amount == 0) {
                $order->payment_status = 'pending';
            } if ($order->paid_amount >= $order_amount) {
                $order->payment_status = 'paid';
                // change the order status as well.
                if ($order->order_status != 'confirmed') {
                    $order->order_status = 'confirmed';
                }
            } else if ($order->paid_amount > 0 && ($order_amount - $order->paid_amount) > 0) {
                $order->payment_status = 'partially';
                // change the order status as well.
                if ($order->order_status != 'confirmed') {
                    $order->order_status = 'confirmed';
                }
            }
        } else {
            $order->payment_status = 'pending';
        }
        $order->save();
        return $order->payment_status;
    }

    public function sendBillReminder($id) {
        $order = Order::find($id);
        if ($order->payment_status != 'paid') {
            $resp = $this->orderService->sendOrderNotifications('payment_reminder', $order, Auth::user()->id);
            if ($resp == -1) {    // no notifications being sent.
                return ['success' => true, 'order_id' => $order->id, 'notifications' => false];
            } else {    // some notifications are sent.
                $resp['success'] = true;
                $resp['order_id'] = $order->id;
//                    $resp['placeholders'] = $payload['placeholders'];
                return $resp;
            }
        }
    }

    public function showInvoice(Request $request, $id)
    {
        $uris = explode('/', $request->getRequestUri());
        $order = Order::find($id);
        $data = [
            'order' => $order,
            'uri' => $uris[2]    // invoice or receipt, show diff title
        ];
        return view('orders.invoice', $data);
        // $pdf = PDF::loadView('student.orders.receipt', $data);
        // return $pdf->stream();
    }
}
