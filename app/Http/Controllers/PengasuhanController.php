<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Pengasuhan;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class PengasuhanController extends Controller
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
        $this->middleware('permission:pengasuhan-daring-list');
        $this->middleware('permission:pengasuhan-daring-create', ['only' => ['create','store']]);
        $this->middleware('permission:pengasuhan-daring-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:pengasuhan-daring-delete', ['only' => ['destroy']]);
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
                9=>'name',
                1=>'keluarga_asuh',
                2=>'media',
                3=>'start_time',
                4=>'end_time',
                5=>'id_media',
                6=>'password',
                7=>'status',
                8=>'created_at'
            );
            $model  = New Pengasuhan();
            return $this->ActionTableWithDetail($columns, $model, $request, 'pengasuhan.edit', 'pengasuhan.show', 'pengasuhan-daring-edit', 'pengasuhan-daring-delete', 'pengasuhan-daring-list');
        }
        return view('pengasuhan.index');
    }

    public function create()
    {
        return view('grade.create');
    }

    public function edit($id)
    {
        $grade = Grade::find($id);
        return view('grade.edit',compact('grade'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['grade' => 'required|unique:grade_table,grade,NULL,id,deleted_at,NULL']
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Grade::create($input);
            DB::commit();
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('grade.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('grade.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['grade' => "required|unique:grade_table,grade,{$id},id,deleted_at,NULL"]
                        );
        try {
            $grade = Grade::find($id);
            DB::beginTransaction();
            $request->request->add(['user_updated'=> Auth::user()->id]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $grade->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diperbarui.');
            return redirect()->route('grade.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server'. $th);
            return redirect()->route('grade.edit');
        }
    }

    public function destroy($id)
    {
        try {
            $grade = Grade::find($id);
            $grade->user_deleted = Auth::user()->id;
            $grade->save();
            $grade->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
