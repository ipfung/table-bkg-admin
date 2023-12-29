<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

class TrainerCommissionExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $s_date;
    protected $e_date; 
    protected $payment_status;
    protected $trainer_commissions;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }

    function __construct($start_date, $end_date, $data) {
        //ddd($this->e_date);
        $this->s_date = $start_date;
        $this->e_date = $end_date;
        $this->trainer_commissions = $data;
        //ddd($data);
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
        return view('exports.trainer-commission', [
            'trainer_commissions' => $this->trainer_commissions,
            'report_s_date' => $this->s_date,
            'report_e_date' => $this->e_date,
        ]);
    }
}
