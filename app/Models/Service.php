<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Service extends Model
{
    protected $appends = [
        'sessions'
    ];

    public function getSessionsAttribute()
    {
        $duration = $this->min_duration;
        $counter = $this->min_duration / $this->duration;
        $max_count = $this->max_duration / $this->duration;
        $sessions = [];
        for ($i=$counter; $i<=$max_count; $i++) {
            if ($duration > 59) {
                $hours = floor($duration / 60);
                $minutes = ($duration % 60);
                if (30 == $minutes) {
                    $description = sprintf('%2d.5 h', $hours);
                    $hours = $hours + 0.5;
                } else if (0 == $minutes) {
                    $description = sprintf('%2d h', $hours);
                } else {
                    $description = sprintf('%2d h %2d m', $hours, $minutes);
                }
            } else {
                $minutes = $duration;
                if (30 == $minutes) {
                    $description = '0.5 h';
                    $hours = 0.5;
                } else {
                    $description = sprintf('%2d m', $duration);
                }
            }
            $sessions[] = ['code' => $i, 'name' => $description, 'duration' => $duration, 'hour' => $hours, 'minute' => $minutes];
            $duration += $this->duration;
        }
        return $sessions;
    }

}
