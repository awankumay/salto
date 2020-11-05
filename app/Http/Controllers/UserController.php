<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\User;
use App\Provinces;
use App\Grade;
use App\Regencies;
use App\OrangTua;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;

class UserController extends Controller
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
        $this->middleware('permission:user-list');
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
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
                2=>'stb',
                3=>'role',
                4=>'photo',
                5=>'status',
                6=>'sex',
                7=>'phone',
                8=>'whatsapp',
                9=>'email'
            );
            $model  = New User();
            return $this->ActionTable($columns, $model, $request, 'user.edit', 'user-edit', 'user-delete');
        }
        return view('user.index');
    }

    public function create()
    {
        $provinces = Provinces::pluck('name','id')->all();
        $grade = Grade::pluck('grade','id')->all();
        $orangtua = User::GetOrangTua()->pluck('name', 'id')->all();
        $role = Role::pluck('name','name')->all();
        return view('user.create', compact('role', 'provinces', 'grade', 'orangtua'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $role = Role::pluck('name','name')->all();
        $grade = Grade::pluck('grade', 'id')->all();
        $orangtua = User::GetOrangTua($user->id)->pluck('name', 'id')->all();
        $provinces = Provinces::pluck('name','id')->all();
        $regencies = Regencies::where('province_id', $user->province_id)->pluck('name','id')->all();
        $getOrangTua = OrangTua::where('taruna_id', $user->id)->first();
        $selectOrangTua = !empty($getOrangTua->orangtua_id) ? $getOrangTua->orangtua_id : '';
        $selectGrade = !empty($user->grade) ? $user->grade : '';
        $selectProvince = !empty($user->province_id) ? $user->province_id : '';
        $selectRegencie = !empty($user->regencie_id) ? $user->regencie_id : '';
        $userRole = $user->roles->pluck('name','name')->all();
        return view('user.edit', compact('user', 'role', 'userRole', 'selectProvince', 'selectRegencie', 'provinces', 'regencies', 'orangtua', 'grade', 'selectOrangTua', 'selectGrade'));
    }

    public function store(Request $request)
    {
        $orangtua = null;
        if($request->input('role')=='Taruna'){
            $this->validate($request, [
                'name' => 'required',
                'identity' => 'nullable|numeric|unique:users,identity,NULL,id,deleted_at,NULL',
                'stb' => 'nullable|unique:users,stb,NULL,id,deleted_at,NULL',
                'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|same:confirm-password',
                'phone' => 'required|numeric|unique:users,phone,NULL,id,deleted_at,NULL',
                'whatsapp' => 'numeric|unique:users,whatsapp,NULL,id,deleted_at,NULL',
                'file' => 'nullable|mimes:jpeg,bmp,png|max:500',
                'role'=>'required',
                'sex'=>'required',
                'status'=>'required',
                'orangtua'=>'required',
                'grade'=>'required'
            ]);
            $orangtua = $request->input('orangtua');
        }else{
            $this->validate($request, [
                'name' => 'required',
                'identity' => 'nullable|numeric|unique:users,identity,NULL,id,deleted_at,NULL',
                'stb' => 'nullable|unique:users,stb,NULL,id,deleted_at,NULL',
                'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|same:confirm-password',
                'phone' => 'required|numeric|unique:users,phone,NULL,id,deleted_at,NULL',
                'whatsapp' => 'numeric|unique:users,whatsapp,NULL,id,deleted_at,NULL',
                'file' => 'nullable|mimes:jpeg,bmp,png|max:500',
                'role'=>'required',
                'sex'=>'required',
                'status'=>'required'
            ]);
        }
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.userImagePath'));
            if($image==false){
                \Session::flash('error','image upload failure');
                return redirect()->route('user.create');

            }
        }

        try {

            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['user_created'=> Auth::user()->id]);
                $input = $request->all();
                Arr::forget($input, array('orangtua'));
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $user->assignRole($request->input('role'));
                if($orangtua!=null){
                    $dataOrangTua = [];
                    $dataOrangTua['orangtua_id']=$orangtua;
                    $dataOrangTua['taruna_id']=$user->id;
                    $dataOrangTua['user_created']=Auth::user()->id;
                    OrangTua::create($dataOrangTua);
                }

            DB::commit();

            \Session::flash('success','User berhasil ditambah.');

            return redirect()->route('user.index');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.userImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('user.create');
        }

    }

    public function update(Request $request, $id)
    {
        $orangtua = null;
        if($request->input('role')=='Taruna'){
            $this->validate($request, [
                'name' => 'required',
                'identity' => "nullable|numeric|unique:users,identity,{$id},id,deleted_at,NULL",
                'stb' => "nullable|unique:users,stb,{$id},id,deleted_at,NULL",
                'email' => "required|email|unique:users,email,{$id},id,deleted_at,NULL",
                'password' => 'same:confirm-password',
                'phone' => "required|numeric|unique:users,phone,{$id},id,deleted_at,NULL",
                'whatsapp' => "numeric|unique:users,whatsapp,{$id},id,deleted_at,NULL",
                'file' => 'nullable|mimes:jpeg,bmp,png|max:500',
                'role'=>'required',
                'sex'=>'required',
                'status'=>'required',
                'grade'=>'required',
                'orangtua'=>'required'
            ]);
        }else{
            $this->validate($request, [
                'name' => 'required',
                'identity' => "nullable|numeric|unique:users,identity,{$id},id,deleted_at,NULL",
                'stb' => "nullable|unique:users,stb,{$id},id,deleted_at,NULL",
                'email' => "required|email|unique:users,email,{$id},id,deleted_at,NULL",
                'password' => 'same:confirm-password',
                'phone' => "required|numeric|unique:users,phone,{$id},id,deleted_at,NULL",
                'whatsapp' => "numeric|unique:users,whatsapp,{$id},id,deleted_at,NULL",
                'file' => 'nullable|mimes:jpeg,bmp,png|max:500',
                'role'=>'required',
                'sex'=>'required',
                'status'=>'required'
            ]);
        }


        $user = User::find($id);
        if($request->file){
            $image = $this->UploadImage($request->file, config('app.userImagePath'));
            if($image==false){
                \Session::flash('error','image upload failure');
                return redirect()->route('user.index');
            }
            $this->DeleteImage($user->photo, config('app.userImagePath'));
        }
        try {
            if(isset($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                }
            }
            $request->request->add(['user_updated'=> Auth::user()->id]);
            $input = $request->all();

            if(!empty($input['password'])){
                $input['password'] = Hash::make($input['password']);
            }else{
               Arr::forget($input, array('password', 'confirm-password', 'orangtua'));
            }
            DB::beginTransaction();
                $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
                $user->assignRole($request->input('role'));
                if($orangtua!=null){
                    $getOrangTua = OrangTua::where('taruna_id', $id)->first();
                    $getOrangTua->orangtua_id=$orangtua;
                    $getOrangTua->user_updated=Auth::user()->id;
                    $getOrangTua->save();
                }
            DB::commit();

            \Session::flash('success','User updated successfully.');

            return redirect()->route('user.index');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.userImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('user.index');
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        try {
            DB::beginTransaction();
                $this->DeleteImage($user->photo, config('app.userImagePath'));
                $user->user_deleted = Auth::user()->id;
                $user->save();
                $user->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageUser(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $user = User::where('id', $id)->where('photo', $image)->first();

        try {
            $deleteFile = $this->DeleteImage($user->photo, config('app.userImagePath'));
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $user->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }
    }
}
