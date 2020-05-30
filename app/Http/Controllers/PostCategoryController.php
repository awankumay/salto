<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\PostCategory;
use App\User;
use App\Traits\DataTrait;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class PostCategoryController extends Controller
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
        $this->middleware('permission:post-category-list');
        $this->middleware('permission:post-category-create', ['only' => ['create','store']]);
        $this->middleware('permission:post-category-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:post-category-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PostCategory::latest()->get();
            return $this->FetchData($data, 'post-category.edit', 'post-category-edit', 'post-category-delete');
        }
        return view('post-category.index');
    }

    public function create()
    {
        return view('post-category.create');
    }

    public function edit($id)
    {
        $postCategory = PostCategory::find($id);
        return view('post-category.edit',compact('postCategory'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['name' => 'required|unique:post_categories,name'],
                        ['name.required'=> 'Nama kategori wajib diisi', 'name.unique'=> 'Nama kategori telah digunakan'],
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['author'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                PostCategory::create($input);
            DB::commit();
            \Session::put('success','Kategori konten berhasil ditambah.');
            return redirect()->route('post-category.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::put('error','Terjadi kesalahan server');
            return redirect()->route('post-category.create');
        }
    }

    public function update(Request $request, $id)
    {
        if($request->post('name')==null || $request->post('name')==''){
            $this->validate($request,
                             ['name' => 'required'],
                             ['name.required' => 'Nama kategori wajib diisi']
                            );
        }else if($request->post('old_name')!=$request->post('name')){
            $this->validate($request,
                             ['name' => 'required|unique:post_categories,name'],
                             ['name.required' => 'Nama kategori wajib diisi',
                              'name.unique' => 'Nama kategori telah digunakan'
                             ]
                            );
        }else{
            $this->validate($request,
                             ['name' => 'required'],
                             ['name.required' => 'Nama kategori wajib diisi']
                            );
        }
        try {
            $postCategory = PostCategory::find($id);
            DB::beginTransaction();
            $request->request->add(['user_updated'=> Auth::user()->name]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $postCategory->update($input);

            DB::commit();
            \Session::put('success','Kategori konten berhasil diperbarui.');
            return redirect()->route('post-category.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::put('error','Terjadi kesalahan server'. $th);
            return redirect()->route('post-category.edit');
        }
    }

    public function destroy($id)
    {
        try {
            PostCategory::find($id)->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
