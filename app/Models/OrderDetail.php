<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderDetail extends Model
{

    public function getDescriptionAttribute() {
        // string to json.
        return json_decode($this->order_description);
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
//
//    public function payments()
//    {
//        return $this->hasMany('App\Models\Payments', 'order_id', 'order_id' );
//    }
}
