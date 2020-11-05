<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\User;
use App\SuratIzin;
use App\IzinSakit;
use App\KeluarKampus;
use App\TrainingCenter;
use App\PernikahanSaudara;
use App\PemakamanKeluarga;
use App\OrangTuaSakit;
use App\KegiatanDalam;
use App\Tugas;
use App\KegiatanPesiar;
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
        $getSurat           = SuratIzin::find($id);
        $currentUser        = Auth::user();
        $suratIzin          = Permission::pluck('nama_menu', 'id')->all();
        $selectSuratIzin    = $getSurat->id_category;
        return view('surat-izin.edit', compact('getSurat', 'currentUser', 'suratIzin', 'selectSuratIzin'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id_user' => 'required',
            'start' => 'required',
            'start_time' => 'required',
            'end' => 'required',
            'end_time' => 'required',
            'id_category' =>'required',
            'keluhan'=>'required_if:id_category,1',
            'keperluan'=>'required_if:id_category,2|required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'tujuan'=>'required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'pendamping'=>'required_if:id_category,2',
            'pelatih'=>'required_if:id_category,3',
            'nm_tc'=>'required_if:id_category,3',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);

        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath'));
            if($image==false){
                \Session::flash('error', 'file upload failure');
                return redirect()->route('surat-izin.create');

            }
        }

        $currentUser    = Auth::user();

        try {
            DB::beginTransaction();
                if(!empty($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('_token', 'file', 'start_time', 'end_time', 'keluhan', 'diagnosa', 'rekomendasi', 'dokter', 'pendamping', 'keperluan', 'tujuan', 'nm_tc', 'pelatih'));
                $getUser = User::where('id', $request->id_user)->first();

                if($currentUser->getRoleNames()[0]!='Taruna'){
                    $input['start'] = $request->start.' '.$request->start_time;
                    $input['end']   = $request->end.' '.$request->end_time;
                    $input['status'] = 1;
                    $input['status_level_1'] = 1;
                    $input['status_level_2'] = 1;
                    $input['reason_level_1'] = 'Surat izin dibuatkan superadmin';
                    $input['reason_level_2'] = 'Surat izin dibuatkan superadmin';
                    $input['user_approve_level_1'] = Auth::user()->id;
                    $input['user_approve_level_2'] = Auth::user()->id;
                    $input['date_approve_level_1'] = date('Y-m-d H:i:s');
                    $input['date_approve_level_2'] = date('Y-m-d H:i:s');
                    $input['grade'] = $getUser->grade;
                }else{     
                    $input['status'] = 0;
                    $input['status_level_1'] = 0;
                    $input['status_level_2'] = 0;
                    $input['grade'] = $getUser->grade;   
                }
                
                $id = DB::table('surat_header')->insertGetId($input);
               
                if($request->id_category==1){
                    $dataDetail=['stb'=>$getUser->stb,
                                 'keluhan'=>$request->keluhan,
                                 'diagnosa'=>$request->diagnosa,
                                 'rekomendasi'=>$request->rekomendasi,
                                 'dokter'=>$request->dokter,
                                 'status'=>$input['status'],
                                 'user_created'=>$input['user_created'],
                                 'created_at'=>$input['created_at'],
                                 'id_user'=>$getUser->id,
                                 'id_surat'=>$id
                                ];
                    IzinSakit::create($dataDetail);
                }
                if($request->id_category==2){
                    $dataDetail=['stb'=>$getUser->stb,
                                 'keperluan'=>$request->keperluan,
                                 'pendamping'=>$request->pendamping,
                                 'status'=>$input['status'],
                                 'user_created'=>$input['user_created'],
                                 'created_at'=>$input['created_at'],
                                 'id_user'=>$getUser->id,
                                 'id_surat'=>$id
                                ];
                    KeluarKampus::create($dataDetail);
                }
                if($request->id_category==3){
                    $dataDetail=['stb'=>$getUser->stb,
                                 'training'=>$request->training,
                                 'nm_tc'=>$request->nm_tc,
                                 'status'=>$input['status'],
                                 'user_created'=>$input['user_created'],
                                 'created_at'=>$input['created_at'],
                                 'id_user'=>$getUser->id,
                                 'id_surat'=>$id
                                ];
                    TrainingCenter::create($dataDetail);
                }
                if($request->id_category==4){
                    $dataDetail=[
                        'stb'=>$getUser->stb,
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_created'=>$input['user_created'],
                        'created_at'=>$input['created_at'],
                        'id_user'=>$getUser->id,
                        'id_surat'=>$id
                    ];
                    PernikahanSaudara::create($dataDetail);
                }
                if($request->id_category==5){
                    $dataDetail=[
                        'stb'=>$getUser->stb,
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_created'=>$input['user_created'],
                        'created_at'=>$input['created_at'],
                        'id_user'=>$getUser->id,
                        'id_surat'=>$id
                    ];
                    PemakamanKeluarga::create($dataDetail);
                }
                if($request->id_category==6){
                    $dataDetail=[
                        'stb'=>$getUser->stb,
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_created'=>$input['user_created'],
                        'created_at'=>$input['created_at'],
                        'id_user'=>$getUser->id,
                        'id_surat'=>$id
                    ];
                    OrangTuaSakit::create($dataDetail);
                }
                if($request->id_category==7){
                    $dataDetail=[
                        'stb'=>$getUser->stb,
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_created'=>$input['user_created'],
                        'created_at'=>$input['created_at'],
                        'id_user'=>$getUser->id,
                        'id_surat'=>$id
                    ];
                    Tugas::create($dataDetail);
                }
                if($request->id_category==8){
                    $dataDetail=[
                        'stb'=>$getUser->stb,
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_created'=>$input['user_created'],
                        'created_at'=>$input['created_at'],
                        'id_user'=>$getUser->id,
                        'id_surat'=>$id
                    ];
                    KegiatanDalam::create($dataDetail);
                }
                if($request->id_category==9){
                    $dataDetail=[
                        'stb'=>$getUser->stb,
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_created'=>$input['user_created'],
                        'created_at'=>$input['created_at'],
                        'id_user'=>$getUser->id,
                        'id_surat'=>$id
                    ];
                    KegiatanPesiar::create($dataDetail);
                }


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('surat-izin.index');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            
            return redirect()->route('surat-izin.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id_user' => 'required',
            'start' => 'required',
            'start_time' => 'required',
            'end' => 'required',
            'end_time' => 'required',
            'id_category' =>'required',
            'keluhan'=>'required_if:id_category,1',
            'keperluan'=>'required_if:id_category,2|required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'tujuan'=>'required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'pendamping'=>'required_if:id_category,2',
            'pelatih'=>'required_if:id_category,3',
            'nm_tc'=>'required_if:id_category,3',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);

        if($request->file){

            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('surat-izin.edit');
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
        $content = SuratIzin::find($id);
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
            $deleteFile = $this->DeleteImage($image, config('app.documentImagePath'));
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
