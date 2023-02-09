<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderDetail extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
//    protected $casts = [
//        'order_description' => 'array',   // this causes order_description saved as \"start_time\": \"2023-12-31 09:00:00\"
//    ];

    public function getDescriptionAttribute() {
        // string to json.
        return json_decode($this->order_description);
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function booking()
    {
        return $this->hasOne(CustomerBooking::class, 'id', 'booking_id');
    }
//
//    public function payments()
//    {
//        return $this->hasMany('App\Models\Payments', 'order_id', 'order_id' );
//    }
}
