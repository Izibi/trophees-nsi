<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreContestRequest;
use App\Models\Contest;
use App\Helpers\SortableTable;
use Illuminate\Support\Facades\DB;
use App\Classes\ActiveContest;

class ContestsController extends Controller
{

    private $sort_fields = [
        'name' => 'contests.name',
        'year' => 'contests.year',
        'status' => 'contests.status',
        'active' => 'contests.active',
    ];


    public function __construct(ActiveContest $active_contest)
    {
        $this->active_contest = $active_contest;
    }


    public function index(Request $request)
    {
        $q = DB::table('contests')->select(DB::raw('contests.*'));
        SortableTable::orderBy($q, $this->sort_fields);
        $contests = $q->paginate()->appends($request->all());
        return view('contests.index', [
            'rows' => $contests
        ]);
    }


    public function create(Request $request)
    {
        return view('contests.edit', [
            'contest' => null,
            'refer_page' => $request->get('refer_page', '/contests')
        ]);
    }


    public function store(StoreContestRequest $request)
    {
        $contest = new Contest($request->all());
        $contest->save();
        $url = $request->get('refer_page', '/contests');
        return redirect($url)->withMessage('Concours créé');
    }


    public function edit(Request $request, Contest $contest)
    {
        return view('contests.edit', [
            'submit_route' => 'contests.update',
            'contest' => $contest,
            'refer_page' => $request->get('refer_page', '/contests')
        ]);
    }


    public function update(StoreContestRequest $request, Contest $contest)
    {
        $contest->fill($request->all());
        $contest->save();
        $url = $request->get('refer_page', '/contests');
        return redirect($url)->withMessage('Concours modifié');
    }

    public function activate(Request $request, $contest_id) {
        $this->active_contest->set($contest_id);
        $url = $request->get('refer_page', '/contests');
        return redirect($url)->withMessage('Concours actif changé');
    }

    public function destroy(Request $request, Contest $contest)
    {
        if($contest->active) {
            return redirect()->back()->withError('Vous ne pouvez pas supprimer un concours acrif');
        }
        $contest->delete();
        $url = $request->get('refer_page', '/contests');
        return redirect($url)->withMessage('Concours supprimé');
    }
}
