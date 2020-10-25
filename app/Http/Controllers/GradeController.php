<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Grade;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class GradeController extends Controller
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
        $this->middleware('permission:grade-list');
        $this->middleware('permission:grade-create', ['only' => ['create','store']]);
        $this->middleware('permission:grade-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:grade-delete', ['only' => ['destroy']]);
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
                1=>'grade',
                2=>'created_at',
                3=>'updated_at',
                4=>'user_created',
            );
            $model  = New Grade();
            return $this->ActionTable($columns, $model, $request, 'grade.edit', 'grade-edit', 'grade-delete');
        }
        return view('grade.index');
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
