<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Content;
use App\PostCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;

class ContentController extends Controller
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
        $this->middleware('permission:post-list');
        $this->middleware('permission:post-create', ['only' => ['create','store']]);
        $this->middleware('permission:post-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:post-delete', ['only' => ['destroy']]);
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
                1=>'title',
                2=>'excerpt',
                3=>'content',
                4=>'headline',
                5=>'status',
                6=>'user_created',
                7=>'created_at',
                8=>'updated_at'
            );
            $model  = New Content();
            return $this->ActionTable($columns, $model, $request, 'content.edit', 'post-edit', 'post-delete');
        }
        return view('content.index');
    }

    public function create()
    {
        $postCategory = PostCategory::pluck('name','id')->all();
        return view('content.create', compact('postCategory'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $role = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        return view('content.edit', compact('user', 'role', 'userRole'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:posts,title',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'post_categories_id' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required'
        ]);

        $slug = Str::slug($request->title, '-');
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('content.create');

            }
        }

       try {

            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['slug'=> $slug]);
                $request->request->add(['author'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();

                $post = Content::create($input);


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('content.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.postImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('content.create');
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
        $content = Content::find($id);
        try {
            DB::beginTransaction();
                $this->DeleteImage($content->photo, config('app.postImagePath'));
                $content->delete();
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

        $user = Content::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.postImagePath'));
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
