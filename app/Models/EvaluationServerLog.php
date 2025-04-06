<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationServerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'login_date',
        'logout_date'
    ];
}