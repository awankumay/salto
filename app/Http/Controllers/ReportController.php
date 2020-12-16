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
            return $this->ActionTable($columns, $model, $request, 'report.show', 'pengaduan-followup', null);
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

    public function show($id)
    {
        $currentUser = Auth::user();
        $data = Report::select('tb_pengaduan.id','tb_pengaduan.pengaduan','tb_pengaduan.follow_up', 'tb_pengaduan.created_at', 'users.name as username')
            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_pengaduan.id_user', $currentUser->id);
                }
            })
            ->where('tb_pengaduan.id', $id)
            ->first();

        return view('send-report.show', ['data' => $data]);
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
        if(Report::where('id', $id)->update(['follow_up' => $request->pengaduan])) {
            return redirect()->route('report.show', ['report' => $id]);
        }
    }

    public function destroy($id)
    {

    }
}
