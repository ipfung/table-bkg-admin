<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Timeslot extends Model
{
    // 1 = Monday, 7 = Sunday.
    protected $fillable = [
        'day_idx', 'location_id', 'from_time', 'to_time'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
