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
        return $interval->format('%h hours %i minutes');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
