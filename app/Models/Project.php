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
        'video',
        'tested_by_teacher',
        'cb_tested_by_teacher'
    ];

    public $upload_attributes = [
        'presentation_file',
        'image_file',
        'zip_file'
    ];


    protected static function boot() {
        parent::boot();
        static::deleting(function($project) {
            foreach($project->upload_attributes as $attr) {
                if(!is_null($project->$attr)) {
                    Storage::disk('uploads')->delete($project->$attr);
                }
            }
        });

        static::updating(function($project) {
            foreach($project->upload_attributes as $attr) {
                $old_file = $project->getOriginal($attr);
                if(!is_null($old_file) && $project->$attr !== $old_file) {
                    Storage::disk('uploads')->delete($old_file);
                }
            }
        });
    }


    public function uploadFiles($request) {
        foreach($this->upload_attributes as $attr) {
            if($request->hasFile($attr)) {
                $file = $request->file($attr);
                $this->$attr = $file->hashName();
                $file->storeAs('/', $this->$attr, 'uploads');
            }
        }
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


    public function setCbTestedByTeacherAttribute($v) {
        $this->attributes['tested_by_teacher'] = !empty($v);
    }

}
