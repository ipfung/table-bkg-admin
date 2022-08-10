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
        return $this->hasOne(Appointment::class, 'id', 'appointment_id');
    }

    /**
     * Get the customer of the customer booking.
     */
    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
}
