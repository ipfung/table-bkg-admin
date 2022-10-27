<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class TrainerWorkdateTimeslot extends Model
{
    protected $fillable = [
        'location_id', 'trainer_id', 'work_date', 'from_time', 'to_time'
    ];

    protected $casts = [
        'from_time'  => 'date:H:i',
        'to_time' => 'date:H:i',
    ];

    /**
     * @return int make it compatible with TrainerTimeslot by day_idx.
     */
    public function getDayIdxAttribute() {
        if ($this->work_date) {
            $d = new Carbon($this->work_date);
            return $d->dayOfWeekIso;   // dayOfWeekIso = 1-7
        }
        return -1;
    }
}
