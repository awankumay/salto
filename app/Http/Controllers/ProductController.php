<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductCategory;
use App\Traits\DataTrait;
use App\Traits\ImageTrait;
use File;
use DataTables;
use DB;
use Auth;

class ProductController extends Controller
{
    use DataTrait;
    use ImageTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:product-list');
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with(['ProductCategory'])->orderBy('created_at', 'DESC')->get();
            return $this->FetchData($data, 'product.edit', 'product-edit', 'product-delete');
        }
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productCategory = ProductCategory::pluck('name','id')->all();
        return view('product.create', compact('productCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_name' => 'required',
            'product_sale' => 'required',
            'product_category' => 'required',
            'product_cost' => 'required',
            'file'=> 'file|image|mimes:jpeg,png,jpg|max:300000'

        ]);

        if($request->file){
            $image = $this->UploadImage($request->file, config('app.productImagePath'));
            if($image==false){
                \Session::put('error','image upload failure');
                return redirect()->route('product.create');

            }
        }

        try {
                DB::beginTransaction();
                    $getCode = DB::table('products')->latest()->sharedLock()->first();
                    if($getCode==null){
                        $request->request->add(['product_code'=> 'PR-0001']);
                    }else{
                        $latestCode = $getCode->product_code;
                        $seq        = substr($latestCode, 3);
                        $generate   = $seq+1;
                        $request->request->add(['product_code'=> 'PR-'.sprintf("%04s", $generate)]);
                    }
                    if(isset($image)!=false){
                        $request->request->add(['product_image'=> $image]);
                    }
                    $input=$request->all();

                    $input['product_category']=$input['product_category']['0'];
                    Arr::forget($input, 'file');

                    $product = Product::create($input);
                DB::commit();

                \Session::put('success','product created successfully.');
                return redirect()->route('product.index');

            } catch (\Throwable $th) {
                DB::rollBack();
                if(isset($image)){
                    $this->DeleteImage($image, config('app.productImagePath'));
                }
                \Session::put('error', 'server failure');
                return redirect()->route('product.create');
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $productCategory = ProductCategory::pluck('name','id')->all();
        $productCategoryPick = $product->productCategory->pluck('id','name')->all();
        return view('product.edit',compact('product', 'productCategory', 'productCategoryPick'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_name' => 'required',
            'product_sale' => 'required',
            'product_category' => 'required',
            'product_cost' => 'required',
            'file'=> 'file|image|mimes:jpeg,png,jpg|max:300000'

        ]);
        try {
            $product = product::find($id);
            if($request->file){
                $image = $this->UploadImage($request->file, config('app.productImagePath'));
                if($image==false){
                    \Session::put('error','image upload failure');
                    return redirect()->route('product.create');

                }
            }

            DB::beginTransaction();
                if(isset($image)!=false){
                    $request->request->add(['product_image'=> $image]);
                    $input=$request->all();
                    $input['product_category']=$input['product_category']['0'];
                    Arr::forget($input, 'file');
                    $product->update($input);
                }else{
                    $input=$request->all();
                    $input['product_category']=$input['product_category']['0'];
                    Arr::forget($input, 'file');
                    $product->update($input);
                }
            DB::commit();

            \Session::put('success','product updated successfully.');
            return redirect()->route('product.index');

        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                $this->DeleteImage($image, config('app.productImagePath'));
            }
            \Session::put('error', 'server failure');
            return redirect()->route('product.edit', compact('product'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product    = Product::find($id);
        $deleteFile = $this->DeleteImage($product->product_image, config('app.productImagePath'));

        if($deleteFile == true){
            Product::find($id)->delete();
            return true;
        }else{
            Product::find($id)->delete();
            return false;
        }

    }

    public function deleteExistImageProduct(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');
        $product    = Product::find($id);
        $deleteFile = $this->DeleteImage($image, config('app.productImagePath'));
        if($deleteFile == true){
            $input = ['product_image'=>NULL];
            $product->update($input);
            return true;
        }else{
            $input = ['product_image'=>NULL];
            $product->update($input);
            return false;
        }
    }

}
