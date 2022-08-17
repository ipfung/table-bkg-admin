<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NotifyMessage extends Model
{

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
