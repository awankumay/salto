<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Report;
use App\PostCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    use ActionTable;
    use ImageTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pengaduan-list');
        $this->middleware('permission:pengaduan-create');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'pengaduan',
                2=>'date_follow_up',
                3=>'action'
            );
            $model  = New Report();
            return $this->ActionTable($columns, $model, $request, 'report.edit', 'pengaduan-edit', null);
        }
        return view('send-report.index');
    }

    public function create()
    {
        return view('send-report.create');
    }

    public function edit($id)
    {

    }

    public function store(Request $request)
    {
        $this->validate($request, ['pengaduan' => 'required']);
        if(Report::create($request->all())) {
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('report.index');
        }
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
