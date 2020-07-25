<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Auction;
use App\ProductCategory;
use App\Tags;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class AuctionController extends Controller
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
        $this->middleware('permission:auction-list');
        $this->middleware('permission:auction-create', ['only' => ['create','store']]);
        $this->middleware('permission:auction-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:auction-delete', ['only' => ['destroy']]);
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
                2=>'headline',
                3=>'status',
                4=>'start_price',
                5=>'buy_now',
                6=>'price_buy_now',
                7=>'user_created',
                8=>'date_published',
                9=>'created_at',
            );
            $model  = New Auction();
            return $this->ActionTable($columns, $model, $request, 'auction.edit', 'auction-edit', 'auction-delete');
        }
        return view('auction.index');
    }

    public function create()
    {
        $_bank = array(
            'BCA'=>'BCA',
            'BNI'=>'BNI',
            'BRI'=>'BRI',
            'MANDIRI'=>'MANDIRI'
        );
        $beneficiary_account_issuer = $_bank;
        $tags = Tags::pluck('name','name')->all();
        $product_categories = ProductCategory::pluck('name','id')->all();
        return view('auction.create', compact('tags', 'beneficiary_account_issuer', 'product_categories'));
    }

    public function edit($id)
    {
        $_bank = array(
            'BCA'=>'BCA',
            'BNI'=>'BNI',
            'BRI'=>'BRI',
            'MANDIRI'=>'MANDIRI'
        );
        $beneficiary_account_issuer = $_bank;
        $tags = Tags::pluck('name','name')->all();
        $auction = Auction::find($id);
        $dateStarted = strtotime($auction->date_started);
        $dateEnded = strtotime($auction->date_ended);
        $auction->date_started = date('Y-m-d', $dateStarted);
        $auction->date_ended = date('Y-m-d', $dateEnded);
        $selectTags = !empty($auction->tags) ? explode(',',$auction->tags) : '';
        $selectBeneficiary = !empty($auction->beneficiary_account_issuer) ? explode(',',$auction->beneficiary_account_issuer) : '';
        return view('auction.edit', compact('auction', 'tags', 'selectTags', 'selectBeneficiary', 'beneficiary_account_issuer'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:auction,title',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required',
            'product_name'=>'required',
            'product_categories_id'=>'required',
            'product_categories_name'=>'required',
            'buy_now'=> 'required|bool',
            'price_buy_now'=> 'exclude_if:buy_now,0|required|numeric|min:1',
            'price_buy_now.min'=>'Harga wajib diisi',
            'start_price'=>'required',
            'rate_donation'=>'required',
            'beneficiary_account'=>'required',
            'beneficiary_account_issuer'=>'required',
            'beneficiary_account_name'=>'required',
            'date_started'=>'required',
            'date_ended'=>'required',
            'file' => 'required|mimes:jpeg,bmp,png|max:200'
        ]);
        $detail=$request->input('content');
        $dom = new \DomDocument();
        $dom->loadHtml($detail, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach($images as $k => $img){
            $data = $img->getAttribute('src');
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $image_name= '/storage/'.config('app.auctionImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s')).$k.'.png';
            $path = public_path() . $image_name;
            file_put_contents($path, $data);
            $img->removeAttribute('src');
            $img->setAttribute('src', $image_name);
        }

        $detail = $dom->saveHTML();

        $slug = Str::slug($request->title, '-');
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.auctionImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('auction.create');

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
                $request->request->add(['user_id'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('content', 'tags', 'price_buy_now', 'start_price', 'beneficiary_account_issuer', 'rate_donation'));
                $input['content']=$detail;
                $input['price_buy_now']=$input['price_buy_now_value'];
                $input['start_price']=$input['start_price_value'];
                $input['tags']= $request->input('tags') ? implode(',',$request->input('tags')) : '';
                $input['beneficiary_account_issuer']= $request->input('beneficiary_account_issuer') ? implode(',',$request->input('beneficiary_account_issuer')) : '';
                $input['rate_donation']= $request->input('rate_donation') ? implode(',',$request->input('rate_donation')) : '';
                if($input['status']==1){
                    $input['date_published']=date('Y-m-d H:i:s');
                }
                Auction::create($input);


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('auction.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.auctionImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server pada proses input');
            return redirect()->route('auction.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|unique:auction,title,'.$id,
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required',
            'product_name'=>'required',
            'product_categories_id'=>'required',
            'product_categories_name'=>'required',
            'buy_now'=> 'required|bool',
            'price_buy_now'=> 'exclude_if:buy_now,false|required',
            'start_price'=>'required',
            'rate_donation'=>'required',
            'beneficiary_account'=>'required',
            'beneficiary_account_issuer'=>'required',
            'beneficiary_account_name'=>'required',
            'date_started'=>'required',
            'date_ended'=>'required',
            'file' => 'required|mimes:jpeg,bmp,png|max:200'
        ]);
        $detail=$request->input('content');
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHtml($detail, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        if($images){
            foreach($images as $k => $img){
                $data = $img->getAttribute('src');
                $getData = $this->checkDataImg($data);
                if($getData != false){
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $image_name= '/storage/'.config('app.auctionImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s.u')).$k.'.png';
                    $path = public_path() . $image_name;
                    file_put_contents($path, $data);
                    $img->removeAttribute('src');
                    $img->setAttribute('src', $image_name);
                }
            }
        }
        $detail = $dom->saveHTML();

        $slug = Str::slug($request->title, '-');
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.auctionImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('auction.index');
            }
        }

       try {

            DB::beginTransaction();
            $auction = Auction::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($auction->file, config('app.auctionImagePath'));
                    }
                }
                $request->request->add(['slug'=> $slug]);
                $request->request->add(['user_id'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('content', 'tags', 'fund_target', 'beneficiary_account_issuer'));
                $input['content']=$detail;
                $input['fund_target']=$input['fund_target_value'];
                $input['tags']= $request->input('tags') ? implode(',',$request->input('tags')) : '';
                $input['beneficiary_account_issuer']= $request->input('beneficiary_account_issuer') ? implode(',',$request->input('beneficiary_account_issuer')) : '';
                if($input['status']==1){
                    $input['date_published']=date('Y-m-d H:i:s');
                }

                $auction->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('auction.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.auctionImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('auction.index');
        }
    }

    public function destroy($id)
    {
        $auction = Auction::find($id);
        try {
            DB::beginTransaction();
                $this->DeleteImage($auction->photo, config('app.auctionImagePath'));
                $auction->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageAuction(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $auction = Auction::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.auctionImagePath'));
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $auction->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL];
                $auction->update($input);
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
