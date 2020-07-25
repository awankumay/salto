<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Campaign;
use App\Tags;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class CampaignController extends Controller
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
        $this->middleware('permission:campaign-list');
        $this->middleware('permission:campaign-create', ['only' => ['create','store']]);
        $this->middleware('permission:campaign-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:campaign-delete', ['only' => ['destroy']]);
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
            $model  = New Campaign();
            return $this->ActionTable($columns, $model, $request, 'campaign.edit', 'campaign-edit', 'campaign-delete');
        }
        return view('campaign.index');
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
        return view('campaign.create', compact('tags', 'beneficiary_account_issuer'));
    }

    public function edit($id)
    {
        //$content = Campaign::with('PostCategory')->where('id', $id)->get();
        //@dd($content);
        $_bank = array(
            'BCA'=>'BCA',
            'BNI'=>'BNI',
            'BRI'=>'BRI',
            'MANDIRI'=>'MANDIRI'
        );
        $beneficiary_account_issuer = $_bank;
        $tags = Tags::pluck('name','name')->all();
        $campaign = Campaign::find($id);
        $dateStarted = strtotime($campaign->date_started);
        $dateEnded = strtotime($campaign->date_ended);
        $campaign->date_started = date('Y-m-d', $dateStarted);
        $campaign->date_ended = date('Y-m-d', $dateEnded);
        $selectTags = !empty($campaign->tags) ? explode(',',$campaign->tags) : '';
        $selectBeneficiary = !empty($campaign->beneficiary_account_issuer) ? explode(',',$campaign->beneficiary_account_issuer) : '';
        return view('campaign.edit', compact('campaign', 'tags', 'selectTags', 'selectBeneficiary', 'beneficiary_account_issuer'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:posts,title',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required',
            'set_fund_target'=> 'required',
            'beneficiary_account'=>'required',
            'beneficiary_account_issuer'=>'required',
            'beneficiary_account_name'=>'required',
            'date_started'=>'required',
            'date_ended'=>'required',
            'file' => 'required|mimes:jpeg,bmp,png|max:100'
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
            $image_name= '/storage/'.config('app.campaignImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s')).$k.'.png';
            $path = public_path() . $image_name;
            file_put_contents($path, $data);
            $img->removeAttribute('src');
            $img->setAttribute('src', $image_name);
        }

        $detail = $dom->saveHTML();

        $slug = Str::slug($request->title, '-');
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.campaignImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('campaign.create');

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
                Arr::forget($input, array('content', 'tags', 'fund_target', 'beneficiary_account_issuer'));
                $input['content']=$detail;
                $input['fund_target']=$input['fund_target_value'];
                $input['tags']= $request->input('tags') ? implode(',',$request->input('tags')) : '';
                $input['beneficiary_account_issuer']= $request->input('beneficiary_account_issuer') ? implode(',',$request->input('beneficiary_account_issuer')) : '';
                if($input['status']==1){
                    $input['date_published']=date('Y-m-d H:i:s');
                }
                Campaign::create($input);


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('campaign.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.campaignImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server pada proses input');
            return redirect()->route('campaign.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|unique:posts,title,'.$id,
            'meta_title' => 'required',
            'meta_description' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'headline' => 'required',
            'status' => 'required',
            'set_fund_target'=> 'required',
            'beneficiary_account'=>'required',
            'beneficiary_account_issuer'=>'required',
            'beneficiary_account_name'=>'required',
            'date_started'=>'required',
            'date_ended'=>'required',
            'file' => 'mimes:jpeg,bmp,png|max:100'
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
                    $image_name= '/storage/'.config('app.campaignImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s.u')).$k.'.png';
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

            $image = $this->UploadImage($request->file, config('app.campaignImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('campaign.index');
            }
        }

       try {

            DB::beginTransaction();
            $campaign = Campaign::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($campaign->file, config('app.campaignImagePath'));
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

                $campaign->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('campaign.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.campaignImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('campaign.index');
        }
    }

    public function destroy($id)
    {
        $campaign = Campaign::find($id);
        try {
            DB::beginTransaction();
                $this->DeleteImage($campaign->photo, config('app.campaignImagePath'));
                $campaign->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageCampaign(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $campaign = Campaign::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.campaignImagePath'));
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $campaign->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL];
                $campaign->update($input);
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
