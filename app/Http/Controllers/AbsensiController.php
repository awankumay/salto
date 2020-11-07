<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Absensi;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class AbsensiController extends Controller
{
    use ActionTable;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:absensi-list');
        $this->middleware('permission:absensi-create', ['only' => ['create','store']]);
        $this->middleware('permission:absensi-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:absensi-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $model  = New Absensi();
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'stb',
                2=>'nama',
                3=>'clock_in',
                4=>'file_clock_in',
                5=>'clock_out',
                6=>'file_clock_out',
            );
            return $this->ActionTable($columns, $model, $request, 'grade.edit', 'grade-edit', 'grade-delete');
        }
        $data = $model::whereRaw('DATE(created_at) = ?', date('Y-m-d'))->where('id_user', Auth::user()->id)->first();
        $clockIn = null;
        $clockOut = null;
        if(!empty($data)){
            $clockIn = $data->clock_in;
            $clockOut = $data->clock_out;
        }
        return view('absensi.index', compact('clockIn', 'clockOut'));
    }

    public function create()
    {
     
    }

    public function edit($id)
    {
       
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {

    }

}
