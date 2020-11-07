<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\JurnalTaruna;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class JurnalController extends Controller
{
    use ActionTableWithDetail;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:jurnal-harian-list');
        $this->middleware('permission:jurnal-harian-create', ['only' => ['create','store']]);
        $this->middleware('permission:jurnal-harian-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:jurnal-harian-delete', ['only' => ['destroy']]);
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
                1=>'nama',
                2=>'tanggal',
                3=>'status',
            );
            $model  = New JurnalTaruna();
            return $this->ActionTableWithDetail($columns, $model, $request, 'jurnal.edit', 'jurnal.show', 'jurnal-harian-edit', 'jurnal-harian-delete', 'jurnal-harian-list');
        }
        return view('jurnal.index');
    }

    public function create()
    {
        return view('jurnal.create');
    }

    public function edit($id)
    {
        $jurnal = JurnalTaruna::where('id_user', Auth::user()->id)->find($id);
        return view('jurnal.edit',compact('jurnal'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                            ['id_user' => "required",
                             'tanggal' => "required",
                             'start' => "required",
                             'end' => "required",
                             'kegiatan' => "required",
                             'status' => "required"
                            ]
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                JurnalTaruna::create($input);
            DB::commit();
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('jurnal.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('jurnal.create');
        }
    }

    public function show(Request $request, $id)
    {
        $model  = New JurnalTaruna();
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'nama',
                2=>'tanggal',
                3=>'kegiatan',
                4=>'start',
                5=>'end',
                6=>'created_at',
                7=>'updated_at',
            );
            return $this->ActionTable($columns, $model, $request, 'jurnal.edit', 'jurnal-harian-edit', 'jurnal-harian-delete');
        }
        $jurnal = $model::find($id);
        return view('jurnal.show', compact('jurnal'));
    }

    public function jurnaldetail(Request $request)
    {
        $model  = New JurnalTaruna();
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'nama',
                2=>'tanggal',
                3=>'kegiatan',
                4=>'start',
                5=>'end',
                6=>'created_at',
                7=>'updated_at',
            );
            return $this->ActionTable($columns, $model, $request, 'jurnal.edit', 'jurnal-harian-edit', 'jurnal-harian-delete');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['id_user' => "required",
                             'tanggal' => "required",
                             'start' => "required",
                             'end' => "required",
                             'kegiatan' => "required",
                             'status' => "required"
                            ]
                        );
        try {
            $jurnal = JurnalTaruna::find($id);
            DB::beginTransaction();
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $input=$request->all();
            $jurnal->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diperbarui.');
            return redirect()->route('jurnal.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server'. $th);
            return redirect()->route('jurnal.edit');
        }
    }

    public function destroy($id)
    {
        try {
            $jurnal = JurnalTaruna::find($id);
            $jurnal->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
