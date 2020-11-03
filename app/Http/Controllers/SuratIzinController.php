<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\SuratIzin;
use App\Permission;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class SuratIzinController extends Controller
{
    use ActionTableWithDetail;
    use ImageTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:surat-izin-list');
        $this->middleware('permission:surat-izin-create', ['only' => ['create','store']]);
        $this->middleware('permission:surat-izin-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:surat-izin-delete', ['only' => ['destroy']]);
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
                2=>'nama_menu',
                3=>'status',
                4=>'created_at',
            );
            $model  = New SuratIzin();
            return $this->ActionTableWithDetail($columns, $model, $request, 'surat-izin.edit', 'surat-izin.show', 'surat-izin-edit', 'surat-izin-delete', 'surat-izin-list');
        }
        return view('surat-izin.index');
    }

    public function create()
    {
        $currentUser    = Auth::user();
        $suratIzin      = Permission::pluck('nama_menu', 'id')->all();
        return view('surat-izin.create', compact('suratIzin', 'currentUser'));
    }

    public function edit($id)
    {
        $postCategory = PostCategory::pluck('name','id')->all();
        $content = Content::find($id);
        $selectCategory =$content->post_categories_id;
        return view('content.edit', compact('content', 'postCategory', 'selectCategory'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id_taruna' => 'required',
            'start' => 'required',
            'start_time' => 'required',
            'end' => 'required',
            'end_time' => 'required',
            'keluhan'=>'required_if:id_category,1',
            'keperluan'=>'required_if:id_category,2|required_if:id_category,3|required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'tujuan'=>'required_if:id_category,3|required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'pendamping'=>'required_if:id_category,2',
            'pelatih'=>'required_if:id_category,3',
            'nm_tc'=>'required_if:id_category,3',
            'file' => 'required|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);

        if($request->file){
            $image = $this->UploadImage($request->file, config('app.documentImagePath'));
            if($image==false){
                \Session::flash('error', 'file upload failure');
                return redirect()->route('surat-izin.create');

            }
        }

        $currentUser    = Auth::user();

        try {
            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                $getUser = User::where('id', $request->id_user)->first();
                if($currentUser->getRoleNames()[0]!='Taruna'){
                    $input=['start'=>$request->start.' '.$request->start_time,
                            'end'=>$request->end.' '.$request->end_time,
                            'status'=>1,
                            'status_level_1'=>1,
                            'status_level_2'=>1,
                            'reason_level_1'=>'Surat izin dibuatkan superadmin',
                            'reason_level_2'=>'Surat izin dibuatkan superadmin',
                            'user_approve_level_1'=>Auth::user()->id,
                            'user_approve_level_2'=>Auth::user()->id,
                            'date_approve_level_1'=>date('Y-m-d H:i:s'),
                            'date_approve_level_2'=>date('Y-m-d H:i:s'),
                            'grade'=>$getUser->grade,
                            ];
                }else{
                    $input=['status'=>0,
                            'status_level_1'=>0,
                            'status_level_2'=>0,
                            'grade'=>$getUser->grade,
                            ];
                    
                }

                $suratIzin = SuratIzin::create($input);

                if($request->id_category==1){
                    $dataDetail=['stb'=>$getUser->stb,
                                 'keluhan'=>$request->keluhan,
                                 'diagnosa'=>$request->diagnosa,
                                 'rekomendasi'=>$request->rekomendasi,
                                 'dokter'=>$request->dokter,
                                 'status'=>$input['status'],
                                 'user_created'=>$input['user_created'],
                                 'created_at'=>$input['created_at'],
                                 'id_surat'=>$suratIzin->id
                                ];
                    IzinSakit::create($dataDetail);
                }


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('surat-izin.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            @dd($th->getMessage());
            return redirect()->route('surat-izin.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|unique:posts,title,'.$id,
            'excerpt' => 'required',
            'post_categories_id' => 'required',
            'content' => 'required',
            'status' => 'required',
            'file' => 'mimes:jpeg,bmp,png|max:300'
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
                    $image_name= '/storage/'.config('app.postImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s.u')).$k.'.png';
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

            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('content.create');
            }
        }

       try {

            DB::beginTransaction();
            $content = Content::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($content->file, config('app.postImagePath'));
                    }
                }
                $request->request->add(['slug'=> $slug]);
                $request->request->add(['user_updated'=> Auth::user()->id]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('content'));
                $input['content']=$detail;

                $content->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('content.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.postImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('content.create');
        }
    }

    public function destroy($id)
    {
        $content = Content::find($id);
        try {
            DB::beginTransaction();
                $this->DeleteImage($content->photo, config('app.postImagePath'));
                $content->user_deleted = Auth::user()->id;
                $content->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImagePost(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $content = Content::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.postImagePath'));
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $content->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL];
                $content->update($input);
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
