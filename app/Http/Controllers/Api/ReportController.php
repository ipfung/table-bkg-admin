<?php

namespace App\Http\Controllers\Api;

use App\Exports\OrderExport;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function salesReport(Request $request)
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

        //return view("reports.sales", ['orders' => $orders->paginate(20)] );
    }

    public function exportXlsxSalesReport1(Request $request)
    {
       // ddd($request->start_date);
        return Excel::download(new OrderExport(), 'Report Sales.xlsx');
        //return Excel::download(new SalesExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Sales.xlsx');
    }

    public function exportXlsxSalesReport(Request $request, $id)
    {
        /* $uris = explode('/', $request->getRequestUri());
        $order = Order::find($id); */
        $data = [
            //'order' => $order,
            'uri' => "testing"    // invoice or receipt, show diff title
        ];
        return view('orders.invoice', $data);
        // $pdf = PDF::loadView('student.orders.receipt', $data);
        // return $pdf->stream();
    }

    public function orderReportExport(Request $request)
    {
       // ddd($request->start_date);
       //return Excel::download(new OrderExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Orders.xlsx');
       return Excel::download(new OrderExport(), 'Report Orders.xlsx');
    }



}
