<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\KeluargaAsuh;
use App\User;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class KeluargaAsuhController extends Controller
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
        $this->middleware('permission:data-keluarga-asuh-list');
        $this->middleware('permission:data-keluarga-asuh-create', ['only' => ['create','store']]);
        $this->middleware('permission:data-keluarga-asuh-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:data-keluarga-asuh-delete', ['only' => ['destroy']]);
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
                1=>'name',
                2=>'description',
                3=>'created_at',
                4=>'updated_at',
            );
            $model  = New KeluargaAsuh();
            return $this->ActionTableWithDetail($columns, $model, $request, 'keluarga-asuh.edit', 'keluarga-asuh.show', 'data-keluarga-asuh-edit', 'data-keluarga-asuh-delete', 'data-keluarga-asuh-list');
        }
        return view('keluarga-asuh.index');
    }

    public function create()
    {
        return view('keluarga-asuh.create');
    }

    public function edit($id)
    {
        $keluargaAsuh = KeluargaAsuh::find($id);
        return view('keluarga-asuh.edit',compact('keluargaAsuh'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['name' => 'required|unique:keluarga_asuh,name,NULL,id,deleted_at,NULL']
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                KeluargaAsuh::create($input);
            DB::commit();
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('keluarga-asuh.index');

        }catch (\Throwable $th) {
            @dd($th);
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('keluarga-asuh.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['name' => "required|unique:keluarga_asuh,name,{$id},id,deleted_at,NULL"]
                        );
        try {
            $keluargaAsuh = KeluargaAsuh::find($id);
            DB::beginTransaction();
            $request->request->add(['user_updated'=> Auth::user()->id]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $keluargaAsuh->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diperbarui.');
            return redirect()->route('keluarga-asuh.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('keluarga-asuh.edit');
        }
    }

    public function show($id)
    {
        $keluargaAsuh = KeluargaAsuh::find($id);
        $pembina      = User::role('Pembina')->pluck('name', 'id')->all();
        $waliasuh     = User::role('Wali Asuh')->pluck('name', 'id')->all();
        return view('keluarga-asuh.show',compact('keluargaAsuh', 'pembina', 'waliasuh'));
    }

    public function destroy($id)
    {
        try {
            $data = KeluargaAsuh::find($id);
            $data->user_deleted = Auth::user()->id;
            $data->save();
            $data->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
