<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Materiwbs;
use App\User;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class MateriWBSController extends Controller
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
        $this->middleware('permission:materi-wbs-list');
        $this->middleware('permission:materi-wbs-create', ['only' => ['create','store']]);
        $this->middleware('permission:materi-wbs-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:materi-wbs-delete', ['only' => ['destroy']]);
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
                1=>'nama_materi',
            );
            $model  = New Materiwbs();
            $data = $this->ActionTable($columns, $model, $request, 'materi-wbs.edit', 'materi-wbs-edit', 'materi-wbs-delete');
            return response()->json($data->original->data);
    
        }
        
        return view('materi-wbs.index');

    }

    // public function index(Request $request)
    // {
    //     $data = DB::table('materi_wbs')->get();
    //     dd($data);
    // }

    public function create()
    {
        return view('materi-wbs.create');
    }

    public function edit($id)
    {
        $materiWBS = Materiwbs::find($id);
        return view('materi-wbs.edit',compact('materiWBS'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
        ['nama_materi' => 'required|unique:materi_wbs,nama_materi']);
        try {
        DB::beginTransaction();
        $request->request->add(['user_created'=> Auth::user()->id]);
        // $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
        $input=$request->all();
        // dd($input);
        Materiwbs::create($input);
        DB::commit();
        \Session::flash('success','Data berhasil ditambah.');
        return redirect()->route('materi-wbs.index');

        }catch (\Throwable $th) {
        DB::rollBack();
        \Session::flash('error','Terjadi kesalahan server');
        return redirect()->route('materi-wbs.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
        ['nama_materi' => 'required|unique:materi_wbs,nama_materi']);
        try {
            $materiWBS = Materiwbs::find($id);
            DB::beginTransaction();
            $request->request->add(['user_updated'=> Auth::user()->id]);
            // $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $materiWBS->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diperbarui.');
            return redirect()->route('materi-wbs.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server'. $th);
            return redirect()->route('materi-wbs.edit');
        }
    }

    public function destroy($id)
    {
        try {
            $data = Materiwbs::find($id);
            $data->save();
            $data->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
