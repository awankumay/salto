<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\User;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class ProductCategoryController extends Controller
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
        $this->middleware('permission:product-category-list');
        $this->middleware('permission:product-category-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-category-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-category-delete', ['only' => ['destroy']]);
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
                5=>'user_created',
            );
            $model  = New ProductCategory();
            return $this->ActionTable($columns, $model, $request, 'product-category.edit', 'product-category-edit', 'product-category-delete');
        }
        return view('product-category.index');
    }

    public function create()
    {
        return view('product-category.create');
    }

    public function edit($id)
    {
        $productCategory = ProductCategory::find($id);
        return view('product-category.edit',compact('productCategory'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['name' => 'required|unique:product_categories,name'],
                        ['name.required'=> 'Nama produk wajib diisi', 'name.unique'=> 'Nama produk telah digunakan'],
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['author'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                ProductCategory::create($input);
            DB::commit();
            \Session::flash('success','Produk kategori berhasil ditambah.');
            return redirect()->route('product-category.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('product-category.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['name' => 'required|unique:product_categories,name,'.$id],
                        );
        try {
            $productCategory = ProductCategory::find($id);
            DB::beginTransaction();
            $request->request->add(['user_updated'=> Auth::user()->name]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $productCategory->update($input);

            DB::commit();
            \Session::flash('success','Product kategori berhasil diperbarui.');
            return redirect()->route('product-category.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server'. $th);
            return redirect()->route('product-category.edit');
        }
    }

    public function destroy($id)
    {
        try {
            ProductCategory::find($id)->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
