<?php

namespace App\Mail;

use App\Models\CustomerBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * The order instance.
     *
     * @var \App\Models\CustomerBooking
     */
    public $customerBooking;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customerBooking)   // frank: don't give param a CustomerBooking type, otherwise it will throw "__construct() must be an instance of App\Models\CustomerBooking, instance of stdClass given" error
    {
        $this->customerBooking = $customerBooking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.appointments.reminder');
    }
}
