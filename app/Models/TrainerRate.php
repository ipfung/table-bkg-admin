<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer','rate_type','trainer_commission','company_income','trainer_charge','student_id','created_at','updated_at'
     ];

     public function users()
     {
         return $this->belongsTo(User::class, 'trainer', 'id');
     }
}
