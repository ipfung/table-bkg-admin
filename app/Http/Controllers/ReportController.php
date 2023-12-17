<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exports\OrderExport;
use App\Exports\SaleExport;
//use App\Exports\SharesExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    //
    public function orderReport(Request $request){
       /*  $orders = Order::with('customer')->OrderBy('id', 'DESC');
        if ($request->has('start_date')) {
            if ($request->start_date != '' ){
                if ($request->has('end_date')) {
                    if ($request->end_date != '' ){
                        $from = date($request->start_date);
                        $to = date($request->end_date);
                        $orders->whereBetween('order_date', [$from, $to]);
                        //ddd($to);
                    }
                }
            }
        
        }
        if ($request->has('search_payment_status')) {
            if ($request->search_payment_status != '' ){
                $orders->where('payment_status', $request->search_payment_status);
            }
        } */
        $orders = Order::orderby('id');
       return  $orders->get();

        return view("reports.orders", ['orders' => $orders->paginate(20)] );
    }

    public function orderReportExport(Request $request)
    {
       // ddd($request->start_date);
       //return Excel::download(new OrderExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Orders.xlsx');
       return Excel::download(new OrderExport(), 'Report Orders.xlsx');
    }

    public function orderReportExport1(Request $request)
    {
       // ddd($request->start_date);
        $data = $this->orderReport($request); 
        return Excel::download(new SaleExport($data), 'Report Sales.xlsx');
        //return Excel::download(new SalesExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Sales.xlsx');
    }
}
