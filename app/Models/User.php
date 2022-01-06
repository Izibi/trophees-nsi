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
        'first_name',
        'last_name',
        'region_id',
        'validated',
        'role',        
    ];

    public function getScreenNameAttribute()
    {
        if($this->first_name || $this->last_name) {
            return ($this->first_name ? $this->first_name.' ' : '').$this->last_name;
        } else if($this->email) {
            return $this->email;
        } else if($this->secondary_email) {
            return $this->secondary_email;
        }
        return 'User #'.$this->id;
    }    

    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function schools() {
        return $this->belongsToMany(School::class);
    }

}