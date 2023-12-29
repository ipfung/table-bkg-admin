<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerRate extends Model
{
    // rate_type: "1" = 1 to 1, "2" = group, "3" = 1 to 1 monthly.
    public const ONE_TO_ONE_TRAINING = 1;
    public const GROUP_TRAINING = 2;
    public const ONE_TO_ONE_MONTHLY = 3;
    use HasFactory;

    protected $fillable = [
        'trainer','rate_type','trainer_commission','company_income','trainer_charge','student_id','created_at','updated_at'
     ];

     public function users()
     {
         return $this->belongsTo(User::class, 'trainer', 'id');
     }
}
