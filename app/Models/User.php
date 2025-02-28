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
        'role'
    ];

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

    public function hasRole($role, $target_id = null) {
        $query = $this->roles()->where('type', $role);
        if($target_id !== null) {
            $query->where('target_id', $target_id);
        }
        return $query->exists();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function roles() {
        return $this->hasMany(Role::class);
    }

    public function schools() {
        return $this->belongsToMany(School::class);
    }

    public function prizes() {
        return $this->belongsToMany(Prize::class, 'roles', 'user_id', 'target_id')
                    ->wherePivot('type', 'prize');
    }
}