<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\User;
use App\UserConvicts;
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
                2=>'email',
                3=>'phone',
                4=>'whatsapp',
                5=>'address',
                6=>'sex',
                7=>'status',
                8=>'photo',
                9=>'created_at',
                10=>'updated_at',
                11=>'deleted_at'
            );
            $model  = New User();
            return $this->ActionTable($columns, $model, $request, 'user.edit', 'user-edit', 'user-delete');
        }
        return view('user.index');
    }

    public function create()
    {
        $role = Role::pluck('name','name')->all();
        return view('user.create', compact('role'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $role = Role::pluck('name','name')->all();
        $getNapi =  UserConvicts::where('users_id', $id)->get();
        $nm_napi_1=null;
        $nm_napi_2=null;
        if(!empty($getNapi)){
            $napi = count($getNapi);
            if($napi==1 && $napi!=0){
                $napi_1=$getNapi[0]->convicts_id;
                $nm_napi_1 = \App\Convict::where('id', $napi_1)->first();
            }else if($napi>1){
                $napi_1=$getNapi[0]->convicts_id;
                $napi_2=$getNapi[1]->convicts_id;
                $nm_napi_1 = \App\Convict::where('id', $napi_1)->first();
                $nm_napi_2 = \App\Convict::where('id', $napi_2)->first();
            }
        }
        $userRole = $user->roles->pluck('name','name')->all();
        return view('user.edit', compact('user', 'role', 'userRole', 'nm_napi_1', 'nm_napi_2'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'identity' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'phone' => 'required|numeric|unique:users,phone',
            'whatsapp' => 'numeric|unique:users,whatsapp',
            'file' => 'nullable|mimes:jpeg,bmp,png|max:100',
            'role'=>'required',
            'sex'=>'required',
            'status'=>'required'
        ]);

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
                $input = $request->all();
                $napi_1 = $input['napi_1'];
                $napi_2 = $input['napi_2'];
                Arr::forget($input, array('napi_1', 'napi_2'));
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $user->assignRole($request->input('role'));
                if(!empty($napi_1)){
                    $userHasConvicts = New UserConvicts();
                    $userHasConvicts->convicts_id=$napi_1;
                    $userHasConvicts->users_id=$user->id;
                    $userHasConvicts->save();
                }
                if(!empty($napi_2)){
                    $userHasConvicts = New UserConvicts();
                    $userHasConvicts->convicts_id=$napi_2;
                    $userHasConvicts->users_id=$user->id;
                    $userHasConvicts->save();
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
        $this->validate($request, [
            'name' => 'required',
            'identity' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'phone' => 'required|numeric|unique:users,phone,'.$id,
            'whatsapp' => 'numeric|unique:users,whatsapp,'.$id,
            'file' => 'nullable|mimes:jpeg,bmp,png|max:100',
            'role'=>'required',
            'sex'=>'required',
            'status'=>'required'
        ]);


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
            $input = $request->all();
            $napi_1 = $input['napi_1'];
            $napi_2 = $input['napi_2'];

            Arr::forget($input, array('napi_1', 'napi_2'));
            if(!empty($input['password'])){
                $input['password'] = Hash::make($input['password']);
            }else{
               Arr::forget($input, array('password', 'confirm-password'));
            }
            DB::beginTransaction();
            if(!empty($napi_1) || !empty($napi_2)){
                \App\UserConvicts::where('users_id', $id)->delete();
                if(!empty($napi_1)){
                    $userHasConvicts = New UserConvicts();
                    $userHasConvicts->convicts_id=$napi_1;
                    $userHasConvicts->users_id=$user->id;
                    $userHasConvicts->save();
                }
                if(!empty($napi_2)){
                    $userHasConvicts = New UserConvicts();
                    $userHasConvicts->convicts_id=$napi_2;
                    $userHasConvicts->users_id=$user->id;
                    $userHasConvicts->save();
                }
            }
                $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
                $user->assignRole($request->input('role'));
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
