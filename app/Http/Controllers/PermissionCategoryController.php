<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Permission;
use App\User;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class PermissionCategoryController extends Controller
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
        $this->middleware('permission:kategori-surat-izin-list');
        $this->middleware('permission:kategori-surat-izin-create', ['only' => ['create','store']]);
        $this->middleware('permission:kategori-surat-izin-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:kategori-surat-izin-delete', ['only' => ['destroy']]);
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
                1=>'nama_menu',
            );
            $model  = New Permission();
            return $this->ActionTable($columns, $model, $request, 'permission.edit', 'kategori-surat-izin-edit', 'kategori-surat-izin-delete');
        }
        return view('permission.index');
    }

    public function create()
    {
        return view('permission.create');
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        return view('permission.edit',compact('permission'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['nama_menu' => 'required|unique:menu_persetujuan,nama_menu,NULL,id,deleted_at,NULL']
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Permission::create($input);
            DB::commit();
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('permission.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('permission.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['nama_menu' => "required|unique:menu_persetujuan,nama_menu,{$id},id,deleted_at,NULL"]
                        );
        try {
            $postCategory = Permission::find($id);
            DB::beginTransaction();
            $request->request->add(['user_updated'=> Auth::user()->id]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $postCategory->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diperbarui.');
            return redirect()->route('permission.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server'. $th);
            return redirect()->route('permission.edit');
        }
    }

    public function destroy($id)
    {
        try {
            $data = Permission::find($id);
            $data->user_deleted = Auth::user()->id;
            $data->save();
            $data->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
