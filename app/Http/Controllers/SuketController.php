<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\User;
use App\OrangTua;
use App\Permission;
use App\Suket;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use App\Traits\Firebase;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class SuketController extends Controller
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
        $this->middleware('permission:surat-keterangan-list');
        $this->middleware('permission:surat-keterangan-create', ['only' => ['create','store']]);
        $this->middleware('permission:surat-keterangan-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:surat-keterangan-delete', ['only' => ['destroy']]);
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
                2=>'keperluan',
                3=>'status',
                4=>'created_at',
            );
            $model  = New Suket();
            return $this->ActionTableWithDetail($columns, $model, $request, 'suket.edit', 'suket.show', 'surat-keterangan-edit', 'surat-keterangan-delete', 'surat-keterangan-list');
        }
        return view('suket.index');
    }

    public function create()
    {
        $currentUser    = Auth::user();
        return view('suket.create', compact('currentUser'));
    }

    public function edit($id)
    {
        $getSurat       = Suket::find($id);
        $currentUser    = Auth::user();
        $selectTaruna   = User::find($getSurat->id_user);
        if($currentUser->getRoleNames()[0]=='Orang Tua' || $currentUser->getRoleNames()[0]=='Taruna' ){
            if($currentUser->id!=$getSurat->user_created){
                return view('suket.index');
            }
        }

        return view('suket.edit', compact('getSurat', 'currentUser', 'selectTaruna'));
    }

    public function show($id)
    {
        $getSurat = Suket::join('users as author', 'author.id', '=', 'tb_suket.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_suket.user_approve_level_1')
                                    ->leftjoin('users as user_approve_2', 'user_approve_2.id', '=', 'tb_suket.user_approve_level_2')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'tb_suket.user_disposisi')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_suket.grade')
                                    ->select('tb_suket.id as id', 
                                            'tb_suket.id_user as id_user',
                                            'tb_suket.stb as stb',
                                            'author.name as nama_taruna',
                                            'tb_suket.photo as photo',
                                            'tb_suket.ttl as ttl',
                                            'tb_suket.orangtua as orangtua',
                                            'tb_suket.pekerjaan as pekerjaan',
                                            'tb_suket.status as status',
                                            'tb_suket.alamat as alamat',
                                            'tb_suket.keperluan as keperluan',
                                            'tb_suket.updated_at as updated_at',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_suket.date_approve_level_1 as date_approve_1',
                                            'tb_suket.reason_level_1 as user_reason_1',
                                            'tb_suket.status_level_1 as status_level_1',
                                            'user_approve_2.name as user_approve_2',
                                            'tb_suket.date_approve_level_2 as date_approve_2',
                                            'tb_suket.reason_level_2 as user_reason_2',
                                            'tb_suket.status_level_2 as status_level_2',
                                            'user_disposisi.name as user_disposisi',
                                            'tb_suket.date_disposisi as date_disposisi',
                                            'tb_suket.status_disposisi as status_disposisi',
                                            'tb_suket.reason_disposisi as reason_disposisi',
                                            'tb_suket.user_created as user_created',
                                            'grade.grade as grade'
                                            )
                                    ->where('tb_suket.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            \Session::flash('error', 'Data tidak ditemukan');
             return redirect()->route('suket.index'); 
        }
        
        $getUser = Auth::user();
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'stb'=>$getSurat->stb,
            'name'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'ttl'=>$getSurat->ttl,
            'orangtua'=>$getSurat->orangtua,
            'pekerjaan'=>$getSurat->pekerjaan,
            'alamat'=>$getSurat->alamat,
            'keperluan'=>$getSurat->keperluan,
            'created_at'=>date('Y-m-d', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/suket/".$getSurat->photo : '',
            'form'=>['ttl', 'orangtua', 'pekerjaan', 'alamat', 'keperluan'],
            'status_disposisi'=> $getSurat->status_disposisi,
            'user_disposisi'=>$getSurat->user_disposisi,
            'date_disposisi'=>$getSurat->date_disposisi,
            'reason_disposisi'=>$getSurat->reason_disposisi,
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'user_approve_2'=>$getSurat->user_approve_2,
            'date_approve_2'=>$getSurat->date_approve_2,
            'status_level_2'=>$getSurat->status_level_2,
            'reason_level_2'=>$getSurat->user_reason_2,
            'show_disposisi'=>false,
            'show_persetujuan'=>false,
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
        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
        $data['show_persetujuan'] = false;
        if($roleName=='Pembina' && $data['status_level_1']!=1 && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }

        $data['permission'] = [];
        if(($roleName=='Taruna' || $roleName=='Orang Tua') && $getSurat->status_disposisi!=1 && $getSurat->status!=1) {
            if($getSurat->user_created==$getUser->id){
                $data['permission'] = ['edit', 'delete'];
            }
        }
        if($roleName=='Akademik dan Ketarunaan' && $data['status_level_1']!=1 && $getSurat->status_disposisi==1){
            $data['show_persetujuan'] = true;
        }

        if($roleName=='Direktur' && $data['status']!=1 && $data['status_level_1']==1){
            $data['show_persetujuan'] = true;
        }

        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/cetaksurat/id/'.$id.'/id_user/'.$getUser->id.'/cetak/suket';;
        }
    
        $data = json_decode(json_encode($data));

        return view('suket.show', compact('data'));
    }

    public function store(Request $request)
    {
       $this->validate($request, [
            'id_user' => 'required',
            'ttl' =>'required',
            'orangtua' =>'required',
            'pekerjaan' =>'required',
            'alamat' =>'required',
            'keperluan' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        $image = false;
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/suket/');
            if($image==false){
                \Session::flash('error', 'file upload failure');
                return redirect()->route('suket.create'); 
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
                if($getUser->getRoleNames()[0]=='Orang Tua'){
                    $taruna     = OrangTua::where('orangtua_id', $id_user)->first(); 
                    if(empty($taruna)){
                        \Session::flash('error', 'Taruna Tidak Ada');
                        return redirect()->route('suket.create');  
                    }
                    $getUser    = User::where('id', $taruna->taruna_id)->first();
                }
                $input['grade']             = $getUser->grade;
                $input['nama']              = $getUser->name;
                $input['id_user']           = $getUser->id;
                $input['stb']               = $getUser->stb;
                if(Auth::user()->getRoleNames()['0']=='Super Admin'){
                    $input['status'] = 1;
                    $input['status_level_1'] = 1;
                    $input['status_level_2'] = 1;
                    $input['reason_level_1'] = 'Surat izin dibuatkan superadmin';
                    $input['reason_level_2'] = 'Surat izin dibuatkan superadmin';
                    $input['user_approve_level_1'] = Auth::user()->id;
                    $input['user_approve_level_2'] = Auth::user()->id;
                    $input['date_approve_level_1'] = date('Y-m-d H:i:s');
                    $input['date_approve_level_2'] = date('Y-m-d H:i:s');

                }else{
                    $input['status_disposisi']  = 0;
                    $input['status_level_1']    = 0;
                    $input['status_level_2']    = 0;
                    $input['status']            = 0;
                }
                Arr::forget($input, array('_token', 'file'));

                $id = DB::table('tb_suket')->insertGetId($input);

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
                    $paramsFirebase=['title'=>'Pemberitahuan surat keterangan baru',
                    'body'=>'surat keterangan  baru telah dibuat',
                    'page'=>'/riwayat-izin/detail/id/'.$id,
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
            return redirect()->route('suket.index'); 
        } catch (\Throwable $th) {
            @dd($th);
            DB::rollBack();
            if($image!=false){
                $this->DeleteImage($image, config('app.documentImagePath').'/suket/');
            }
            $data['status'] = false;
            \Session::flash('error', 'terjadi kesalahan server');
            return redirect()->route('suket.create'); 
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id_user' => 'required',
            'ttl' =>'required',
            'orangtua' =>'required',
            'pekerjaan' =>'required',
            'alamat' =>'required',
            'keperluan' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;

        $image = false;
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/suket/');
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('surat-izin.edit', $id);
            }
        }
        $id_user = $request->id_user;
        try {

            DB::beginTransaction();
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $getUser = User::where('id', $request->id_user)->first();
            if($getUser->getRoleNames()[0]=='Orang Tua'){
                $taruna = OrangTua::where('orangtua_id', $id_user)->first(); 
                if(empty($taruna)){
                    \Session::flash('error', 'Taruna Tidak Ada');
                    return redirect()->route('suket.create');  
                }
                $getUser = User::where('id', $taruna->taruna_id)->first();
            }
            $suket = Suket::where('id_user', $getUser->id)->where('id', $id)->first();
            $input['grade']     = $getUser->grade;
            $input['id_user']   = $getUser->id;
            $input['nama']      = $getUser->name;
            $input['stb']       = $getUser->stb;
            if(!empty($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                    $this->DeleteImage($suket->photo, config('app.documentImagePath').'/suket/');
                }
            }
            $input = $request->all();
            if($getUser->getRoleNames()[0]=='Super Admin'){
                $input['status'] = 1;
                $input['status_level_1'] = 1;
                $input['status_level_2'] = 1;
                $input['reason_level_1'] = 'Surat izin dibuatkan superadmin';
                $input['reason_level_2'] = 'Surat izin dibuatkan superadmin';
                $input['user_approve_level_1'] = Auth::user()->id;
                $input['user_approve_level_2'] = Auth::user()->id;
                $input['date_approve_level_1'] = date('Y-m-d H:i:s');
                $input['date_approve_level_2'] = date('Y-m-d H:i:s');

            }else{
                $input['status_disposisi']  = 0;
                $input['status_level_1']    = 0;
                $input['status_level_2']    = 0;
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
                    $paramsFirebase=['title'=>'Pemberitahuan surat keterangan baru',
                    'body'=>'surat keterangan baru telah dibuat',
                    'page'=>'/suket/detail/id/'.$id,
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
            return redirect()->route('suket.index');  
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath').'/suket/');
                }
            }
            $data['status'] = false;
            \Session::flash('error', 'Terjadi Kesalahan Server');
            return redirect()->route('suket.index'); 

        }
    }

    public function destroy($id)
    {
        $suket      = Suket::find($id);
        $currentUser    = Auth::user();
        if($currentUser->getRoleNames()[0]=='Orang Tua'){
            if($currentUser->id!=$suket->user_created){
                return false;
            }
        }
        if($currentUser->getRoleNames()[0]=='Taruna'){
            if($currentUser->id!=$suket->user_created){
                return false;
            }
        }
        try {
            DB::beginTransaction();
                //$this->DeleteImage($suket->photo, config('app.documentImagePath'));
                $suket->user_deleted = Auth::user()->id;
                $suket->save();
                $suket->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageSuket(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $suket = Suket::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.documentImagePath').'/suket/');
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL, 'updated_at'=> date('Y-m-d H:i:s')];
                    $suket->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL, 'updated_at'=> date('Y-m-d H:i:s')];
                $suket->update($input);
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
