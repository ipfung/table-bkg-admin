<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;


class Appointment extends Model
{

    public function getDurationAttribute()
    {
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $this->start_time);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $this->end_time);
        $interval = $start->diff($end);
        return $interval->format('%h h %i min');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customerBookings() {
        return $this->hasMany(CustomerBooking::class, 'appointment_id', 'id');
    }
}
