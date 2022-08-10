<?php

namespace App\Mail;

use App\Models\CustomerBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentApproved extends Mailable
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
    public function __construct(CustomerBooking $customerBooking)
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
        return $this->view('mails.appointments.approved');
    }
}
