<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'status',
        'message'
    ];

    protected static function boot() {
        parent::boot();
        
        static::updated(function($contest) {
            // Check if status was changed
            if ($contest->isDirty('status')) {
                // Recompute all project ratings for this contest when phase changes
                \App\Models\Rating::refreshAllProjectsForContest($contest->id);
            }
        });
    }
}
