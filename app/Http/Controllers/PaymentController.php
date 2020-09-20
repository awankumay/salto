<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Product;
use App\Payment;
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

class PaymentController extends Controller
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
    }

    public function create()
    {
    }

    public function edit($id)
    {
    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,bmp,png,jpg|max:300',
            'date_payment'=>'required'
            ]
        );
        if($request->file){
            $image = $this->UploadImage($request->file, config('app.documentImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('transaction.edit', ['transaction'=>$id]);
            }
        }
        $transaction = Transaction::where('id_trans', $id)->first();
        $transactionHeader = TransactionView::where('id', $id)->first();
        $detailTransaction = New Transaction();
        $detailTransaction = $detailTransaction->getDetails($id);
       try {
            DB::beginTransaction();
            $payment=Payment::where('id_trans', $id)->first();
            if(!empty($payment)){
                $payment->photo=$image;
                $payment->save();
                $request->request->add(['status'=> 3]);
            }else{
                $payment=New Payment();
                $payment->photo=$image;
                $payment->id_trans=$id;
                $payment->save();
                $request->request->add(['status'=> 3]);
            }
            $input = $request->all();
            Transaction::where('id_trans', $id)
            ->update(['status' => $input['status'], 'date_payment'=>$input['date_payment']]);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('transaction.edit', ['transaction'=>$id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('transaction.edit', ['transaction'=>$id]);
        }
    }

    public function destroy($id)
    {

    }

    public function deleteExistImagePayment(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');
        $deleteFile = false;
        $payment = Payment::where('id_trans', $id)->first();
        try {
            if(!empty($image)){
                $deleteFile = $this->DeleteImage($image, config('app.documentImagePath'));
            }
            DB::beginTransaction();
                if($deleteFile == true || !empty($image)){
                    $input = ['photo'=>NULL];
                    $payment->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            if($image){
                $input = ['photo'=>NULL];
                $payment->update($input);
            }
            DB::rollback();
            return false;
        }
    }
}
