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
        'award_mixed',
        'award_citizenship',
        'award_engineering',
        'award_heart',
        'award_originality',
        //'cb_award_mixed',
        'cb_award_citizenship',
        'cb_award_engineering',
        'cb_award_heart',
        'cb_award_originality',
        'notes',
        'published'
    ];


    protected static function boot() {
        parent::boot();
        static::saving(function($rating) {
            if($rating->published) {
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
            } else {
                $rating->score_total = 0;
            }
        });

        static::saved(function($rating) {
            if($rating->published) {
                $rating->refreshProjectRatings($rating->project_id);
            }
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
                sum(award_mixed) as award_mixed,
                sum(award_citizenship) as award_citizenship,
                sum(award_engineering) as award_engineering,
                sum(award_heart) as award_heart,
                sum(award_originality) as award_originality,
                count(*) as ratings_amount
            '))
            ->groupBy('project_id')
            ->where('project_id', '=', $project_id)
            ->where('published', '=', 1)
            ->first();
        if(!$res) {
            return;
        }
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

        $project->award_mixed = $res->award_mixed;
        $project->award_citizenship = $res->award_citizenship;
        $project->award_engineering = $res->award_engineering;
        $project->award_heart = $res->award_heart;
        $project->award_originality = $res->award_originality;
        $project->save();
    }

/*
    public function setCbAwardMixedAttribute($v) {
        $this->attributes['award_mixed'] = !empty($v);
    }
*/
    public function setCbAwardCitizenshipAttribute($v) {
        $this->attributes['award_citizenship'] = !empty($v);
    }

    public function setCbAwardEngineeringAttribute($v) {
        $this->attributes['award_engineering'] = !empty($v);
    }

    public function setCbAwardHeartAttribute($v) {
        $this->attributes['award_heart'] = !empty($v);
    }

    public function setCbAwardOriginalityAttribute($v) {
        $this->attributes['award_originality'] = !empty($v);
    }
}