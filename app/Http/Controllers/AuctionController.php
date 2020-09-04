<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Auction;
use App\PostCategory;
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
        $donationRate = $this->getDonationRate();
        $beneficiary_account_issuer = $this->getBank();
        $tags = Tags::pluck('name','name')->all();
        $product_categories = ProductCategory::pluck('name','id')->all();
        return view('auction.create', compact('tags', 'beneficiary_account_issuer', 'product_categories', 'donationRate'));
    }

    public function edit($id)
    {
        $donationRate = $this->getDonationRate();
        $product_categories = ProductCategory::pluck('name','id')->all();
        $beneficiary_account_issuer = $this->getBank();
        $tags = Tags::pluck('name','name')->all();
        $auction = Auction::find($id);
        $dateStarted = strtotime($auction->date_started);
        $dateEnded = strtotime($auction->date_ended);
        $auction->date_started = date('Y-m-d', $dateStarted);
        $auction->date_ended = date('Y-m-d', $dateEnded);
        $selectTags = !empty($auction->tags) ? explode(',',$auction->tags) : '';
        $selectRate = !empty($auction->rate_donation) ? explode(',',$auction->rate_donation) : '';
        $selectBeneficiary = !empty($auction->beneficiary_account_issuer) ? explode(',',$auction->beneficiary_account_issuer) : '';
        $selectProductCategories = !empty($auction->product_categories_id) ? explode(',',$auction->product_categories_id) : '';
        return view('auction.edit', compact('auction', 'tags', 'selectTags', 'selectBeneficiary', 'selectProductCategories', 'selectRate', 'beneficiary_account_issuer', 'donationRate', 'product_categories'));
    }

    public function store(Request $request)
    {
        #@dd($request);
        $this->validate($request, [
            'title' => 'required|unique:auctions,title',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required',
            'product_name'=>'required',
            'product_categories_id'=>'required',
            'buy_now'=> 'required|bool',
            'price_buy_now_value'=> 'exclude_if:buy_now,0|required|numeric|min:1',
            'price_buy_now_value.min'=>'Harga wajib diisi',
            'price_buy_now_value.required'=>'Harga wajib diisi',
            'start_price_value'=>'required',
            'multiple_bid_value'=>'required',
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
                Arr::forget($input, array('content', 'tags', 'price_buy_now', 'start_price', 'beneficiary_account_issuer', 'product_categories_id', 'rate_donation'));
                $input['content']=$detail;
                $input['price_buy_now']=$input['price_buy_now_value'];
                $input['start_price']=$input['start_price_value'];
                $input['multiple_bid']=$input['multiple_bid_value'];
                $input['tags']= $request->input('tags') ? implode(',',$request->input('tags')) : '';
                $input['beneficiary_account_issuer']= $request->input('beneficiary_account_issuer') ? implode(',',$request->input('beneficiary_account_issuer')) : '';
                $input['product_categories_id']= $request->input('product_categories_id') ? implode(',',$request->input('product_categories_id')) : '';
                $input['rate_donation']= $request->input('rate_donation') ? implode(',',$request->input('rate_donation')) : '';
                $productCategory=ProductCategory::where('id', $input['product_categories_id'])->first();
                if(!empty($productCategory)){
                    $input['product_categories_name']=$productCategory->name;
                }
                if($input['status']==1){
                    $input['date_published']=date('Y-m-d H:i:s');
                }

                Auction::create($input);


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('auction.index');
        } catch (\Throwable $th) {
            @dd($th);
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
            'title' => 'required|unique:auctions,title,'.$id,
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required',
            'product_name'=>'required',
            'product_categories_id'=>'required',
            'buy_now'=> 'required|bool',
            'price_buy_now_value'=> 'exclude_if:buy_now,0|required|numeric|min:1',
            'price_buy_now_value.min'=>'Harga wajib diisi',
            'start_price_value'=>'required',
            'multiple_bid_value'=>'required',
            'rate_donation'=>'required',
            'beneficiary_account'=>'required',
            'beneficiary_account_issuer'=>'required',
            'beneficiary_account_name'=>'required',
            'date_started'=>'required',
            'date_ended'=>'required',
            'file' => 'mimes:jpeg,bmp,png|max:200'
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
                Arr::forget($input, array('content', 'tags', 'price_buy_now', 'start_price', 'beneficiary_account_issuer', 'product_categories_id', 'rate_donation'));
                $input['content']=$detail;
                $input['price_buy_now']=$input['price_buy_now_value'];
                $input['start_price']=$input['start_price_value'];
                $input['multiple_bid']=$input['multiple_bid_value'];
                $input['tags']= $request->input('tags') ? implode(',',$request->input('tags')) : '';
                $input['beneficiary_account_issuer']= $request->input('beneficiary_account_issuer') ? implode(',',$request->input('beneficiary_account_issuer')) : '';
                $input['product_categories_id']= $request->input('product_categories_id') ? implode(',',$request->input('product_categories_id')) : '';
                $input['rate_donation']= $request->input('rate_donation') ? implode(',',$request->input('rate_donation')) : '';
                $productCategory=ProductCategory::where('id', $input['product_categories_id'])->first();
                if(!empty($productCategory)){
                    $input['product_categories_name']=$productCategory->name;
                }
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

    public function getBank(){
        return array(
            'BCA'=>'BCA',
            'BNI'=>'BNI',
            'BRI'=>'BRI',
            'MANDIRI'=>'MANDIRI'
        );
    }

    public function getDonationRate(){
        return array(
            '10'=>'10%',
            '20'=>'20%',
            '30'=>'30%',
            '40'=>'40%',
            '50'=>'50%',
            '60'=>'60%',
            '70'=>'70%',
            '80'=>'80%',
            '90'=>'90%',
            '100'=>'100%'
        );
    }
}
