<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TrainerTimeslot extends Model
{
    // 1 = Monday, 7 = Sunday.
    protected $fillable = [
        'trainer_id', 'day_idx', 'location_id', 'from_time', 'to_time'
    ];

    protected $casts = [
        'from_time'  => 'date:H:i',
        'to_time' => 'date:H:i',
    ];

}
