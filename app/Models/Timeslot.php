<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Timeslot extends Model
{
    // 1-6 = Monday-Saturday(same as Carbon), 7 = Sunday.
    public const MONDAY = Carbon::MONDAY;
    public const TUESDAY = Carbon::TUESDAY;
    public const WEDNESDAY = Carbon::WEDNESDAY;
    public const THURSDAY = Carbon::THURSDAY;
    public const FRIDAY = Carbon::FRIDAY;
    public const SATURDAY = Carbon::SATURDAY;
    public const SUNDAY = 7;
    // the name of week, for Carbon::is()
    public const WEEKS = [
        '', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
    ];

    protected $fillable = [
        'day_idx', 'location_id', 'from_time', 'to_time'
    ];

    protected $casts = [
        'from_time'  => 'date:H:i',
        'to_time' => 'date:H:i',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
