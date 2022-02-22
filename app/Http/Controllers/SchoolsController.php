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
        return view('schools.index', [
            'rows' => $schools,
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get()->pluck('name', 'id')->toArray()
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
        }
        return $q;
    }


    public function edit(Request $request, School $school)
    {
        return view('schools.edit', [
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
            'refer_page' => $request->get('refer_page', '/projects'),
            'school' => $school
        ]);
    }


    public function update(StoreSchoolRequest $request, School $school)
    {
        $school->fill($request->all());
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


    public function destroy(Request $request, School $school)
    {
        $school->delete();
        $url = $request->get('refer_page', '/schools');
        return redirect($url)->withMessage('Établissement supprimé');
    }
}
