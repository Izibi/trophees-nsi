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
        'video_url'
    ];


    protected static function boot() {
        parent::boot();
        static::deleting(function($project) {
            if(!is_null($project->presentation_file)) {
                Storage::disk('uploads')->delete($project->presentation_file);
            }
            if(!is_null($project->image_file)) {
                Storage::disk('uploads')->delete($project->image_file);
            }            
        });

        static::updating(function($project) {
            $old_file = $project->getOriginal('presentation_file');
            if(!is_null($old_file) && $project->presentation_file != $old_file) {
                Storage::disk('uploads')->delete($old_file);
            }
            $old_image = $project->getOriginal('image_file');
            if(!is_null($old_image) && $project->image_file != $old_image) {
                Storage::disk('uploads')->delete($old_image);
            }            
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
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
