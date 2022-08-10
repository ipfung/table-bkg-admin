<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Room extends Model
{

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
