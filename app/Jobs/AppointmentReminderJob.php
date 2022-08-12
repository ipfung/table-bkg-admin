<?php

namespace App\Jobs;

use App\Mail\AppointmentReminder;
use App\Models\BookingReminder;
use App\Models\CustomerBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        // testing.
//        $booking = CustomerBooking::find(15);
//        $email = "ipfung@gmail.com";
//        Mail::to($email)
////            ->bcc(config('mail.from.address'))
//            ->send(new AppointmentReminder($booking));

        Log::info('AppointmentReminderJob: Running the handle function SQL.');
        $bookings = DB::table('customer_bookings')
            ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
            ->join('rooms', 'appointments.room_id', '=', 'rooms.id')
            ->select('customer_bookings.*',
                DB::raw('CAST(appointments.start_time AS DATE) as appointment_date'),
                DB::raw('(select payments.status from order_details, payments where order_details.booking_id=customer_bookings.id and order_details.order_id=payments.order_id) as payment_status'),
                'appointments.start_time', 'appointments.end_time', 'appointments.status', 'appointments.room_id', 'rooms.name')
//testing            ->where('appointments.start_time', '2022-08-16 01:00:00')
                // a day before appointment start_time.
            ->whereRaw('DATE_SUB(appointments.start_time, INTERVAL 1 DAY)=?', [date('Y-d-m h:i:s')])
            ->get();
        Log::info('AppointmentReminderJob: Running the handle function');
        foreach ($bookings as $booking) {
            $email = "ipfung@gmail.com";
            Mail::to($email)
                ->bcc(config('mail.from.admin'))
                ->send(new AppointmentReminder($booking));
            $reminder = new BookingReminder;
            $reminder->booking_id = $booking->id;
            $reminder->email = $email;
            $reminder->save();
        }
    }
}
