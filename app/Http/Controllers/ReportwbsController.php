<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Materiwbs;
use App\Reportwbs;
use App\PostCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class ReportwbsController extends Controller
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
        $this->middleware('permission:pengaduan-wbs-list');
        $this->middleware('permission:pengaduan-wbs-create');
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
                1=>'username',
                2=>'materi',
                3=>'ewhat',
                4=>'ewho',
                5=>'ewhy',
                6=>'ewhen',
                7=>'ewhere',
                8=>'created_at',
                9=>'action'
            );
            $model  = New Reportwbs();
            return $this->ActionTable($columns, $model, $request, 'reportwbs.show', 'pengaduan-followup', null);
        }
        return view('report-wbs.index');
    }

    public function create()
    {
        $materi = Materiwbs::select('*')->pluck('nama_materi','id');
        return view('report-wbs.create', ['materi' => $materi]);
    }

    public function edit($id)
    {

    }

    public function show($id)
    {
        $currentUser = Auth::user();
        $data = Reportwbs::select('tb_wbs.id','tb_wbs.materi','tb_wbs.follow_up', 'tb_wbs.created_at', 'users.name as username','tb_wbs.ewhat','tb_wbs.ewho','tb_wbs.ewhy','tb_wbs.ewhen','tb_wbs.ewhere')
            ->leftJoin('users', 'users.id', '=', 'tb_wbs.id_user')
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_wbs.id_user', $currentUser->id);
                }
            })
            ->where('tb_wbs.id', $id)
            ->first();

        return view('report-wbs.show', ['data' => $data]);
    }

    public function store(Request $request)
    {
        if(Reportwbs::create($request->all())) {
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('reportwbs.index');
        }
    }

    public function update(Request $request, $id)
    {
        if(Reportwbs::where('id', $id)->update(['follow_up' => $request->pengaduan])) {
            return redirect()->route('reportwbs.show', ['reportwb' => $id]);
        }
    }

    public function destroy($id)
    {

    }
}
