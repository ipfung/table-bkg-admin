<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserTeammate extends Model
{
    protected $fillable = [
        'user_id', 'teammate_id', 'created_by'
    ];

}
