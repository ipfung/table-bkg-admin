<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NotificationTemplate extends Model
{
    protected $fillable = [
        'name',	'type',	'entity', 'send_to', 'subject', 'content', 'whatsapp_tpl', 'translations'
    ];
}
