<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Room extends Model
{
    protected $fillable = [
        'name', 'location_id', 'translations', 'status'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
