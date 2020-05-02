<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Traits\DataTrait;
use App\Traits\ImageTrait;
use DataTables;
use DB;
use Auth;
class ProductCategoryController extends Controller
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
            $data = ProductCategory::latest()->get();
            return $this->FetchData($data, 'product-category.edit', 'product-category-edit', 'product-category-delete');
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
        $this->validate($request, [
            'name' => 'required',
            'file'=> 'file|image|mimes:jpeg,png,jpg|max:300000'
        ]);
        try {
            if($request->file){
                $image = $this->UploadImage($request->file, config('app.productCategoryImagePath'));
                if($image==false){
                    \Session::put('error','image upload failure');
                    return redirect()->route('product-category.create');
                }
            }
            DB::beginTransaction();
            if(isset($image)){
                $request->request->add(['product_category_image'=> $image]);
                $input=$request->all();
                Arr::forget($input, 'file');
            }
            ProductCategory::create($input);
            DB::commit();
            \Session::put('success','product category created successfully.');
            return redirect()->route('product-category.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                $this->DeleteImage($image, config('app.productCategoryImagePath'));
            }
            \Session::put('error','server failure');
            return redirect()->route('product-category.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'file'=> 'file|image|mimes:jpeg,png,jpg|max:300000'
        ]);
        try {
            $productCategory = ProductCategory::find($id);
            if($request->file){
                $image = $this->UploadImage($request->file, config('app.productCategoryImagePath'));
                if($image==false){
                    \Session::put('error','image upload failure');
                    return redirect()->route('product-category.edit');
                }
            }
            DB::beginTransaction();
                if(isset($image)!=false){
                    $request->request->add(['product_category_image'=> $image]);
                    $input=$request->all();
                    Arr::forget($input, 'file');
                    $productCategory->update($input);
                }else{
                    $input=$request->all();
                    $productCategory->update($input);
                }
            DB::commit();
            \Session::put('success','product category updated successfully.');
            return redirect()->route('product-category.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                $this->DeleteImage($image, config('app.productCategoryImagePath'));
            }
            \Session::put('error','server failure');
            return redirect()->route('product-category.edit');
        }
    }

    public function destroy($id)
    {
        $productCategory    = ProductCategory::find($id);
        $deleteFile         = $this->DeleteImage($productCategory->product_category_image, config('app.productCategoryImagePath'));
        if($deleteFile == true){
            ProductCategory::find($id)->delete();
            return true;
        }else{
            ProductCategory::find($id)->delete();
            return false;
        }
    }

    public function deleteExistImageProductCategory(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');
        $productCategory    = ProductCategory::find($id);
        $deleteFile = $this->DeleteImage($image, config('app.productCategoryImagePath'));
        if($deleteFile == true){
            $input = ['product_category_image'=>NULL];
            $productCategory->update($input);
            return true;
        }else{
            $input = ['product_category_image'=>NULL];
            $productCategory->update($input);
            return false;
        }
    }

}
