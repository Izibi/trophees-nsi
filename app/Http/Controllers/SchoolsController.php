<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use App\Helpers\SortableTable;
use App\Models\Country;
use App\Models\Region;
use App\Http\Requests\StoreSchoolRequest;
use App\Classes\ActiveContest;
use App\Models\Academy;

class SchoolsController extends Controller
{

    private $sort_fields = [
        'name' => 'schools.name',
        'address' => 'schools.address',
        'city' => 'schools.city',
        'zip' => 'schools.zip',
        'country' => 'countries.name',
        'region' => 'regions.name',
        'uai' => 'schools.uai',
        'hidden' => 'schools.hidden',
        'verified' => 'schools.verified',
        'projects_amount' => 'projects_amount'
    ];


    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }

    public function index(Request $request)
    {
        $q = $this->getSchoolsQuery($request);
        SortableTable::orderBy($q, $this->sort_fields);
        $schools = $q->paginate()->appends($request->all());
        $nb_verify = School::where('hidden', 0)->where('verified', 0)->count();
        return view('schools.index', [
            'rows' => $schools,
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get()->pluck('name', 'id')->toArray(),
            'filter' => $request->get('filter', false) === 1,
            'nb_verify' => $nb_verify
        ]);
    }


    private function getSchoolsQuery($request) {
        $q = DB::table('schools')
            ->select(DB::raw('
                schools.id,
                schools.name,
                schools.address,
                schools.city,
                schools.zip,
                countries.name as country_name,
                regions.name as region_name,
                schools.uai,
                schools.hidden,
                schools.verified,
                (select count(*) from projects where projects.school_id=schools.id and projects.contest_id='.$this->contest->id.') as projects_amount
            '))
            ->leftJoin('regions', 'schools.region_id', '=', 'regions.id')
            ->leftJoin('countries', 'schools.country_id', '=', 'countries.id');

        if($request->has('filter')) {
            $filter_name = $request->get('filter_name');
            if(strlen($filter_name) > 0) {
                $q->where('schools.name', 'LIKE', '%'.$filter_name.'%');
            }
            $filter_address = $request->get('filter_address');
            if(strlen($filter_address) > 0) {
                $q->where('schools.address', 'LIKE', '%'.$filter_address.'%');
            }
            $filter_city = $request->get('filter_city');
            if(strlen($filter_city) > 0) {
                $q->where('schools.city', 'LIKE', '%'.$filter_city.'%');
            }
            $filter_zip = $request->get('filter_zip');
            if(strlen($filter_zip) > 0) {
                $q->where('schools.zip', 'LIKE', '%'.$filter_zip.'%');
            }
            $filter_region_id = $request->get('filter_region_id');
            if(strlen($filter_region_id) > 0) {
                $q->where('schools.region_id', '=', $filter_region_id);
            }
            $filter_uai = $request->get('filter_uai');
            if(strlen($filter_uai) > 0) {
                $q->where('schools.uai', 'LIKE', '%'.$filter_uai.'%');
            }
            $filter_hidden = $request->get('filter_hidden');
            if(strlen($filter_hidden) > 0) {
                $q->where('schools.hidden', '=', $filter_hidden);
            }
            $filter_verified = $request->get('filter_verified');
            if(strlen($filter_verified) > 0) {
                $q->where('schools.verified', '=', $filter_verified);
            }
        }
        return $q;
    }


    public function edit(Request $request, School $school)
    {
        $otherSchools = School::where('id', '!=', $school->id)
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->id . ' - ' . $item->name];
            })
            ->toArray();

        return view('schools.edit', [
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
            'academies' => Academy::orderBy('name')->get(),
            'refer_page' => $request->get('refer_page', '/projects'),
            'school' => $school,
            'other_schools' => $otherSchools
        ]);
    }


    public function update(StoreSchoolRequest $request, School $school)
    {
        $school->fill($request->all());
        $school->verified = 1;
        $school->save();
        $url = $request->get('refer_page', '/schools');
        return redirect($url)->withMessage('Établissement enregistré');
    }


    public function hide(Request $request, School $school)
    {
        $school->hidden = 1;
        $school->save();
        $url = $request->get('refer_page', '/school');
        return redirect($url)->withMessage('Etablissement caché');
    }


    public function merge(Request $request, School $school)
    {
        $request->validate([
            'merge_school_id' => 'required|exists:schools,id'
        ]);

        $mergeSchoolId = $request->get('merge_school_id');
        
        if ($mergeSchoolId == $school->id) {
            return redirect()->back()->withErrors(['merge_school_id' => 'Vous ne pouvez pas fusionner un établissement avec lui-même.']);
        }

        $targetSchool = School::findOrFail($mergeSchoolId);

        // Update all projects to point to the target school
        DB::table('projects')
            ->where('school_id', $school->id)
            ->update(['school_id' => $targetSchool->id]);

        // Update all user-school relationships
        // First, get all users linked to the school being merged
        $userIds = DB::table('school_user')
            ->where('school_id', $school->id)
            ->pluck('user_id')
            ->toArray();

        // Delete the old relationships
        DB::table('school_user')
            ->where('school_id', $school->id)
            ->delete();

        // Insert new relationships, avoiding duplicates
        foreach ($userIds as $userId) {
            DB::table('school_user')->insertOrIgnore([
                'user_id' => $userId,
                'school_id' => $targetSchool->id
            ]);
        }

        // Delete the merged school
        $school->delete();

        $url = $request->get('refer_page', '/schools');
        return redirect($url)->withMessage('Établissement fusionné avec succès');
    }


    public function destroy(Request $request, School $school)
    {
        $school->delete();
        $url = $request->get('refer_page', '/schools');
        return redirect($url)->withMessage('Établissement supprimé');
    }
}
