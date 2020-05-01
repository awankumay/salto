<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Traits\DataTrait;
use DataTables;
use DB;
use Auth;
class ProductCategoryController extends Controller
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
            'name' => 'required'
        ]);

        $input=$request->all();
        $supplier = ProductCategory::create($input);
        \Session::put('success','product category created successfully.');

        return redirect()->route('product-category.index');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $input = $request->all();
        $productCategory = ProductCategory::find($id);
        $productCategory->update($input);

        \Session::put('success','product category updated successfully.');

        return redirect()->route('product-category.index');
    }

    public function destroy($id)
    {
        if(ProductCategory::find($id)->delete()){
            return true;
        }
            return false;
    }

}
