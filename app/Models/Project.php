<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'grade_id',
        'team_girls',
        'team_boys',
        'team_not_provided',
        'description',
        'video_url',
//        'presentation_file',
    ];


    protected static function boot() {
        parent::boot();
        static::deleting(function($project) {
            if(!is_null($project->presentation_file)) {
                Storage::disk('presentation_files')->delete($project->presentation_file);
            }
        });

        static::updating(function($project) {
            $old_file = $project->getOriginal('presentation_file');
            if(!is_null($old_file) && $project->presentation_file != $old_file) {
                Storage::disk('presentation_files')->delete($old_file);
            }
        });
    }


    public function school()
    {
        return $this->belongsTo(School::class);
    }


    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }    

}
