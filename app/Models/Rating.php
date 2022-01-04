<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'score_idea',
        'score_communication',
        'score_presentation',
        'score_image',
        'score_logic',
        'score_creativity',
        'score_organisation',
        'score_operationality',
        'score_ouverture',
    ];


    protected static function boot() {
        parent::boot();
        static::saving(function($rating) {
            $rating->score_total = 
                $rating->score_idea +
                $rating->score_communication +
                $rating->score_presentation +
                $rating->score_image +
                $rating->score_logic +
                $rating->score_creativity +
                $rating->score_organisation +
                $rating->score_operationality +
                $rating->score_ouverture;
        });
    }
}