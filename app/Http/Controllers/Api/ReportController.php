<?php

namespace App\Http\Controllers\Api;

use App\Exports\OrderExport;
use App\Exports\SaleExport;
use App\Exports\TrainerCommissionExport;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Appointment;
use App\Models\CustomerBookinghg;
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
    protected $s_date;
    protected $e_date; 

    public function index()
    {
        //
    }
   

    // Report Sales 
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
        $this->s_date = $fromDate;
        $this->e_date = $toDate;
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

        if ($request->has('exporttoexcel'))
        {
            if($request->exporttoexcel)
            {
                return $payments->get();
            }
        }
        if ($request->expectsJson()) {
//            return $payments->get();
            // ref: https://stackoverflow.com/questions/52559732/how-to-add-custom-properties-to-laravel-paginate-json-response
            $data = $payments->with($withRelationship)->with('details.booking.appointment.room')->paginate()->toArray();
            $data['paymentGateway'] = (config("app.jws.settings.payment_gateway") != false);
            $data['showCustomer'] = $showCustomer;   // append to paginate()
            $tmppayments1 =clone $payments;
            $tmppayments2 =clone $payments;
            $tmppayments3 =clone $payments;
            $totalamount =  $tmppayments1->sum('order_total') ;
           
            $paidamount = $tmppayments1->where('payment_status','=','paid')->sum('order_total') ;
            $unpaidamount = $tmppayments3->whereIn('payment_status', ['pending', 'partially'])->sum('order_total') ;
            $data['reportTotal'] = $totalamount;
            $data['reportPaidTotal'] = $paidamount;
            $data['reportUnpaidTotal'] = $unpaidamount;
            return $data;
        }
        return view("finance.list", $payments);

        //return view("reports.sales", ['orders' => $orders->paginate(20)] );
    }

    public function ordersReport()
    {
        $orders =Order::OrderBy('id');
        //ddd($orders->get());
        return $orders->get();
        
    }

    public function exportXlsxSalesReport1(Request $request)
    {
        $data = $this->salesReport($request);
        return Excel::download(new SaleExport($this->s_date, $this->e_date, $data), 'Report Sales.xlsx');
        //return Excel::download(new SalesExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Sales.xlsx');
    }

    /* public function exportXlsxSalesReport2(Request $request)
    {
       // ddd($request->start_date);
        return Excel::download(new OrderExport(), 'Report Sales.xlsx');
        //return Excel::download(new SalesExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Sales.xlsx');
    }

    public function exportXlsxSalesReport(Request $request, $id)
    {
        $uris = explode('/', $request->getRequestUri());
        $order = Order::find($id); 
        $data = [
            //'order' => $order,
            'uri' => "testing"    // invoice or receipt, show diff title
        ];
        return view('orders.invoice', $data);
        // $pdf = PDF::loadView('student.orders.receipt', $data);
        // return $pdf->stream();
    } */

    /* public function orderReportExport(Request $request)
    {
       // ddd($request->start_date);
       //return Excel::download(new OrderExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Orders.xlsx');
       return Excel::download(new OrderExport(), 'Report Orders.xlsx');
    } */

    // end Sales Report

    // Report Trainer commission 
    public function trainersCommissionReport(Request $request){

        $fromDate = Carbon::today()->format("Y-m-d");
        if ($request->has('from_date')) {
            $fromDate = $request->from_date;
        }
        $toDate = Carbon::today()->addDays(7)->format("Y-m-d");
        if ($request->has('to_date')) {
            $toDate = $request->to_date;
        }
        $this->s_date = $fromDate;
        $this->e_date = $toDate;
        //
        //$trainer_commissions = Appointment::orderby('user_id')->with('user')
       
                            //->orderBy('user.name');
                            // ->orderby('start_time', 'desc')
           
           // $trainer_commissions =  $trainer_commissions->with('customerBookings')
             //           ->with('customerBookings.customer');
            /*  $trainer_commissions = Appointment::with(['user' => function($query) {
                $query->orderBy('users.name', 'desc');
            }]); */
            //$trainer_commissions = Appointment::with('user');
            //->orderBy('users.name');
            /* $trainer_commissions =  $trainer_commissions->with('customerBookings')
                    ->with('customerBookings.customer'); */
        
                    
        $trainer_commissions =  Appointment::select(['appointments.*','users.name'])
        ->join('users','appointments.user_id','=','users.id')
        ->join('customer_bookings','appointments.id','=','appointment_id')
        ->where('start_time', '>=', $fromDate )
        ->where('start_time', '<=', $toDate );

        if ($request->has('trainerId')){
            $trainer_commissions = $trainer_commissions->where('user_id','=',$request->trainerId);
        }
        if ($request->has('ratetype')){
            $trainer_commissions = $trainer_commissions->where('rate_type','=',$request->ratetype);
        }
        $trainer_commissions =  $trainer_commissions
       
        //->with('customerBookings')
        ->with('customerBookings.customer')
        ->orderby('users.name')
        ->orderby('start_time');

        if ($request->has('exporttoexcel'))
        {
            if($request->exporttoexcel)
            {
              
                return $trainer_commissions->get();
            }
        }
        return $trainer_commissions->paginate()->toArray();
    }

    public function exportXlsxTrainerCommissionReport(Request $request)
    {
        $data = $this->trainersCommissionReport($request);
        return Excel::download(new TrainerCommissionExport($this->s_date, $this->e_date, $data), 'Report TrainerCommission.xlsx');
        //return Excel::download(new SalesExport($request->start_date, $request->end_date, $request->search_payment_status), 'Report Sales.xlsx');
    }

    // End Report Trainer commission



}
