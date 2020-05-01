<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\User;
use App\Traits\DataTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;

class UserController extends Controller
{
    use DataTrait;
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
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        return view('user.edit', compact('user', 'roles', 'userRole'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'role'=>'required'
        ]);


        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('role'));

        \Session::put('success','User created successfully.');

        return redirect()->route('user.index');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'role' => 'required'
        ]);


        $input = $request->all();

        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
           Arr::forget($input, array('password', 'confirm-password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('role'));

        \Session::put('success','User updated successfully.');

        return redirect()->route('user.index');
    }

    public function destroy($id)
    {
        if(User::find($id)->delete()){
            return true;
        }
            return false;
    }

    public function showTable($data)
    {
        if(Auth::user()->hasPermissionTo('user-edit') && Auth::user()->hasPermissionTo('user-delete')){
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href='.route('user.edit', $row).' class="action-table text-success text-sm"><i class="fas fa-edit"></i></a> <a href="javascript:void(0)" onclick="deleteRecord('.$row->id.',this)" class="action-table text-danger text-sm"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else if (Auth::user()->hasPermissionTo('user-edit')) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href='.route('user.edit', $row).' class="action-table text-success text-sm"><i class="fas fa-edit"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else if(Auth::user()->hasPermissionTo('user-delete')){
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" onclick="deleteRecord('.$row->id.',this)" class="action-table text-danger text-sm"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else{
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                return $btn;
            })
            ->make(true);
        }
    }
}
