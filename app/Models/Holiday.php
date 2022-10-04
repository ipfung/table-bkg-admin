<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Holiday extends Model
{
    protected $fillable = [
        'name', 'location_id', 'start_date', 'end_date'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
