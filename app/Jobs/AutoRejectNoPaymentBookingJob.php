<?php

namespace App\Jobs;

use App\Mail\AppointmentRejected;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoRejectNoPaymentBookingJob implements ShouldQueue
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
        $hour = config("app.jws.auto_reject_unpaid_booking");
        Log::info('AutoRejectNoPaymentBookingJob: Running the handle function SQL. hour=' . $hour);
        if ($hour > 0) {
            $now = date('Y-m-d h:i:s');
//            Log::info('AutoRejectNoPaymentBookingJob: Running the handle function SQL. now=' . $now);
            // select > update > inform client.
            $unpaidAppointments = Appointment::orderBy('id', 'asc')
                ->where('status', '<>', 'rejected')
                // 1 day after appointment created_at.
                ->whereRaw('DATE_ADD(created_at, INTERVAL ? HOUR) < ?', [$hour, $now])
                ->whereRaw('id in (select appointment_id from customer_bookings, order_details, payments where customer_bookings.id=order_details.booking_id and order_details.order_type=? and order_details.order_id=payments.order_id and payments.status=?)', ['booking', 'pending'])
                ->get();
            foreach ($unpaidAppointments as $appointment) {
                Log::info('AutoRejectNoPaymentBookingJob: Running the handle function SQL. id=' . $appointment->id);
                // update to 'rejected'.
                $appointment->status = 'rejected';
                $appointment->internal_remark = 'Appointment rejected due to no payment has been made in ' . $hour . ' hour';
                $appointment->save();
                // inform client by email.
                foreach ($appointment->customerBookings as $customerBooking) {
                    Log::info('AutoRejectNoPaymentBookingJob: Running the handle function SQL. email=' . $customerBooking->customer->email);
                    Mail::to($customerBooking->customer->email)
                        ->bcc(config('mail.from.admin'))
                        ->send(new AppointmentRejected($customerBooking));
                }
            }
        }
    }
}
