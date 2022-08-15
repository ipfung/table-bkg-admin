<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
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
            if ($request->payment_status > 0)
                $payments->where('payment_status', $request->payment_status);
        }

        if ($request->has('customer_id')) {
            if ($request->customer_id > 0)
                $payments->where('customer_id', $request->customer_id);
        }

        if ($request->expectsJson()) {
//            return $payments->get();
            return $payments->with('customer', 'details', 'payments')->paginate();
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

}
