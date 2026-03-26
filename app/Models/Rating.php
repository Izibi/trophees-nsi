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
        'phase',
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
        'published',
        'ignored',
        'cannot_evaluate_technical'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the current phase based on active contest status
     * 
     * @return string 'territorial' or 'national'
     */
    public static function getCurrentPhase()
    {
        $contest = \App\Models\Contest::where('active', true)->first();
        if (!$contest) {
            return 'territorial'; // Default to territorial if no active contest
        }
        
        $contestStatus = $contest->status;
        $territorialPhases = ['preparing', 'open', 'instruction', 'grading-territorial', 'deliberating-territorial'];
        
        return in_array($contestStatus, $territorialPhases) ? 'territorial' : 'national';
    }

    /**
     * Get query builder filtered by current phase
     * Use this instead of Rating::where() to ensure phase filtering
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getActive()
    {
        return self::where('phase', '=', self::getCurrentPhase());
    }


    protected static function boot() {
        parent::boot();
        static::saving(function($rating) {
            $rating->phase = self::getCurrentPhase();
            
            if($rating->published) {
                // If user cannot evaluate technical, ensure score_operationality is null
                if ($rating->cannot_evaluate_technical) {
                    $rating->score_operationality = null;
                }
                
                // Calculate score_total as weighted sum of non-null scores
                $oper = $rating->score_operationality;
                $comm = $rating->score_communication;
                
                if ($oper !== null && $comm !== null) {
                    // Both present: sum them
                    $rating->score_total = $oper + $comm;
                } elseif ($oper !== null) {
                    // Only operationality: multiply by 2
                    $rating->score_total = $oper * 2;
                } elseif ($comm !== null) {
                    // Only communication: multiply by 2
                    $rating->score_total = $comm * 2;
                } else {
                    // Neither present
                    $rating->score_total = 0;
                }
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
        if (!$project) {
            return;
        }
        
        // Get current phase
        $currentPhase = self::getCurrentPhase();
        
        // Get ratings for current phase only
        $res = DB::table('ratings')
            ->select(DB::raw('
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
                count(*) as ratings_amount,
                sum(CASE WHEN score_operationality IS NOT NULL THEN 1 ELSE 0 END) as oper_count,
                sum(CASE WHEN score_communication IS NOT NULL THEN 1 ELSE 0 END) as comm_count
            '))
            ->groupBy('project_id')
            ->where('project_id', '=', $project_id)
            ->where('phase', '=', $currentPhase)
            ->where('published', '=', 1)
            ->where('ignored', '=', 0)
            ->first();
        
        if(!$res || $res->ratings_amount == 0) {
            // No ratings for this phase, clear scores
            $project->score_total = null;
            $project->score_idea = null;
            $project->score_communication = null;
            $project->score_presentation = null;
            $project->score_image = null;
            $project->score_logic = null;
            $project->score_creativity = null;
            $project->score_organisation = null;
            $project->score_operationality = null;
            $project->score_ouverture = null;
            $project->ratings_amount = 0;
            $project->award_mixed = 0;
            $project->award_citizenship = 0;
            $project->award_engineering = 0;
            $project->award_heart = 0;
            $project->award_originality = 0;
            $project->save();
            return;
        }
        
        // Calculate score_total from components with weighted average logic
        $avgOper = $res->score_operationality;
        $avgComm = $res->score_communication;
        $operCount = $res->oper_count;
        $commCount = $res->comm_count;
        
        if ($operCount > 0 && $commCount > 0) {
            // Both components have ratings: sum averages
            $project->score_total = $avgOper + $avgComm;
        } elseif ($operCount > 0) {
            // Only operationality has ratings: multiply by 2
            $project->score_total = $avgOper * 2;
        } elseif ($commCount > 0) {
            // Only communication has ratings: multiply by 2
            $project->score_total = $avgComm * 2;
        } else {
            // Neither has ratings (shouldn't happen given ratings_amount > 0, but handle it)
            $project->score_total = 0;
        }
        
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

    /**
     * Refresh aggregated ratings for all projects in a contest
     * Called when contest phase changes to recompute ratings based on new phase context
     * 
     * @param int $contest_id The contest ID
     * @return void
     */
    public static function refreshAllProjectsForContest($contest_id) {
        $projects = Project::where('contest_id', $contest_id)->get();
        
        foreach($projects as $project) {
            // Use a Rating instance to call the private method
            $rating = new self();
            $rating->refreshProjectRatings($project->id);
        }
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