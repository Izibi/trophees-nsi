<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use App\Helpers\SortableTable;
use App\Models\Country;
use App\Models\Region;
use App\Http\Requests\StoreSchoolRequest;

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
        'hidden' => 'schools.hidden'
    ];


    public function index(Request $request)
    {
        $q = DB::table('schools')
            ->select(DB::raw('schools.id, schools.name, schools.address, schools.city, schools.zip, countries.name as country_name, regions.name as region_name, schools.uai, schools.hidden'))
            ->leftJoin('regions', 'schools.region_id', '=', 'regions.id')
            ->leftJoin('countries', 'schools.country_id', '=', 'countries.id');
        SortableTable::orderBy($q, $this->sort_fields);
        $schools = $q->paginate()->appends($request->all());
        return view('schools.index', [
            'rows' => $schools
        ]);
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
