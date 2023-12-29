<?php

namespace App\Http\Controllers\Api;

use App\Facade\OrderService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DateTime;
use stdClass;

class PaymentController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
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
        $withRelationship = ['customer.role', 'details.booking.appointment.user', 'payment'];

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
            } else {
                $payments->whereRaw('id in (select order_id from order_details where order_type=?)', $request->order_type);
            }
        }
        if ($request->has('trainer_id')) {
            $payments->where('trainer_id', $request->trainer_id);
        }
        $showCustomer = false;
        if ($this->isExternalCoachLevel($user)) {
            if ($request->has('customer_id')) {
                $payments->where('customer_id', $request->customer_id);
            }
            // external coach gets their own customers only
            if (!$this->isInternalCoachLevel($user)) {
                $payments->where('trainer_id', $user->id);
            }
            $showCustomer = true;
        } else {
            $payments->where('customer_id', $user->id);
        }

        if ($request->expectsJson()) {
//            return $payments->get();
            // ref: https://stackoverflow.com/questions/52559732/how-to-add-custom-properties-to-laravel-paginate-json-response
            $data = $payments->with($withRelationship)->with('details.booking.appointment.room')->paginate()->toArray();
            $data['paymentGateway'] = (config("app.jws.settings.payment_gateway") != false);
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
        $user = Auth::user();
        $request->validate([
            'order_total' => 'required',
            'order_status' => 'required',
            'payment_status' => 'required',
        ]);
        $locationId = 1;

        DB::beginTransaction();
        $order = new Order;
        $order->order_number = $this->orderService->genOrderNo($locationId);
        $order->order_date = Carbon::today()->format('Y-m-d');
        if ($request->has('discount')) {
            if ($request->discount > 0)
                $order->discount = $request->discount;
        }
        $order->customer_id = $request->customer_id;
        $order->user_id = $user->id;
        $order->order_total = $request->order_total;
        $order->order_status = $request->order_status;
        $recurring = $request->input('recurring');
        if ($request->has('recurring')) {
            $order->recurring = json_encode($recurring);
        }
        $order->repeatable = $request->has('repeatable' ) ? $request->repeatable : false;
        $order->paid_amount = $request->payment_amount;
        $order->payment_status = $request->payment_status;
        $order->save();

        if ($request->order_type == 'token' && $request->has('recurring')) {
            // token based can be used
            $orderDetail = new OrderDetail;
            $orderDetail->order_id = $order->id;
            $orderDetail->order_type = $request->order_type;
            $orderDetail->original_price = $order->order_total;
            $orderDetail->discounted_price = $order->order_total;
            $orderDetail->order_description = $order->recurring;
            $orderDetail->save();
            // token based, loop the quantity? how to handle free sessions?
//            foreach (range(1, $recurring['quantity']) as $i) {
//                $orderDetail = new OrderDetail;
//                $orderDetail->order_id = $order->id;
//                $orderDetail->order_type = $request->order_type;
//                $orderDetail->original_price = 0;
//                $orderDetail->discounted_price = 0;
//                $order_description = new stdClass;
//                $order_description->quantity = 1;
//                $order_description->no_of_session = $recurring["no_of_session"];
//                $orderDetail->order_description = json_encode($order_description);
//                $orderDetail->booking_id = 0;
//                $orderDetail->save();
//            }
            // free class, due to it must be used as per free hour, create OrderDetail by free.quantity.
            if ($recurring["free"]) {
                foreach (range(1, $recurring["free"]["quantity"]) as $i) {
                    $orderDetail = new OrderDetail;
                    $orderDetail->order_id = $order->id;
                    $orderDetail->order_type = 'free_' . $request->order_type;
                    $orderDetail->original_price = 0;
                    $orderDetail->discounted_price = 0;
                    $order_description = new stdClass;
                    $order_description->quantity = 1;
                    $order_description->no_of_session = $recurring["free"]["no_of_session"];
                    $orderDetail->order_description = json_encode($order_description);
                    $orderDetail->booking_id = 0;
                    $orderDetail->save();
                }
            }
        } else {
            $orderDetail = new OrderDetail;
            $orderDetail->order_id = $order->id;
            $orderDetail->order_type = $request->order_type;
            $orderDetail->original_price = $order->order_total;
            $orderDetail->discounted_price = $order->order_total;
            $orderDetail->order_description = $order->recurring;
            $orderDetail->save();
        }

        $payment = new Payment;
        $payment->order_id = $order->id;
        $payment->amount = $order->paid_amount;
        $payment->payment_date_time = (new DateTime())->format('Y-m-d H:i:s');
        $payment->status = $order->payment_status;
        $payment->payment_method = 'onsite';
        $payment->gateway = $request->payment_gateway;
//        $payment->parent_id = ;
//        $payment->entity = 'commission';
        $payment->save();

        DB::commit();
        $result = ["success" => true, "data" => $order];
        return $result;
    }

    /**
     * Display the payment status.
     *
     * @param  int  $id the payment id, NOT order id.
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payment::find($id);
        $user = Auth::user();
        if ($payment->order->customer_id == $user->id || $this->isInternalCoachLevel($user)) {
            $can_read_time = DateTime::createFromFormat(self::$dateTimeFormat, $payment->payment_date_time)->modify('2 minutes');
            $now = $this->getCurrentDateTime();
            if (strtotime($now->format(self::$dateTimeFormat)) < strtotime($can_read_time->format(self::$dateTimeFormat))) {
                return $this->sendResponse($payment, strtotime($now->format(self::$dateTimeFormat)) . "==" . strtotime($can_read_time->format(self::$dateTimeFormat)));
            }
            return $this->sendError("Expired to read this.");
        }
        return $this->sendError("You don't have permission to read this.");
    }

    /**
     * Display the payment status.
     *
     * @param  int  $id the payment id, NOT order id.
     * @return \Illuminate\Http\Response
     */
    public function showOrder($id)
    {
        $order = Order::find($id);
//        return $order->customer;
        $validOrder = $this->orderService->getValidTokenBasedOrder($order->customer, $order->id);
        return $this->sendResponse($validOrder, "Found valid order");
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
