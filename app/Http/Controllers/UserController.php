<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\User;
use App\Store;
use App\Traits\DataTrait;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;

class UserController extends Controller
{
    use DataTrait;
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
            $data = User::latest()->get();
            return $this->FetchData($data, 'user.edit', 'user-edit', 'user-delete');
        }
        //\Session::put('error','Item created successfully.');
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
        $userRole = $user->roles->pluck('name','name')->all();
        return view('user.edit', compact('user', 'role', 'userRole'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'phone' => 'required|numeric|unique:users,phone',
            'whatsapp' => 'numeric|unique:users,whatsapp',
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
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $user->assignRole($request->input('role'));
            DB::commit();

            \Session::flash('success','User berhasil ditambah.');

            return redirect()->route('user.index');
        } catch (\Throwable $th) {
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
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'phone' => 'required|numeric|unique:users,phone,'.$id,
            'whatsapp' => 'numeric|unique:users,whatsapp,'.$id,
            'role'=>'required',
            'sex'=>'required',
            'status'=>'required'
        ]);



        if($request->file){

            $image = $this->UploadImage($request->file, config('app.userImagePath'));
            if($image==false){
                \Session::flash('error','image upload failure');
                return redirect()->route('user.index');

            }
        }
        try {
            if(isset($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                }
            }
            $input = $request->all();

            if(!empty($input['password'])){
                $input['password'] = Hash::make($input['password']);
            }else{
               Arr::forget($input, array('password', 'confirm-password'));
            }
            DB::beginTransaction();
                $user = User::find($id);
                $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
                $user->assignRole($request->input('role'));
            DB::commit();

            \Session::flash('success','User updated successfully.');

            return redirect()->route('user.index');
        } catch (\Throwable $th) {
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

        $user = User::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.userImagePath'));
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $user->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL];
                $user->update($input);
            DB::rollback();
            return false;
        }
    }
}
