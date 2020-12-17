<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\User;
use App\OrangTua;
use App\Permission;
use App\Prestasi;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use App\Traits\Firebase;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class PrestasiController extends Controller
{
    use ActionTableWithDetail;
    use ImageTrait;
    use Firebase;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:prestasi-taruna-list');
        $this->middleware('permission:prestasi-taruna-create', ['only' => ['create','store']]);
        $this->middleware('permission:prestasi-taruna-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:prestasi-taruna-delete', ['only' => ['destroy']]);
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
                1=>'stb',
                2=>'name',
                3=>'keterangan',
                4=>'tingkat',
                5=>'tempat',
                6=>'waktu',
                7=>'status',
                8=>'created_at',
            );
            $model  = New Prestasi();
            return $this->ActionTableWithDetail($columns, $model, $request, 'prestasi.edit', 'prestasi.show', 'prestasi-taruna-edit', 'prestasi-taruna-delete', 'prestasi-taruna-list');
        }
        return view('prestasi.index');
    }

    public function create()
    {
        $currentUser    = Auth::user();
        return view('prestasi.create', compact('currentUser'));
    }

    public function edit($id)
    {
        $getSurat       = Prestasi::find($id);
        $currentUser    = Auth::user();
        $selectTaruna   = User::find($getSurat->id_user);
        if($currentUser->getRoleNames()[0]=='Taruna'){
            if($currentUser->id!=$getSurat->user_created){
                return view('prestasi.index');
            }
        }

        return view('prestasi.edit', compact('getSurat', 'currentUser', 'selectTaruna'));
    }

    public function show($id)
    {
        $getSurat = Prestasi::join('users as author', 'author.id', '=', 'tb_penghargaan.id_user')
        ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_penghargaan.user_approve_level_1')
        ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'tb_penghargaan.user_disposisi')
        ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_penghargaan.grade')
        ->select('tb_penghargaan.id as id', 
                'tb_penghargaan.id_user as id_user',
                'tb_penghargaan.stb as stb',
                'author.name as nama_taruna',
                'tb_penghargaan.photo as photo',
                'tb_penghargaan.keterangan as keterangan',
                'tb_penghargaan.tingkat as tingkat',
                'tb_penghargaan.tempat as tempat',
                'tb_penghargaan.waktu as waktu',
                'tb_penghargaan.status as status',
                'tb_penghargaan.updated_at as updated_at',
                'user_approve_1.name as user_approve_1',
                'tb_penghargaan.date_approve_level_1 as date_approve_1',
                'tb_penghargaan.reason_level_1 as user_reason_1',
                'tb_penghargaan.status_level_1 as status_level_1',
                'user_disposisi.name as user_disposisi',
                'tb_penghargaan.date_disposisi as date_disposisi',
                'tb_penghargaan.status_disposisi as status_disposisi',
                'tb_penghargaan.reason_disposisi as reason_disposisi',
                'grade.grade as grade'
                )
        ->where('tb_penghargaan.id', $id)
        ->first();
        $data = [];
        if(empty($getSurat)){
            \Session::flash('error', 'Data tidak ditemukan');
             return redirect()->route('prestasi.index'); 
        }
        
        $getUser = Auth::user();
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'stb'=>$getSurat->stb,
            'name'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'keterangan'=>$getSurat->keterangan,
            'tingkat'=>$getSurat->tingkat,
            'tempat'=>$getSurat->tempat,
            'waktu'=>$getSurat->waktu,
            'created_at'=>date('Y-m-d', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/prestasi/".$getSurat->photo : '',
            'form'=>['keterangan', 'tingkat', 'tempat', 'waktu'],
            'status_disposisi'=> $getSurat->status_disposisi,
            'user_disposisi'=>$getSurat->user_disposisi,
            'date_disposisi'=>$getSurat->date_disposisi,
            'reason_disposisi'=>$getSurat->reason_disposisi,
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'show_disposisi'=>false,
            'show_approve'=>false,
            'download'=>false
        );
        if(!empty($request->cetak)){
            return $data;
        }
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }
        $data['show_approve'] = false;

        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
    
        if($roleName=='Pembina' && $getSurat->status_level_1!=1 && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }
        $data['permission'] = [];
        if(($roleName=='Akademik dan Ketarunaan' || $roleName=='Super Admin') && $getSurat->status!=1 && $getSurat->status_disposisi==1){
            $data['show_approve'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetaksurat/id/'.$getSurat->id.'/id_user/'.$getSurat->id_user.'/cetak/prestasi';
        }
    
        $data = json_decode(json_encode($data));

        return view('prestasi.show', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id_user' => 'required',
            'tingkat' =>'required',
            'tempat' =>'required',
            'keterangan' =>'required',
            'waktu' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        $image = false;
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/prestasi/');
            if($image==false){
                \Session::flash('error', 'file upload failure');
                return redirect()->route('prestasi.create'); 
            }
        }
        $id_user = $request->id_user;
        try {
            DB::beginTransaction();
                if(!empty($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['user_created'=> $request->id_user]);
                $request->request->add(['user_updated'=> $request->id_user]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('file', '_token'));
                $getUser = User::where('id', $request->id_user)->first();
                $input['grade']             = $getUser->grade;
                $input['id_user']           = $getUser->id;
                $input['stb']               = $getUser->stb;
                if(Auth::user()->getRoleNames()['0']=='Super Admin'){
                    $input['status'] = 1;
                    $input['status_level_1'] = 1;
                    $input['reason_level_1'] = 'dibuatkan superadmin';
                    $input['user_approve_level_1'] = Auth::user()->id;
                    $input['date_approve_level_1'] = date('Y-m-d H:i:s');

                }else{
                    $input['status_disposisi']  = 0;
                    $input['status_level_1']    = 0;
                    $input['status']            = 0;
                }
                Arr::forget($input, array('_token', 'file'));

                $id = DB::table('tb_penghargaan')->insertGetId($input);

            DB::commit();
            $data['status'] = true;
            $data['firebase'] = false;
            $keluarga = User::keluargataruna($getUser->id);
            $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$getUser->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic = User::topic('createsurat', $dataFirebase);
            if(!empty($topic) && Auth::user()->getRoleNames()['0']!='Super Admin'){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan prestasi baru',
                    'body'=>'prestasi baru telah dibuat',
                    'page'=>'/prestasi/detail/id/'.$id,
                    'token'=>$topic[$i]];
                    try {
                        $firebase = $this->pushNotif($paramsFirebase);
                        $data['firebase'][$i] = $firebase;
                    } catch (\Throwable $th) {
                        $data['firebase'] = $th->getMessage();
                    }
                    sleep(1);
                }
            }
            \Session::flash('success', 'data berhasil ditambah');
            return redirect()->route('prestasi.index'); 
        } catch (\Throwable $th) {
            @dd($th);
            DB::rollBack();
            if($image!=false){
                $this->DeleteImage($image, config('app.documentImagePath').'/prestasi/');
            }
            $data['status'] = false;
            \Session::flash('error', 'terjadi kesalahan server');
            return redirect()->route('prestasi.create'); 
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id_user' => 'required',
            'tingkat' =>'required',
            'tempat' =>'required',
            'keterangan' =>'required',
            'waktu' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;

        $image = false;
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/prestasi/');
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('prestasi.edit', $id);
            }
        }
        $id_user = $request->id_user;
        try {

            DB::beginTransaction();
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $getUser = User::where('id', $request->id_user)->first();
            $suket = Prestasi::where('id_user', $getUser->id)->where('id', $id)->first();
            $input['grade']     = $getUser->grade;
            $input['id_user']   = $getUser->id;
            $input['stb']       = $getUser->stb;
            if(!empty($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                    $this->DeleteImage($suket->photo, config('app.documentImagePath').'/suket/');
                }
            }
            $input = $request->all();
            if(Auth::user()->getRoleNames()['0']=='Super Admin'){
                $input['status'] = 1;
                $input['status_level_1'] = 1;
                $input['reason_level_1'] = 'dibuatkan superadmin';
                $input['user_approve_level_1'] = Auth::user()->id;
                $input['date_approve_level_1'] = date('Y-m-d H:i:s');

            }else{
                $input['status_disposisi']  = 0;
                $input['status_level_1']    = 0;
                $input['status']            = 0;
            }
            Arr::forget($input, array('file', '_token'));
            $suket->update($input);
            DB::commit();
            $data['status'] = true;
            $data['firebase'] = false;
            $keluarga = User::keluargataruna($getUser->id);
            $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$getUser->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic = User::topic('createsurat', $dataFirebase);
            if(!empty($topic) && Auth::user()->getRoleNames()['0']!='Super Admin'){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan prestasi baru',
                    'body'=>'prestasi baru telah dibuat',
                    'page'=>'/prestasi/detail/id/'.$id,
                    'token'=>$topic[$i]];
                    try {
                        $firebase = $this->pushNotif($paramsFirebase);
                        $data['firebase'][$i] = $firebase;
                    } catch (\Throwable $th) {
                        $data['firebase'] = $th->getMessage();
                    }
                    sleep(1);
                }
            }
            \Session::flash('success', 'Data berhasil disimpan');
            return redirect()->route('prestasi.index');  
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath').'/prestasi/');
                }
            }
            $data['status'] = false;
            \Session::flash('error', 'Terjadi Kesalahan Server');
            return redirect()->route('prestasi.index'); 

        }
    }

    public function destroy($id)
    {
        $prestasi      = Prestasi::find($id);
        $currentUser    = Auth::user();
        if($currentUser->getRoleNames()[0]=='Orang Tua'){
            if($currentUser->id!=$prestasi->user_created){
                return false;
            }
        }
        if($currentUser->getRoleNames()[0]=='Taruna'){
            if($currentUser->id!=$prestasi->user_created){
                return false;
            }
        }
        try {
            DB::beginTransaction();
                //$this->DeleteImage($prestasi->photo, config('app.documentImagePath'));
                $prestasi->user_deleted = Auth::user()->id;
                $prestasi->save();
                $prestasi->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImagePrestasi(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $prestasi = Prestasi::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.documentImagePath').'/prestasi/');
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL, 'updated_at'=> date('Y-m-d H:i:s')];
                    $prestasi->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL, 'updated_at'=> date('Y-m-d H:i:s')];
                $prestasi->update($input);
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
