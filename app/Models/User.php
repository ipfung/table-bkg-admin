<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use TCG\Voyager\Models\Role;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'rating'
    ];

    public function getRatingAttribute()
    {
        // FIXME get rating.
        return rand(1, 5);
    }

    /**
     * override Voyager' settings which will cause us toJson() error.
     * @param $value
     * @return void
     */
    public function setSettingsAttribute($value) {
        $this->attributes['settings'] = $value;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function teammates()
    {
        return $this->belongsToMany(User::class, 'user_teammates', 'user_id', 'teammate_id');
    }
}
