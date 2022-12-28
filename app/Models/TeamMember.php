<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'first_name',
        'last_name',
        'gender'
    ];


    protected static function boot() {
        parent::boot();
        static::deleting(function($project) {
            if(!is_null($project->parental_permissions_file)) {
                Storage::disk('uploads')->delete($project->parental_permissions_file);
            }
        });

        static::updating(function($project) {
            $old_file = $project->getOriginal('parental_permissions_file');
            if(!is_null($old_file) && $project->parental_permissions_file !== $old_file) {
                Storage::disk('uploads')->delete($old_file);
            }
        });
    }


    public function uploadFile($file) {
        if($file) {
            $this->parental_permissions_file = $file->hashName();
            $file->storeAs('/', $this->parental_permissions_file, 'uploads');
        }
    }
}
