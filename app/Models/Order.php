<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{

    protected $appends = [
        'due_amount', 'total_amount', 'paid_amount'
    ];

    /**
     * @return double order_total - discount.
     */
    public function getTotalAmountAttribute()
    {
        return $this->order_total - $this->discount;
    }

    /**
     * @return double order_total - paid amounts.
     */
    public function getDueAmountAttribute()
    {
        $amt = $this->order_total - $this->getPaidAmountAttribute();
        return $amt;
    }

    /**
     * @return double paid amounts.
     */
    public function getPaidAmountAttribute()
    {
        $paid = 0;
        if ($this->payments()) {
            $payments = $this->payments();
            foreach ($payments as $payment) {
                if ($payment->status == 'paid') {
                    $paid += $payment->amount;
                }
            }
        }
        return $paid;
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany('App\Models\OrderDetail' );
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment' );
    }
}
