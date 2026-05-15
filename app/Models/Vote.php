<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'normalized_email',
        'project_id',
        'confirmation_string',
        'confirmed'
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
