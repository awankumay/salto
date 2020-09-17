<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Product;
use App\Visit;
use App\TransactionView;
use App\Transaction;
use App\ProductCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class TransactionController extends Controller
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
        $this->middleware('permission:transaction-list');
        $this->middleware('permission:transaction-create', ['only' => ['create','store']]);
        $this->middleware('permission:transaction-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transaction-delete', ['only' => ['destroy']]);
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
                1=>'userapp',
                2=>'id_visit',
                3=>'visitor_name',
                4=>'created_at',
                5=>'date_payment',
                6=>'shop_option',
                7=>'trans_status',
                8=>'tqty',
                9=>'tprice',
            );
            $model  = New TransactionView();
            return $this->ActionTable($columns, $model, $request, 'transaction.edit', 'transaction-edit', 'transaction-delete');
        }
        return view('transaction.index');
    }

    public function create()
    {
    }

    public function edit($id)
    {
        $transaction = New Transaction();
        $transactionHeader = TransactionView::where('id', $id)->first();
        $detailTransaction = $transaction->getDetails($id);
        #@dd($transactionHeader);
        return view('transaction.edit', compact('transactionHeader', 'detailTransaction'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:products,name',
            'type' => 'required',
            'price' => 'required',
            'status' => 'required',
            'id_categories' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:300'
            ]
        );

        if($request->file){
            $image = $this->UploadImage($request->file, config('app.productImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('product.create');
            }
        }

       try {

            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Product::create($input);

            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('product.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.productImagePath'));
                }
            }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('product.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:products,name,'.$id,
            'type' => 'required',
            'price' => 'required',
            'status' => 'required',
            'id_categories' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:300'
            ]
        );

        if($request->file){

            $image = $this->UploadImage($request->file, config('app.productImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('product.create');
            }
        }

       try {

            DB::beginTransaction();
            $product = Product::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($product->photo, config('app.productImagePath'));
                    }
                }
                $input = $request->all();
                $product->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('product.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.productImagePath'));
                }
            }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('product.create');
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        try {
            DB::beginTransaction();
            if($product->photo){
                $this->DeleteImage($product->photo, config('app.productImagePath'));
            }
            $product->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageProduct(Request $request)
    {
        $image = $request->post('image');
        $document = $request->post('document');
        $id    = $request->post('id');
        $deleteFile = false;
        $deleteFile2 = false;
        $product = Product::find($id);
        try {
            if(!empty($image)){
                $deleteFile = $this->DeleteImage($image, config('app.productImagePath'));
            }
            DB::beginTransaction();
                if($deleteFile == true || !empty($image)){
                    $input = ['photo'=>NULL];
                    $product->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            if($image){
                $input = ['photo'=>NULL];
                $product->update($input);
            }
            DB::rollback();
            return false;
        }
    }

    public function checkDataImg($data)
    {
        try {
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }
}
