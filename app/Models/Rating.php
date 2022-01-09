<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

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

        static::saved(function($rating) {
            $rating->refreshProjectRatings($rating->project_id);
        });
    }





    private function refreshProjectRatings($project_id) {
        $project = Project::find($project_id);
        $res = DB::table('ratings')
            ->select(DB::raw('
                avg(score_total) as score_total,
                avg(score_idea) as score_idea,
                avg(score_communication) as score_communication,
                avg(score_presentation) as score_presentation,
                avg(score_image) as score_image,
                avg(score_logic) as score_logic,
                avg(score_creativity) as score_creativity,
                avg(score_organisation) as score_organisation,
                avg(score_operationality) as score_operationality,
                avg(score_ouverture) as score_ouverture,
                count(*) as ratings_amount
            '))
            ->groupBy('project_id')
            ->where('project_id', '=', $project_id)
            ->first();

        $project->score_total = $res->score_total;
        $project->score_idea = $res->score_idea;
        $project->score_communication = $res->score_communication;
        $project->score_presentation = $res->score_presentation;
        $project->score_image = $res->score_image;
        $project->score_logic = $res->score_logic;
        $project->score_creativity = $res->score_creativity;
        $project->score_organisation = $res->score_organisation;
        $project->score_operationality = $res->score_operationality;
        $project->score_ouverture = $res->score_ouverture;
        $project->ratings_amount = $res->ratings_amount;
        $project->save();
    }
}