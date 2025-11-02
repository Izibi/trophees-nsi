<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'project_id',
        'prize_id',
        'region_id',
        'user_id',
        'comment'
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getRegionPrizeTitle($prize, $region_id) {
        if($region_id == 0) {
            return "Prix national " . $prize->name;
        } else {
            $region = Region::find($region_id);
            if(!$region) {
                return "Prix " . $prize->name;
            }
            return "Prix " . $prize->name . " pour le territoire " . $region->name;
        }
    }

    public function getPrizeTitle()
    {
        return self::getRegionPrizeTitle($this->prize, $this->region_id);
    }
}