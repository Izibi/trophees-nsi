<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'secondary_email',
        'name',
        'login',
        'country_id',
        'region_id',
        'validated',
        'role',
        'cb_coordinator'
    ];

    public function setCbCoordinatorAttribute($v) {
        $this->attributes['coordinator'] = !empty($v);
    }

    public function getScreenNameAttribute()
    {
        if(strlen($this->name) > 0) {
            return $this->name;
        } else if(strlen($this->email) > 0) {
            return $this->email;
        } else if(strlen($this->secondary_email) > 0) {
            return $this->secondary_email;
        }
        return 'User #'.$this->id;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function schools() {
        return $this->belongsToMany(School::class);
    }

}