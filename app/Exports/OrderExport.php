<?php

namespace App\Exports;

use App\Models\Order;
//use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

use Maatwebsite\Excel\Concerns\FromCollection;

class OrderExport implements FromView, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    /* public function collection()
    {
        return Order::all();
    } */

    protected $s_date;
    protected $e_date; 
    protected $payment_status;

    function __construct() {
        //ddd($this->e_date);
    }

    public function registerEvents(): array
    {

        //ddd('registerevents');
        return [
            
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                //$event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setpaperSize(9);
                // ddd('registerevents');
            },
        ];
    }

    public function view(): View
    {
        /* $orders = Order::with('customer')->OrderBy('id','DESC');
        if ($this->s_date != '' ){
            if ($this->e_date != '' ){
                $from = date($this->s_date);
                $to = date($this->e_date);
                
                $orders->whereBetween('order_date', [$from, $to]);
            }
        }
        
                //if ($request->has('search_payment_status')) {
            if ($this->payment_status != '' ){
                $orders->where('payment_status', $this->payment_status);
            }
        //} */
        $orders =Order::OrderBy('id');
       
        return view('exports.orders', [
            'orders' => $orders->get(),
            /* 'report_s_date' => $this->s_date,
            'report_e_date' => $this->e_date, */
        ]);
    }

   
    
}
