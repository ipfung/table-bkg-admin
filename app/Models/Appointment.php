<?php

namespace App\Models;

use DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Appointment extends Model
{
    protected $appends = ['duration','simpleduration', 'lessontime'];

    public function getDurationAttribute()
    {
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $this->start_time);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $this->end_time);
        $interval = $start->diff($end);
        return $interval->format('%h h %i min');
    }

    public function getSimpleDurationAttribute()
    {
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $this->start_time);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $this->end_time);
        $interval = Carbon::parse($start)->diffInMinutes($end);        
        return $interval;
    }

    public function getLessonTimeAttribute()
    {
        /* $start = DateTime::createFromFormat('H:i', $this->start_time);
        $end = DateTime::createFromFormat('H:i', $this->end_time); */
        $start = Carbon::parse($this->start_time)->format('H:i');
        $end = Carbon::parse($this->end_time)->format('H:i');
       // $end = DateTime::createFromFormat('H:i', $this->end_time); 
        //$interval = $start->toTimeString() .'-'. $end->toTimeString();        
        return $start .'-' .$end;
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerBookings() {
        return $this->hasMany(CustomerBooking::class, 'appointment_id', 'id');
    }
}
