<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Leave extends Model
{
    public static $APPLIED = 1;    // by customer or staff
    public static $APPROVED = 2;   // by staff
    public static $RESCHEDULE = 3;
    public static $REJCTED = 4;    // by staff, no reschedule can be done.

}
