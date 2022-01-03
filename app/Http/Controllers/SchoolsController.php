<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use App\Helpers\SortableTable;

class SchoolsController extends Controller
{

    private $sort_fields = [
        'name' => 'schools.name',
        'address' => 'schools.address',
        'city' => 'schools.city',
        'zip' => 'schools.zip',
        'country' => 'countries.name',
        'region' => 'regions.name',
        'uai' => 'schools.uai'
    ];


    public function index(Request $request)
    {
        $q = DB::table('schools')
            ->select(DB::raw('schools.id, schools.name, schools.address, schools.city, schools.zip, countries.name as country_name, regions.name as region_name, schools.uai'))
            ->leftJoin('regions', 'schools.region_id', '=', 'regions.id')
            ->leftJoin('countries', 'schools.country_id', '=', 'countries.id');
        SortableTable::orderBy($q, $this->sort_fields);
        $schools = $q->paginate()->appends($request->all());
        return view('schools.index', [
            'rows' => $schools
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
