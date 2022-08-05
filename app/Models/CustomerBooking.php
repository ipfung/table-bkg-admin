<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CustomerBooking extends Model
{
    /**
     * Get the appointment that owns the customer booking.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
