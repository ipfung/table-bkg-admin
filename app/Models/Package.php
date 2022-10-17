<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'service_id',
        'room_id',
        'no_of_session',
        'price',
        'discount',
        'status',
        'quantity',
        'trainer_id',
        'start_time',
        'start_date',
        'end_date',
        'recurring'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class);
    }
}
