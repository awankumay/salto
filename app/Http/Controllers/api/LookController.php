<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Content;
use App\Grade;
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
use App\OrangTua;
use App\WaliasuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\Provinces;
use App\Regencies;
use App\Permission;
use App\Prestasi;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use DB;
use App\Absensi;
use App\JurnalTaruna;
use App\Traits\ImageTrait;
use App\Traits\Firebase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class LookController extends BaseController
{
    use ImageTrait;
    use Firebase;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $success['data']='ok';
        return $this->sendResponse($success, 'User login successfully.');
    }
    
    public function getprofile(Request $request)
    {
        $user = User::where('id', $request->id_user)->first();
        
        $data=['id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'phone'=>$user->phone,
                'whatsapp'=>$user->whatsapp,
                'alamat'=>$user->address,
                'sex_name'=>$user->sex==1 ? 'Laki-laki' : 'Perempuan',
                'sex'=>$user->sex,
                'photo'=>!empty($user->photo) ? url('/')."/storage/".config('app.userImagePath')."/".$user->photo : url('/').'/profile.png',
                'form'=>['name', 'email', 'phone', 'whatsapp', 'address', 'sex', 'file', 'password', 'confirm-password']
                ];
        $grade      = Grade::where('id', $user->grade)->first();
        $keluarga   = User::keluargataruna($user->id);
        $provinces  = Provinces::where('id', $user->province_id)->first();
        $regencies  = Regencies::where('id', $user->regencie_id)->first();
        $data['show_grade']=false;
        $data['show_keluarga_asuh']=false;
        if($user->getRoleNames()[0]=='Taruna'){
            if(!empty($grade)){
                $data['show_grade']=true;
                $data['grade']=$grade->grade;
            }
        }
        if(!empty($keluarga)){
            $data['show_keluarga_asuh']=true;
            $data['keluarga_asuh']=$keluarga->name;
          
        }
        $data['provinces']  = !empty($provinces) ? $provinces->name : null;
        $data['regencies']  = !empty($regencies) ? $regencies->name : null;
        return $this->sendResponse($data, 'profile load successfully.');

    }

    public function setprofile(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $data = [];

        $data['success'] = false;
        $validator = Validator::make($request->all(), 
        [ 
            'id' => 'required',
            'file' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'email' => "required|email|unique:users,email,{$request->id},id,deleted_at,NULL",
            'password' => 'same:confirm-password',
            'phone' => "required|numeric|unique:users,phone,{$request->id},id,deleted_at,NULL",
            'whatsapp' => "numeric|unique:users,whatsapp,{$request->id},id,deleted_at,NULL",
            'sex'=>'required',
            'address'=>'required',
            'name'=>'required'
        ]); 
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }  
        $image=false;
        if($request->file){
            $image = $this->UploadImage($request->file, config('app.userImagePath'));
            if($image==false){
                return $this->sendResponseError($data, 'failure upload image'); 
            }
            $this->DeleteImage($user->photo, config('app.userImagePath'));
        }
        try {
            if(!empty($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                }
            }
            $request->request->add(['user_updated'=> $user->id]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $input = $request->all();

            if(!empty($input['password'])){
                $input['password'] = Hash::make($input['password']);
            }else{
               Arr::forget($input, array('password', 'confirm-password'));
            }
            DB::beginTransaction();
                $user->update($input);
            DB::commit();
            $data['success']=true;
            return $this->sendResponse($data, 'success update profile'); 
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.userImagePath'));
                }
            return $this->sendResponseError($data, 'failure update profile'); 
        }

    }

    public function getregencies(Request $request)
    {
        $data = Regencies::where('province_id', $request->id)->pluck('name','id')->all();
        return $this->sendResponse($data, 'regencies load successfully.');

    }

    public function getslider(Request $request)
    {
        $result=[];
        $data = Content::where('status', 1)->where('headline', 1)->select('id','photo')->get();
        foreach ($data as $key => $value) {
            $result[]=[
                'id'=>$value->id,
                'photo'=>url('/')."/storage/".config('app.postImagePath')."/".$value->photo
            ];
        }
        return $this->sendResponse($result, 'slider load successfully.');

    }

    public function getberitadetail(Request $request)
    {
        $data = Content::where('status', 1)->where('id', $request->id)->select('id','photo','title','content','created_at')->first();
        $data->photo = url('/')."/storage/".config('app.postImagePath')."/".$data->photo;
        return $this->sendResponse($data, 'detail slider load successfully.');

    }

    public function getberita(Request $request){
        $limit  = 5;
        $id_category = !empty($request->id_category) ? $request->id_category : 0;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'id';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $total = Content::where('status', 1)
                    ->where('post_categories_id', $id_category)
                    ->count();
        
        if($lastId==0){
            $data = Content::where('status', 1)
                        ->where('post_categories_id', $id_category)
                        ->select('id','photo','title','excerpt','content','created_at')
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
            $count = Content::where('status', 1)
                        ->where('post_categories_id', $id_category)
                        ->count();
        }else{
            $data = Content::where('status', 1)
                    ->where('id', '<', $lastId)
                    ->where('post_categories_id', $id_category)
                    ->select('id','photo','title','excerpt','content','created_at')
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            $count = Content::where('status', 1)
                    ->where('id', '<', $lastId)
                    ->where('post_categories_id', $id_category)
                    ->count();
        }
        $result =[];
        foreach ($data as $key => $value) {
            if ($request->id_category==2) {
                $result['berita'][]= [ 
                    'id'=>$value->id,
                    'title'=>$value->title,
                    'excerpt'=>$value->excerpt,
                    'content'=>$value->content,
                    'created_at'=>date_format($value->created_at, 'Y-m-d H:i'),
                    'file'=> $value->file!=null ? url('/')."/storage/".config('app.documentImagePath')."/".$value->file : null
                ];
            }else{
                $result['berita'][]= [ 
                    'id'=>$value->id,
                    'title'=>$value->title,
                    'excerpt'=>$value->excerpt,
                    'created_at'=>date_format($value->created_at, 'Y-m-d H:i'),
                    'photo'=> url('/')."/storage/".config('app.postImagePath')."/".$value->photo
                ];
            }
        }

        if($count > $limit){
            $result['info']['lastId'] = $data[count($data)-1]->id;
            $result['info']['loadmore'] = true;
            $result['info']['dataload'] = count($data);
            $result['info']['totaldata'] = $total;
        }else{
            $result['info']['lastId'] = 0;
            $result['info']['loadmore'] = false;
            $result['info']['dataload'] = count($data);
            $result['info']['totaldata'] = $total;
        }
        $result['info']['limit']  = $limit;
        return $this->sendResponse($result, 'berita load successfully.');
    }



    public function checkabsen($request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $absensi = Absensi::where('id_user', $request->id_user)->whereRaw('DATE(created_at) = ?', date('Y-m-d'))->first();
        $data = [];
        if($roleName=='Taruna'){
            if(empty($absensi)){
                $data['clock_in'] = true;
                $data['clock_out'] = true;
            }else{
                if(!empty($absensi)){
                    $data['clock_in'] = !empty($absensi->clock_in) ? date('d-m-Y H:i', strtotime($absensi->clock_in)) : true;
                    $data['clock_out'] = !empty($absensi->clock_out) ? date('d-m-Y H:i', strtotime($absensi->clock_out)) : true;
                }
            }
            $data['absen'] = true;
        }else{
            $data['absen'] = false;
        }
        return $data;
    }

    public function getabsen(Request $request)
    {
        $data = $this->checkabsen($request);
        return $this->sendResponse($data, 'berhasil check absensi');

    }


    public function clockin(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $data = [];
        $validator = Validator::make($request->all(), 
                    [ 
                        'id_user' => 'required',
                        'file_clock_in' => 'required|mimes:jpg,jpeg,png|max:2048',
                    ]);   
 
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }  
        if ($files = $request->file('file_clock_in')) {
            $file = $this->UploadImage($request->file_clock_in, config('app.documentImagePath').'/absensi/');
            if($file!=false){
                try {
                    DB::beginTransaction();
                    $getUser = User::where('id', $request->id_user)->first();
                    $absensi = New Absensi();
                    $absensi->id_user = $request->id_user;
                    $absensi->clock_in = date('Y-m-d H:i:s');
                    $absensi->file_clock_in = $file;
                    $absensi->created_at = date('Y-m-d H:i:s');
                    $absensi->lat_in = !empty($request->lat) ? $request->lat : '-' ;
                    $absensi->long_in = !empty($request->long) ? $request->long : '-' ;
                    $absensi->grade = !empty($getUser->grade) ? $getUser->grade : null;
                    $absensi->save();
                    $jurnal = New JurnalTaruna();
                    $jurnal->id_user = $request->id_user;
                    $jurnal->grade = !empty($getUser->grade) ? $getUser->grade : null;
                    $jurnal->tanggal = date('Y-m-d');
                    $jurnal->kegiatan = 'Clock In / Apel Pagi';
                    $jurnal->status = 0;
                    $jurnal->start_time = date('Y-m-d H:i:s');
                    $jurnal->end_time = date('Y-m-d H:i:s');
                    $jurnal->created_at = date('Y-m-d H:i:s');
                    $jurnal->save();
                    DB::commit();
                } catch (\Throwable $th) {
                    @dd($th);
                    DB::rollBack();
                    return $this->sendResponseError($data, 'Terjadi Kesalahan Server');  
                }
            }else{
                return $this->sendResponseError($data, 'Terjadi Kesalahan Server'); 
            }
              
            $result=[
                "success" => true,
                "file" => $file
            ];
            return $this->sendResponse($result, 'berhasil clock in');
        }
        
    }

    public function clockout(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), 
                    [ 
                        'id_user' => 'required',
                        'file_clock_out' => 'required|mimes:jpg,jpeg,png|max:2048',
                    ]);   
        $data = [];
        if ($validator->fails()) {          
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                        
        }  
        if ($files = $request->file('file_clock_out')) {
            $file = $this->UploadImage($request->file_clock_out, config('app.documentImagePath').'/absensi/');
            if($file!=false){
                try {
                    $absensi = Absensi::whereRaw('DATE(created_at) = ?', date('Y-m-d'))->where('id_user', $request->id_user)->first();
                    $jurnal = New JurnalTaruna();
                    $getUser = User::where('id', $request->id_user)->first();
                    DB::beginTransaction();
                    $absensi->clock_out = date('Y-m-d H:i:s');
                    $absensi->file_clock_out = $file;
                    $absensi->updated_at = date('Y-m-d H:i:s');
                    $absensi->lat_out = !empty($request->lat) ? $request->lat : '-' ;
                    $absensi->long_out = !empty($request->long) ? $request->long : '-' ;
                    $absensi->update();
                    $jurnal->id_user = $request->id_user;
                    $jurnal->grade = !empty($getUser->grade) ? $getUser->grade : null;
                    $jurnal->tanggal = date('Y-m-d');
                    $jurnal->kegiatan = 'Clock Out';
                    $jurnal->status = 1;
                    $jurnal->start_time = date('Y-m-d H:i:s');
                    $jurnal->end_time = date('Y-m-d H:i:s');
                    $jurnal->created_at = date('Y-m-d H:i:s');
                    $jurnal->save();
                    JurnalTaruna::whereRaw('DATE(created_at) = ?', date('Y-m-d'))->where('id_user', $request->id_user)->update(['status' => 1]);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return $this->sendResponseError($data, 'Terjadi Kesalahan Server');   
                }
            }else{
                return $this->sendResponseError($data, 'Terjadi Kesalahan Server');
            }
              
            $result=[
                "success" => true,
                "file" => $file
            ];
            return $this->sendResponse($result, 'berhasil clock out');
        }
        
    }

    public function inputjurnal(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $data = [];
        $validator = Validator::make($request->all(), 
                    [ 
                        'id_user' => 'required',
                        'kegiatan' => 'required',
                        'start_time' => 'required',
                        'end_time' => 'required',
                    ]);   
 
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }

        $getUser = User::where('id', $request->id_user)->first();
        
        if($request->id){
            try {
                DB::beginTransaction();
                $jurnal = JurnalTaruna::where('id', $request->id)->first();
                $input = $request->all();
                Arr::forget($input, array('start_time', 'end_time'));
                $input['start_time'] = date_create(date('Y-m-d').' '.$request->start_time);
                $input['end_time'] = date_create(date('Y-m-d').' '.$request->end_time);
                //$input['updated_at'] = date('Y-m-d H:i:s');
                $input['status'] = 0;
                $input['grade'] = !empty($getUser->grade) ? $getUser->grade : null;
                $jurnal->update($input);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->sendResponseError($data, 'Terjadi Kesalahan Server');
            }
        }else{
            try {
                DB::beginTransaction();
                $input = $request->all();
                Arr::forget($input, array('start_time', 'end_time'));
                $input['start_time'] = date_create(date('Y-m-d').' '.$request->start_time);
                $input['end_time'] = date_create(date('Y-m-d').' '.$request->end_time);
                //$input['created_at'] = date('Y-m-d H:i:s');
                $input['status'] = 0;
                $input['tanggal'] = date('Y-m-d');
                $input['grade'] = !empty($getUser->grade) ? $getUser->grade : null;
                JurnalTaruna::create($input);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                @dd($th);
                return $this->sendResponseError($data, 'Terjadi Kesalahan Server');
            }
        }

        return $this->sendResponse($data, 'success');
        
    }


    public function getjurnal(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastDate = !empty($request->lastDate) ? $request->lastDate : date('Y-m-d');
        $order  = !empty($request->order) ? $request->order : 'jurnal_taruna.tanggal';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'jurnal_taruna.tanggal='.$lastDate.'';
        $getUser = User::find($request->idUser);
        if(!empty($search)){
            $getUser = User::find($search);
            $id_user = $getUser->id;
        }
        $roleName = $getUser->getRoleNames()[0];
        $result = [];
        $data=[];
        $result['info']['permission'] = [];
        if($order=='status'){
            $order='jurnal_taruna.status';
        }
        if($lastDate==date('Y-m-d')){
            if($roleName=='Taruna'){
                $condition  = 'jurnal_taruna.id_user='.$id_user.'';
                $total      = JurnalTaruna::where('id_user', $id_user)
                                ->count(DB::raw('DISTINCT tanggal'));     
                $count  = $total;
                $data   = $this->jurnaltaruna($condition, $limit, $order, $dir);
                $result['info']['permission'] = ['create', 'delete', 'edit'];
            }else if($roleName=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'jurnal_taruna.id_user in('.$getTaruna.')';
                $total      = JurnalTaruna::whereRaw($condition)
                                ->count(DB::raw('DISTINCT tanggal'));         
                $count  = $total;
                $data   = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'jurnal_taruna.id_user in('.$getTaruna.')';
                $total      = JurnalTaruna::whereRaw($condition)
                                ->count(DB::raw('DISTINCT tanggal'));        
                $count  = $total;
                $data   = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'jurnal_taruna.id_user in('.$getTaruna.')';
                $total      = JurnalTaruna::whereRaw($condition)
                                ->count(DB::raw('DISTINCT tanggal'));        
                $count  = $total;
                $data   = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $taruna     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 7)
                                ->whereNull('users.deleted_at')
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'jurnal_taruna.id_user in('.$getTaruna.')';
                $total      = JurnalTaruna::whereRaw($condition)
                                ->count(DB::raw('DISTINCT tanggal'));        
                $count  = $total;
                $data   = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }
        }else {
            if($roleName=='Taruna'){
                $condition = 'jurnal_taruna.id_user='.$id_user.' AND jurnal_taruna.tanggal '.$diff.' \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw($condition)
                            ->count(DB::raw('DISTINCT tanggal')); 
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);
                $result['info']['permission'] = ['create', 'delete', 'edit'];
            }else if($roleName=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal '.$diff.' \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw('jurnal_taruna.id_user in('.$getTaruna.')')
                        ->count(DB::raw('DISTINCT tanggal'));
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal '.$diff.' \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw('jurnal_taruna.id_user in('.$getTaruna.')')
                        ->count(DB::raw('DISTINCT tanggal'));
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);

            }else if($roleName=='Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal '.$diff.' \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw('jurnal_taruna.id_user in('.$getTaruna.')')
                            ->count(DB::raw('DISTINCT tanggal'));
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);

            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $taruna     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 7)
                                ->whereNull('users.deleted_at')
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal '.$diff.' \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw('jurnal_taruna.id_user in('.$getTaruna.')')
                        ->count(DB::raw('DISTINCT tanggal'));
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }
        }
        foreach ($data as $key => $value) {
                $result['jurnal'][]= [ 
                    'id_user'=>$value->id_user,
                    'name'=>$value->name,
                    'tanggal'=>$value->tanggal,
                    'status_name'=> $value->status==1 ? 'Terkirim' : 'Belum Terkirim',
                    'status'=> $value->status,

                ];
        }
        
        $result['info']['permissionCreate'] = false;
        if($roleName=='Taruna' && empty($search)){
            $result['info']['permissionCreate'] = true;
        }

        if($count > $limit){
            $result['info']['lastDate'] = $data[count($data)-1]->tanggal;
            $result['info']['loadmore'] = true;
            $result['info']['dataload'] = count($data);
            $result['info']['totaldata'] = $total;
        }else{
            $result['info']['lastDate'] = null;
            $result['info']['loadmore'] = false;
            $result['info']['dataload'] = count($data);
            $result['info']['totaldata'] = $total;
        }
     
        $result['info']['limit']  = $limit;
        return $this->sendResponse($result, 'jurnal load successfully.');
    }

    public function getjurnaldetail(Request $request)
    {
        #bugs
        $data = [];
        $date = $request->date;
        $id_user =$request->id_user;
        $getUser = User::find($request->id_login);
        $roleName = $getUser->getRoleNames()[0];
        $jurnal = JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                    ->join('grade_table', 'grade_table.id', '=', 'jurnal_taruna.grade')
                    ->whereRaw('jurnal_taruna.tanggal = ?', $date)
                    ->where('users.id', $id_user)
                    ->select('jurnal_taruna.*', 'users.name as nama_taruna', 'grade_table.grade as nama_grade')
                    ->get();
        foreach ($jurnal as $key => $value) {
            $data['jurnal'][]=array(
                'id'=>$value->id,
                'id_user'=>$value->id_user,
                'name'=>$value->nama_taruna,
                'grade'=>$value->grade,
                'grade_name'=>$value->nama_grade,
                'start_time'=>date_format(date_create($value->start_time), 'H:i'),
                'end_time'=>date_format(date_create($value->end_time), 'H:i'),
                'kegiatan'=>$value->kegiatan,
                'status'=> $value->status,
                'status_name'=> $value->status==1 ? 'Terkirim' : 'Belum Terkirim',
                'created_at'=> date_format(date_create($value->created_at), 'd-m-Y H:i:s'),
                'udpated_at'=> date_format(date_create($value->udpated_at), 'd-m-Y H:i:s'),
                'permission'=>[]
            );
            $data['jurnal'][$key]['permission']=[];
            if($value->status==0 && $value->kegiatan!='Clock In / Apel Pagi' && $roleName=='Taruna'){
                $data['jurnal'][$key]['permission']=['edit', 'delete'];
            }
        }
        $data['clock_out'] = $this->checkabsen($request);
        return $this->sendResponse($data, 'jurnal load successfully.');
        
    }

    public function getjurnaldetailbyid(Request $request)
    {
        #bugs
        $date       = $request->date;
        $id         = $request->id;
        $id_user    = $request->id_user;
        $data       = [] ;

        $getUser    = User::find($request->id_user);
        $roleName   = $getUser->getRoleNames()[0];
        $jurnal     = JurnalTaruna::whereRaw('tanggal = ?', $date)
                    ->where('jurnal_taruna.id', $id)
                    ->first();
        $jurnal->start_time = date_format(date_create($jurnal->start_time), 'H:i');
        $jurnal->end_time = date_format(date_create($jurnal->end_time), 'H:i');
        $grade  = Grade::where('id', $jurnal->grade)->first();
        if(!empty($grade)){
            $jurnal->grade_name = $grade->grade;
        }
        $jurnal->permission=[];
        if($jurnal->status==0 && $jurnal->kegiatan!='Clock In / Apel Pagi' && $roleName=='Taruna'){
            $jurnal->permission=['edit', 'delete'];
        }
        return $this->sendResponse($jurnal, 'jurnal load successfully.');
    }

    public function deletejurnal(Request $request)
    {
        $data = [];
        $id   = $request->id;
        $id_user = $request->idUser;
        $jurnal = JurnalTaruna::where('id', $id)->where('id_user', $id_user)->first();
        if(empty($jurnal)){
            return $this->sendResponseError($data, 'jurnal not found.');
        }
        $jurnal->delete();
        return $this->sendResponse($data, 'jurnal delete successfully.');
    }

    public function jurnaltaruna($condition, $limit, $order, $dir)
    {
        return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
        ->whereRaw($condition)
        ->select('jurnal_taruna.id_user', 'jurnal_taruna.tanggal','users.name', 'jurnal_taruna.status')
        ->limit($limit)
        ->orderBy($order,$dir)
        ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
        ->get();
    }

    public function getsuratizin(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'surat_header.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'surat_header.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        if(!empty($search)){
            $getUser = User::find($search);
            $id_user = $getUser->id;
        }
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        $data = [];
        if($order=='status'){
            $order='surat_header.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='surat_header.id';
        }

        $permission = [];
        foreach ($getUser->getAllPermissions() as $key => $vals) {
            $permission[]=$vals->name;
        }
        $listPermission = $this->setcategoryperizinan($permission);
        $getCategoryId = [];
        foreach ($listPermission as $key => $vals) {
            $getCategoryId[] = $vals['id'];
        }
        if($lastId==0){
            if($roleName=='Taruna'){
                $id = [];
                $orangtua   = OrangTua::where('taruna_id', $id_user)->get();
                if(!empty($orangtua)){
                    foreach ($orangtua as $key => $value) {
                        $id[]=$value->orangtua_id;
                    }
                }
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      =  SuratIzin::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                if(!empty($taruna)){
                    foreach ($taruna as $key => $value) {
                        $tarunaId[]=$value->taruna_id;
                    }
                }
                $tarunaId[] = $id_user;
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      = SuratIzin::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      = SuratIzin::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      = SuratIzin::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $taruna     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 7)
                                ->whereNull('users.deleted_at')
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      = SuratIzin::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }
        }else {
            if($roleName=='Taruna'){
                $id = [];
                $orangtua   = OrangTua::where('taruna_id', $id_user)->get();
                if(!empty($orangtua)){
                    foreach ($orangtua as $key => $value) {
                        $id[]=$value->orangtua_id;
                    }
                }
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'surat_header.id_user in('.$getTaruna.') AND surat_header.id '.$diff.' '.$lastId.'';
                $total      =  SuratIzin::whereRaw($condition)
                                ->count();  
                $count = SuratIzin::whereRaw($condition)->count();
                $data = $this->suratizintaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                if(!empty($taruna)){
                    foreach ($taruna as $key => $value) {
                        $tarunaId[]=$value->taruna_id;
                    }
                }
                $tarunaId[] = $id_user;
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'surat_header.id_user in('.$getTaruna.') AND surat_header.id '.$diff.' '.$lastId.'';
                $total = SuratIzin::whereRaw('surat_header.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = SuratIzin::whereRaw($condition)->count();
                $data = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'surat_header.id_user in('.$getTaruna.') AND surat_header.id '.$diff.' '.$lastId.'';
                $total = SuratIzin::whereRaw('surat_header.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = SuratIzin::whereRaw($condition)->count();
                $data = $this->suratizintaruna($condition, $limit, $order, $dir);
               

            }else if($roleName=='Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'surat_header.id_user in('.$getTaruna.') AND surat_header.id '.$diff.' '.$lastId.'';
                $total = SuratIzin::whereRaw('surat_header.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = SuratIzin::whereRaw($condition)->count();
                $data = $this->suratizintaruna($condition, $limit, $order, $dir);
               

            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $taruna     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 7)
                                ->whereNull('users.deleted_at')
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'surat_header.id_user in('.$getTaruna.') AND surat_header.id '.$diff.' '.$lastId.'';
                $total = SuratIzin::whereRaw('surat_header.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = SuratIzin::whereRaw($condition)->count();
                $data = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }
        }
        foreach ($data as $key => $value) {
            if($value->status==1){
                $status='Disetujui';
                $download = 'link_download';
            }else if ($value->status==0) {
                $status='Belum Disetuji';
                $download = '-';
            }else{
                $status='Tidak Disetuji';
                $download = '-';
            }
            $dataPermission = [];
            if(in_array($value->id_category, $getCategoryId)==true && $value->status_disposisi!=1 && $value->status!=1){
                if($value->user_created==$id_user && empty($search)){
                    $dataPermission = ['edit', 'delete'];
                }
            }
                $permissionApprove = $this->checkapprovepermission($value->id_category, $permission);
                if(!empty($permissionApprove)){
                    $dataPermission [] = $permissionApprove;
                }
            $result['suratizin'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'tanggal'=>$value->tanggal,
                'jenis_surat'=>$value->category,
                'id_category'=>$value->id_category,
                'status_name'=> $status,
                'status'=> $value->status,
                'download'=> $download,
                'permission'=>$dataPermission
            ];
                
        }

        $result['info']['permissionCreate'] = false;
        if($roleName=='Taruna' || $roleName=='Orang Tua'){
            $result['info']['permissionCreate'] = true;
        }

        if($count > $limit){
            $result['info']['lastId'] = $data[count($data)-1]->id;
            $result['info']['loadmore'] = true;
            $result['info']['dataload'] = count($data);
            $result['info']['totaldata'] = $total;
        }else{
            $result['info']['lastId'] = 0;
            $result['info']['loadmore'] = false;
            $result['info']['dataload'] = count($data);
            $result['info']['totaldata'] = $total;
        }
        $result['info']['limit']  = $limit;
        return $this->sendResponse($result, 'surat izin load successfully.');
    }

    public function suratizintaruna($condition, $limit, $order, $dir)
    {
        return SuratIzin::join('users', 'users.id', '=', 'surat_header.id_user')
            ->join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(surat_header.created_at))as tanggal"),'users.name', 'surat_header.status', 'menu_persetujuan.nama_menu as category', 'surat_header.id as id', 'surat_header.id_category as id_category', 'surat_header.status_disposisi', 'surat_header.status_level_1', 'surat_header.status_level_2', 'surat_header.id_user', 'surat_header.user_created')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }

    public function getsuratizincategory(Request $request)
    {
        $getUser = User::find($request->id_user);
        $permission = [];
        foreach ($getUser->getAllPermissions() as $key => $vals) {
            $permission[]=$vals->name;
        }
        $data = $this->setcategoryperizinan($permission);
        return $this->sendResponse($data, 'surat izin category load successfully.');
    }

    public function suratizindetailbyid(Request $request)
    {
        $id   = $request->id;
        $getSurat = SuratIzin::join('users as author', 'author.id', '=', 'surat_header.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'surat_header.user_approve_level_1')
                                    ->leftjoin('users as user_approve_2', 'user_approve_2.id', '=', 'surat_header.user_approve_level_2')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'surat_header.user_disposisi')
                                    ->select('surat_header.id as id', 
                                            'surat_header.id_user as id_user',
                                            'author.name as nama_taruna',
                                            'surat_header.photo as photo',
                                            'surat_header.id_category as id_category',
                                            'surat_header.status as status',
                                            'surat_header.start as start',
                                            'surat_header.end as end',
                                            'user_approve_1.name as user_approve_1',
                                            'surat_header.date_approve_level_1 as date_approve_1',
                                            'surat_header.reason_level_1 as user_reason_1',
                                            'surat_header.status_level_1 as status_level_1',
                                            'user_approve_2.name as user_approve_2',
                                            'surat_header.date_approve_level_2 as date_approve_2',
                                            'surat_header.reason_level_2 as user_reason_2',
                                            'surat_header.status_level_2 as status_level_2',
                                            'user_disposisi.name as user_disposisi',
                                            'surat_header.date_disposisi as date_disposisi',
                                            'surat_header.status_disposisi as status_disposisi',
                                            'surat_header.reason_disposisi as reason_disposisi',
                                            'surat_header.user_created as user_created'
                                            )
                                    ->where('surat_header.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseError($data, 'Surat Izin Not Found or Deleted');
        }
        $getCategory = Permission::where('id', $getSurat->id_category)->first();
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $author = User::find($getSurat->id_user);
        $permission = [];
        foreach ($getUser->getAllPermissions() as $key => $vals) {
            $permission[]=$vals->name;
        }
     
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }
        switch ($getSurat->id_category) {
            case 1:
                $getSuratDetail = IzinSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'keluhan'=>$getSuratDetail->keluhan,
                        'diagnosa'=>$getSuratDetail->diagnosa,
                        'rekomendasi'=>$getSuratDetail->rekomendasi,
                        'dokter'=>$getSuratDetail->dokter,
                        'permission'=>$this->checkapprovepermission(1, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keluhan', 'diagnosa', 'rekomendasi', 'dokter']
                    );
                }

                break;
            case 2:
                $getSuratDetail = KeluarKampus::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'pendamping'=>$getSuratDetail->pendamping,
                        'permission'=>$this->checkapprovepermission(2, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'pendamping']
                    );
                }
                break;
            case 3:
                $getSuratDetail = TrainingCenter::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'nm_tc'=>$getSuratDetail->nm_tc,
                        'pelatih'=>$getSuratDetail->pelatih,
                        'permission'=>$this->checkapprovepermission(3, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['nm_tc', 'pelatih']
                    );
                }
                break;
            case 4:
                $getSuratDetail = PernikahanSaudara::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'permission'=>$this->checkapprovepermission(4, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'tujuan']
                    );
                }
                break;
            case 5:
                $getSuratDetail = PemakamanKeluarga::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'permission'=>$this->checkapprovepermission(5, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'tujuan']
                    );
                }
                break;
            case 6:
                $getSuratDetail = OrangTuaSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'permission'=>$this->checkapprovepermission(6, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'tujuan']
                    );
                }
                break;
            case 7:
                $getSuratDetail = Tugas::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'permission'=>$this->checkapprovepermission(7, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'tujuan']
                    );
                }
                break;
            case 8:
            #bugs
                $getSuratDetail = KegiatanDalam::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'permission'=>$this->checkapprovepermission(8, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'tujuan']
                    );
                }
                break;
            case 9:
                $getSuratDetail = KegiatanPesiar::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id'=>$getSurat->id,
                        'id_user'=>$getSurat->id_user,
                        'name'=>$author->name,
                        'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/".$getSurat->photo : '',
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>$getCategory->nama_menu,
                        'status'=>$getSurat->status,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'permission'=>$this->checkapprovepermission(9, $permission),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'status_level_1' => $getSurat->status_level_1,
                        'user_reason_1' => $getSurat->user_reason_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'status_disposisi'=>$status_disposisi,
                        'reason_disposisi'=>$getSurat->reason_disposisi,
                        'show_disposisi' =>false,
                        'show_persetujuan' =>false,
                        'form'=>['keperluan', 'tujuan']
                    );
                }
                break;
            default:
                $getSuratDetail = [];
                break;
        }
        #bugs
        $data['menginap']='Izin Tidak Menginap';
        if(strtotime(date_format(date_create($getSurat->end), 'Y-m-d')) > strtotime(date_format(date_create($getSurat->start), 'Y-m-d'))){
            //if(in_array($data['id_category'], ['1', '4', '5', '6', '9'])){
                $data['user_approve_2']=$getSurat->user_approve_2;
                $data['date_approve_2']=$getSurat->date_approve_2;
                $data['status_level_2']=$getSurat->status_level_2;
                $data['user_reason_2']=$getSurat->user_reason_2;
                $data['menginap']='Izin Menginap';
                if(($roleName=='Direktur' || $roleName=='Super Admin') && $getSurat->status!=1 && $data['status_level_1']==1){
                    $data['show_persetujuan'] = true;
                }
            //}
        }
        if($roleName=='Pembina' && $data['status_level_1']!=1 && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }
        $data['permission'] = [];
        if(($roleName=='Taruna' || $roleName=='Orang Tua') && $getSurat->status_disposisi!=1 && $getSurat->status!=1) {
            if($getSurat->user_created==$request->id_user){
                $data['permission'] = ['edit', 'delete'];
            }
        }
        if(($roleName=='Akademik dan Ketarunaan' || $roleName=='Super Admin') && $getSurat->status!=1 && $data['status_level_1']!=1 && $getSurat->status_disposisi==1){
            $data['show_persetujuan'] = true;
        }
        $data['download'] = false;
        if($getSurat['status']==1 && ($roleName=='Orang Tua' || $roleName=='Taruna')){
            $data['download'] = \URL::to('/').'/api/cetaksurat/id/'.$request->id.'/id_user/'.$request->id_user.'/cetak/perizinan';
        }
        $data['start'] = date('Y-m-d H:i', strtotime($getSurat->start));
        $data['end'] = date('Y-m-d H:i', strtotime($getSurat->end));
        $data['start_bi'] = date('d-m-Y H:i', strtotime($getSurat->start));
        $data['start_date'] = date('d/m/Y', strtotime($getSurat->start));
        $data['end_bi'] = date('d-m-Y H:i', strtotime($getSurat->end));
        $data['end_date'] = date('d/m/Y', strtotime($getSurat->end));
      /*   asort($data);
        $sortData = [];
        foreach ($data as $key => $value) {
            $sortData[$key]=$value;
        } */
        return $this->sendResponse($data, 'surat izin detail load successfully.');
    }

    public function inputsuratizin(Request $request)
    {
        if(!empty($request->id)){
            return $this->updatesuratizin($request);
        }else {
            return $this->savesuratizin($request);
        }
    }

    public function savesuratizin($request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'start' => 'required',
            'end' => 'required',
            'id_category' =>'required',
            'keluhan'=>'required_if:id_category,1',
            'keperluan'=>'required_if:id_category,2|required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'tujuan'=>'required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'pendamping'=>'required_if:id_category,2',
            'pelatih'=>'required_if:id_category,3',
            'nm_tc'=>'required_if:id_category,3',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $image=false;

        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath'));
            if($image==false){
                return $this->sendResponseError($data, 'failed upload');  
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
                Arr::forget($input, array('file', 'start_time', 'end_time', 'keluhan', 'diagnosa', 'rekomendasi', 'dokter', 'pendamping', 'keperluan', 'tujuan', 'nm_tc', 'pelatih'));
                $getUser = User::where('id', $request->id_user)->first();

                if($getUser->getRoleNames()[0]!='Taruna' && $getUser->getRoleNames()[0]!='Orang Tua'){
                    $input['start'] = date('Y-m-d H:i:s', strtotime($request->start));
                    $input['end']   = date('Y-m-d H:i:s', strtotime($request->end));
                    $input['status'] = 1;
                    $input['status_disposisi'] = 1;
                    $input['status_level_1'] = 1;
                    $input['status_level_2'] = 1;
                    $input['reason_level_1'] = 'Surat izin dibuatkan superadmin';
                    $input['reason_level_2'] = 'Surat izin dibuatkan superadmin';
                    $input['reason_disposisi'] = 'Surat izin dibuatkan superadmin';
                    $input['user_disposisi'] = $request->id_user;
                    $input['user_approve_level_1'] = $request->id_user;
                    $input['user_approve_level_2'] = $request->id_user;
                    $input['date_approve_level_1'] = date('Y-m-d H:i:s');
                    $input['date_approve_level_2'] = date('Y-m-d H:i:s');
                    $input['date_disposisi'] = date('Y-m-d H:i:s');
                    $input['grade'] = $getUser->grade;
                }else{    
                    if($getUser->getRoleNames()[0]=='Orang Tua'){
                        $taruna     = OrangTua::where('orangtua_id', $id_user)->first(); 
                        if(empty($taruna)){
                            return $this->sendResponseError($data, 'taruna tidak ditemukan');  
                        }
                        $getUser    = User::where('id', $taruna->taruna_id)->first();
                    }
                    $input['status'] = 0;
                    $input['status_level_1'] = 0;
                    $input['status_level_2'] = 0;
                    $input['grade'] = $getUser->grade;
                    $input['id_user'] = $getUser->id;
                    $input['start'] = date('Y-m-d H:i:s', strtotime($request->start));
                    $input['end']   = date('Y-m-d H:i:s', strtotime($request->end));
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
                                'pelatih'=>$request->pelatih,
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
            $data['firebase'] = false;
            $firebase = [];
            $keluarga = User::keluargataruna($getUser->id);
            $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$getUser->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic = User::topic('createsurat', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan perizinan baru',
                    'body'=>'perizinan baru telah dibuat',
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
            
            $data['status'] = true;
            $data['firebase'] = $firebase;
            return $this->sendResponse($data, 'surat izin create successfully.');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
            if($image!=false){
                $this->DeleteImage($image, config('app.documentImagePath'));
            }
            $data['status'] = false;
            return $this->sendResponseError($data, 'surat izin create failure.');
        }

    }

    public function updatesuratizin($request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'start' => 'required',
            'end' => 'required',
            'id_category' =>'required',
            'keluhan'=>'required_if:id_category,1',
            'keperluan'=>'required_if:id_category,2|required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'tujuan'=>'required_if:id_category,4|required_if:id_category,5|required_if:id_category,6|required_if:id_category,7|required_if:id_category,8|required_if:id_category,9',
            'pendamping'=>'required_if:id_category,2',
            'pelatih'=>'required_if:id_category,3',
            'nm_tc'=>'required_if:id_category,3',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data['status']=false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $image=false;
        if(!empty($request->file)){
            $image = $this->UploadImage($request->file, config('app.documentImagePath'));
            if($image==false){
                return $this->sendResponseError($data, 'failed upload');  
            }
        }
        $id_user = $request->id_user;
        try {
            DB::beginTransaction();
            $suratIzin = SuratIzin::where('id_user', $request->id_user)->where('id', $request->id)->first();

                if(!empty($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($suratIzin->photo, config('app.documentImagePath'));
                    }
                }
                $request->request->add(['user_updated'=> $request->id_user]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('file', 'start_time', 'end_time', 'keluhan', 'diagnosa', 'rekomendasi', 'dokter', 'pendamping', 'keperluan', 'tujuan', 'nm_tc', 'pelatih'));
                $getUser = User::where('id', $request->id_user)->first();
                if($getUser->getRoleNames()[0]!='Taruna' && $getUser->getRoleNames()[0]!='Orang Tua'){
                    $input['start'] = $request->start.' '.$request->start_time;
                    $input['end']   = $request->end.' '.$request->end_time;
                    $input['status'] = 1;
                    $input['status_disposisi'] = 1;
                    $input['status_level_1'] = 1;
                    $input['status_level_2'] = 1;
                    $input['reason_level_1'] = 'Surat izin dibuatkan superadmin';
                    $input['reason_level_2'] = 'Surat izin dibuatkan superadmin';
                    $input['reason_disposisi'] = 'Surat izin dibuatkan superadmin';
                    $input['user_disposisi'] = $request->id_user;
                    $input['user_approve_level_1'] = $request->id_user;
                    $input['user_approve_level_2'] = $request->id_user;
                    $input['date_approve_level_1'] = date('Y-m-d H:i:s');
                    $input['date_approve_level_2'] = date('Y-m-d H:i:s');
                    $input['date_disposisi'] = date('Y-m-d H:i:s');
            
                }else{   
                    if($getUser->getRoleNames()[0]=='Orang Tua'){
                        $taruna     = OrangTua::where('orangtua_id', $id_user)->first(); 
                        if(empty($taruna)){
                            return $this->sendResponseError($data, 'taruna tidak ditemukan');  
                        }
                        $getUser    = User::where('id', $taruna->taruna_id)->first();
                    }  
                    $input['status'] = $suratIzin->status;
                    $input['status_level_1'] = $suratIzin->status_level_1;
                    $input['status_level_2'] = $suratIzin->status_level_2;
                    $input['status_disposisi'] = 0;
                    $input['grade'] = $getUser->grade;
                    $input['id_user'] = $getUser->id;
                    $input['start'] = date('Y-m-d H:i:s', strtotime($request->start));
                    $input['end']   = date('Y-m-d H:i:s', strtotime($request->end));
               
                }
                
                $suratIzin->update($input);
               
                if($request->id_category==1){
                    $table = IzinSakit::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    
                    $dataDetail=[
                                 'keluhan'=>$request->keluhan,
                                 'diagnosa'=>$request->diagnosa,
                                 'rekomendasi'=>$request->rekomendasi,
                                 'dokter'=>$request->dokter,
                                 'status'=>$input['status'],
                                 'user_updated'=>$input['user_updated'],
                                 'updated_at'=>$input['updated_at']
                                 
                                 
                                ];
                    $table->update($dataDetail);
                }
                if($request->id_category==2){
                    $table = KeluarKampus::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                                 'keperluan'=>$request->keperluan,
                                 'pendamping'=>$request->pendamping,
                                 'status'=>$input['status'],
                                 'user_updated'=>$input['user_updated'],
                                 'updated_at'=>$input['updated_at']
                                 
                                ];
                    $table->update($dataDetail);
                }
                if($request->id_category==3){
                    $table = TrainingCenter::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                                'training'=>$request->training,
                                'pelatih'=>$request->pelatih,
                                 'nm_tc'=>$request->nm_tc,
                                 'status'=>$input['status'],
                                 'user_updated'=>$input['user_updated'],
                                 'updated_at'=>$input['updated_at']
                                 
                                 
                                ];
                    $table->update($dataDetail);
                }
                if($request->id_category==4){
                    $table = PernikahanSaudara::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                        
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_updated'=>$input['user_updated'],
                        'updated_at'=>$input['updated_at']
                        
                        
                    ];
                    $table->update($dataDetail);
                }
                if($request->id_category==5){
                    $table = PemakamanKeluarga::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                        
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_updated'=>$input['user_updated'],
                        'updated_at'=>$input['updated_at']
                        
                        
                    ];
                    $table->update($dataDetail);
                }
                if($request->id_category==6){
                    $table = PemakamanKeluarga::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                        
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_updated'=>$input['user_updated'],
                        'updated_at'=>$input['updated_at']
                        
                        
                    ];
                    $table->update($dataDetail);
                }
                if($request->id_category==7){
                    $table = Tugas::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                        
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_updated'=>$input['user_updated'],
                        'updated_at'=>$input['updated_at']
                        
                        
                    ];
                    $table->update($dataDetail);
                }
                if($request->id_category==8){
                    $table = KegiatanDalam::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                        
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_updated'=>$input['user_updated'],
                        'updated_at'=>$input['updated_at']
                        
                        
                    ];
                    $table->update($dataDetail);
                }
                if($request->id_category==9){
                    $table = KegiatanPesiar::where('id_surat', $request->id)->where('id_user', $request->id_user)->first();
                    $dataDetail=[
                        
                        'keperluan'=>$request->keperluan,
                        'tujuan'=>$request->tujuan,
                        'status'=>$input['status'],
                        'user_updated'=>$input['user_updated'],
                        'updated_at'=>$input['updated_at']
                        
                        
                    ];
                    $table->update($dataDetail);
                }
            $data['firebase'] = false;
            $firebase = [];
            $keluarga = User::keluargataruna($getUser->id);
            $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$getUser->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic = User::topic('createsurat', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan perizinan baru',
                    'body'=>'perizinan baru telah dibuat',
                    'page'=>'/riwayat-izin/detail/id/'.$request->id,
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
            DB::commit();
            $data['status'] = true;
            $data['firebase'] = $firebase;
            return $this->sendResponse($data, 'surat izin update successfully.');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath'));
                }
            $data['status'] = false;
            return $this->sendResponseError($data, 'surat izin update failure.');
        }
    }

    public function deletesuratizin(Request $request)
    {
        $suratIzin = SuratIzin::where('id_user', $request->id_user)->where('id', $request->id)->where('status', 0)->first();
        if(!empty($suratIzin)){
            try {
                DB::beginTransaction();
                   // $this->DeleteImage($suratIzin->photo, config('app.documentImagePath'));
                    $suratIzin->user_deleted = $request->id_user;
                    $suratIzin->save();
                    $suratIzin->delete();
                    switch ($suratIzin->id_category) {
                        case 1:
                            $getSuratDetail = IzinSakit::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 2:
                            $getSuratDetail = KeluarKampus::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 3:
                            $getSuratDetail = TrainingCenter::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 4:
                            $getSuratDetail = PernikahanSaudara::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 5:
                            $getSuratDetail = PemakamanKeluarga::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 6:
                            $getSuratDetail = OrangTuaSakit::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 7:
                            $getSuratDetail = Tugas::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 8:
                            $getSuratDetail = KegiatanDalam::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        case 9:
                            $getSuratDetail = KegiatanPesiar::where('id_surat', $request->id)->where('id_user', $suratIzin->id_user)->first();
                            break;
                        default:
                            $getSuratDetail = [];
                            break;
                    }
                    if(!empty($getSuratDetail)){
                        $getSuratDetail->user_deleted = $request->id_user;
                        $getSuratDetail->save();
                        $getSuratDetail->delete();
                    }
                DB::commit();
                $data['status'] = true;
                return $this->sendResponse($data, 'surat izin delete successfully.');
            } catch (\Throwable $th) {
                DB::rollback();
                $data['status'] = false;
                return $this->sendResponseError($data, 'surat izin delete failure.');
            }
            $data['status'] = false;
            return $this->sendResponseError($data, 'surat izin delete failure.');
        }
    }

    public function disposisisuratizin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $suratIzin = SuratIzin::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $suratIzin->user_disposisi=$request->id_user;
        $suratIzin->date_disposisi=date('Y-m-d H:i:s');
        $suratIzin->reason_disposisi=$request->reason;
        $suratIzin->status_disposisi=$request->status;
        $suratIzin->save();

        $data['status'] = true;
        $data['firebase'] = false;

        $keluarga       = User::keluargataruna($suratIzin->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$suratIzin->id_user, 'keluarga_asuh'=>$keluarga_asuh];
        
        $topic = User::topic('disposisisurat', $dataFirebase);
        if(!empty($topic)){
            set_time_limit(60);
            for ($i=0; $i < count($topic); $i++) { 
                $paramsFirebase=['title'=>'Pemberitahuan disposisi perizinan baru',
                'body'=>'perizinan baru telah diposisi',
                'page'=>'/riwayat-izin/detail/id/'.$request->id,
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

        return $this->sendResponse($data, 'disposisi surat izin success');
    }

    public function approvesuratizin(Request $request)
    {
        #categorytugas
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $suratIzin = SuratIzin::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();
        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan'){
            $suratIzin->user_approve_level_1=$request->id_user;
            $suratIzin->date_approve_level_1=date('Y-m-d H:i:s');
            $suratIzin->status_level_1=$request->status;
            $suratIzin->reason_level_1=$request->reason;

            $keluarga       = User::keluargataruna($suratIzin->id_user);
            $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
            $dataFirebase   = [];
            $dataFirebase   = ['id'=>$suratIzin->id_user, 'keluarga_asuh'=>$keluarga_asuh];

            if(strtotime(date_format(date_create($suratIzin->end), 'Y-m-d')) == strtotime(date_format(date_create($suratIzin->start), 'Y-m-d'))){
                $suratIzin->status=$request->status;
                $suratIzin->save();
                $topic  = User::topic('approve-direktur', $dataFirebase);
                if(!empty($topic)){
                    set_time_limit(60);
                    for ($i=0; $i < count($topic); $i++) { 
                        $paramsFirebase=['title'=>'Pemberitahuan persetujuan perizinan baru',
                        'body'=>'perizinan baru telah disetuji aak',
                        'page'=>'/riwayat-izin/detail/id/'.$request->id,
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
            }else{
                $topic  = User::topic('approve-aak', $dataFirebase);
                if(!empty($topic)){
                    set_time_limit(60);
                    for ($i=0; $i < count($topic); $i++) { 
                        $paramsFirebase=['title'=>'Pemberitahuan persetujuan perizinan baru',
                        'body'=>'perizinan baru telah disetuji oleh aak',
                        'page'=>'/riwayat-izin/detail/id/'.$request->id,
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

            }
            $suratIzin->save();
        }
        if($getUser->getRoleNames()[0]=='Direktur' || $getUser->getRoleNames()[0]=='Super Admin'){
            $suratIzin->user_approve_level_2=$request->id_user;
            $suratIzin->date_approve_level_2=date('Y-m-d H:i:s');
            $suratIzin->status_level_2=$request->status;
            $suratIzin->reason_level_2=$request->reason;
            $suratIzin->status=$request->status;
            $suratIzin->save();
        }
        $data['status'] = true;
        $data['firebase'] = false;
        $topic  = User::topic('approve-direktur', $dataFirebase);
                    if(!empty($topic)){
                        set_time_limit(60);
                        for ($i=0; $i < count($topic); $i++) { 
                            $paramsFirebase=['title'=>'Pemberitahuan persetujuan perizinan baru',
                            'body'=>'perizinan baru telah disetuji direktur',
                            'page'=>'/riwayat-izin/detail/id/'.$request->id,
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
        return $this->sendResponse($data, 'approve surat izin success');
    }

    public function gettaruna(Request $request)
    {
        $getUser    = User::find($request->id_user);
        $id_user    = $request->id_user; 
        $roleName   = $getUser->getRoleNames()[0];
        if($roleName=='Taruna'){
            $tarunaId   = [];
            //$orangtua   = OrangTua::where('taruna_id', )->get();
            $tarunaData['taruna'] = ['id'=>$getUser->id, 'name'=>$getUser->name];
            return $tarunaData;
        }else if($roleName=='OrangTua'){
            $taruna         = OrangTua::join('users', 'users.id', '=', 'orang_tua_taruna.taruna_id')
                                ->select('orang_tua_taruna.taruna_id', 'users.name')
                                ->where('orangtua_id', $id_user)
                                ->get();
            $tarunaId       = [];
            $tarunaData     = [];
            foreach ($taruna as $key => $value) {
                $tarunaId[]=$value->taruna_id;
                $tarunaWithName[]=['id'=>$value->taruna_id, 'name'=>$value->name];
            }
            //$tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            //return $tarunaData;
        }else if($roleName=='Wali Asuh'){
            $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->join('users', 'users.id', '=', 'taruna_keluarga_asuh.taruna_id')
                            ->select('taruna_keluarga_asuh.taruna_id', 'users.name')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                            ->get();

            $tarunaId       = [];
            $tarunaData     = [];
            foreach ($taruna as $key => $value) {
                $tarunaId[]=$value->taruna_id;
                $tarunaWithName[]=['id'=>$value->taruna_id, 'name'=>$value->name];
            }
            //$tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            //return $tarunaData;
        }else if($roleName=='Pembina'){
            $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->join('users', 'users.id', '=', 'taruna_keluarga_asuh.taruna_id')
                            ->select('taruna_keluarga_asuh.taruna_id', 'users.name')
                            ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                            ->get();
  
            $tarunaId       = [];
            $tarunaData     = [];
            foreach ($taruna as $key => $value) {
                $tarunaId[]=$value->taruna_id;
                $tarunaWithName[]=['id'=>$value->taruna_id, 'name'=>$value->name];
            }
            //$tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            //return $tarunaData;
        }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
            $taruna     = DB::table('users')
                            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                            ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                            ->select('users.id', 'users.name')
                            ->where('model_has_roles.role_id', 7)
                            ->whereNull('users.deleted_at')
                            ->get();

            $tarunaId       = [];
            $tarunaData     = [];
            foreach ($taruna as $key => $value) {
                $tarunaId[]=$value->id;
                $tarunaWithName[]=['id'=>$value->id, 'name'=>$value->name];
            }
            //$tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
        }
        /* if($option!=1){
            //$tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
        }else{ */
            $tarunaData['taruna']   = $tarunaWithName;
        //}
        return $tarunaData;
    }

    public function setcategoryperizinan($permission)
    {
        $category=[];
        $data=[];
        if (in_array('surat-izin-orang-tua-sakit-create', $permission)) {
            $category[]=['id'=>6, 
                        'name'=>'Orang Tua Sakit', 
                        'form'=>['keperluan', 'tujuan']
                        ];
        }
        if (in_array('surat-izin-orang-tua-meninggal-create', $permission)) {
            $category[]=['id'=>5, 
                        'name'=>'Pemakaman Keluarga',
                        'form'=>['keperluan', 'tujuan']];
        }
        if (in_array('surat-izin-pernikahan-saudara-create', $permission)) {
            $category[]=['id'=>4, 
                        'name'=>'Pernikahan Saudara',
                        'form'=>['keperluan', 'tujuan']];
        }
        if (in_array('surat-izin-kegiatan-pesiar-create', $permission)) {
            $category[]=['id'=>9, 
                        'name'=>'Kegiatan Pesiar',
                        'form'=>['keperluan', 'tujuan']];
        }
        if (in_array('surat-izin-rawat-inap-create', $permission)) {
            $category[]=['id'=>1, 
                          'name'=>'Izin Sakit', 
                          'form'=>['keluhan', 'diagnosa', 'rekomendasi', 'dokter']
                        ];
        }
        if (in_array('surat-izin-training-create', $permission)) {
            $category[]=['id'=>3, 
                        'name'=>'Training Center',
                        'form'=>['nm_tc', 'pelatih']];
        }
        if (in_array('surat-izin-keluar-kampus-create', $permission)) {
            $category[]=['id'=>2, 
                         'name'=>'Keluar Kampus',
                         'form'=>['keperluan', 'pendamping']
                        ];
        }
        if (in_array('surat-izin-kegiatan-dalam-create', $permission)) {
            $category[]=['id'=>8, 
                        'name'=>'Kegiatan Dalam',
                        'form'=>['keperluan', 'tujuan']];
        }
        if (in_array('surat-tugas-create', $permission)) {
            $category[]=['id'=>7, 
                        'name'=>'Tugas',
                        'form'=>['keperluan', 'tujuan']];
        }
        asort($category);
        foreach ($category as $key => $value) {
            $data[]=$value;
        }
        return $data;
    }

    public function checkapprovepermission($id, $permission){
        $data = '';
        if (in_array('surat-izin-orang-tua-sakit-approve', $permission) && $id==6) {
            $data = 'approve';
        }
        if (in_array('surat-izin-orang-tua-meninggal-approve', $permission) && $id==5) {
            $data = 'approve';
        }
        if (in_array('surat-izin-pernikahan-saudara-approve', $permission) && $id==4) {
            $data = 'approve';
        }
        if (in_array('surat-izin-kegiatan-pesiar-approve', $permission) && $id==9) {
            $data = 'approve';
        }
        if (in_array('surat-izin-rawat-inap-approve', $permission) && $id==1) {
            $data = 'approve';
        }
        if (in_array('surat-izin-training-approve', $permission) && $id==3) {
            $data = 'approve';
        }
        if (in_array('surat-izin-keluar-kampus-approve', $permission) && $id==2) {
            $data = 'approve';
        }
        if (in_array('surat-izin-kegiatan-dalam-approve', $permission) && $id==8) {
            $data = 'approve';
        }
        if (in_array('surat-tugas-approve', $permission) && $id==7) {
            $data = 'approve';
        }

        return $data;
    }

    public function cetaksurat(Request $request){
        $data   = ['id'=>$request->id, 'id_user'=>$request->id_user, 'cetak'=>$request->cetak];
        $res    = [];
        $data   = SuratIzin::tmpreport($data);
        
        $res['link'] = $data; 
        $res['status'] = true;
        return $this->sendResponse($res, 'link surat generate');
    }
/* 
    public function cetaksurat(Request $request){
        $data   = [];
        $res    = [];
        $data   = $this->datasuratizin($request);
        if(!empty($data)){
            $pdf = app()->make('dompdf.wrapper');
            $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
            $content = $pdf->download()->getOriginalContent();
            $name = \Str::slug($data['category_name'].'-'.$data['name'].'-'.date('dmyhis')).".pdf";
            Storage::put('public/'.config('app.documentImagePath').'/temp/'.$name, $content) ;
           
            //\Storage::put(config('app.documentImagePath').$name, $pdf->output());
            //$data->storeAs('public/'.config('app.documentImagePath'), $file_name);
            $link =  \URL::to('/').'/storage/'.config('app.documentImagePath').'/temp/'.$name;
            //return $pdf->stream();
        }

        $res['link'] = $link; 
        $res['status'] = true;
        return $this->sendResponse($res, 'link surat generate');
        
           return $this->sendResponse($res, 'link surat generate failure');
    } */

    public function triggercetak(Request $request){
        return view('triggercetak');
    }

    public function datasuratizin($request)
    {
        $id   = $request->id;
        $getSurat = SuratIzin::join('users as author', 'author.id', '=', 'surat_header.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'surat_header.user_approve_level_1')
                                    ->leftjoin('users as user_approve_2', 'user_approve_2.id', '=', 'surat_header.user_approve_level_2')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'surat_header.user_disposisi')
                                    ->select('surat_header.id as id', 
                                            'surat_header.id_user as id_user',
                                            'author.name as nama_taruna',
                                            'surat_header.photo as photo',
                                            'surat_header.id_category as id_category',
                                            'surat_header.status as status',
                                            'surat_header.start as start',
                                            'surat_header.end as end',
                                            'user_approve_1.name as user_approve_1',
                                            'surat_header.date_approve_level_1 as date_approve_1',
                                            'surat_header.reason_level_1 as user_reason_1',
                                            'user_approve_2.name as user_approve_2',
                                            'surat_header.date_approve_level_2 as date_approve_2',
                                            'surat_header.reason_level_2 as user_reason_2',
                                            'user_disposisi.name as user_disposisi',
                                            'surat_header.date_disposisi as date_disposisi',
                                            'surat_header.status_disposisi as status_disposisi',
                                            'surat_header.reason_disposisi as reason_disposisi'
                                            )
                                    ->where('surat_header.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $data;
        }
        $getCategory = Permission::where('id', $getSurat->id_category)->first();
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $author = User::find($getSurat->id_user);
        $permission = [];
        foreach ($getUser->getAllPermissions() as $key => $vals) {
            $permission[]=$vals->name;
        }
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }
        switch ($getSurat->id_category) {
            case 1:
                $getSuratDetail = IzinSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id_category'=>$getSurat->id_category,
                        'name'=>$author->name,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['No', 'Nama', 'STB', 'Keluhan', 'Diagnosa', 'Rekomendasi', 'Dokter', 'Tanggal'],
                        'body'=>['1', $author->name, $author->stb, $getSuratDetail->keluhan, $getSuratDetail->diagnosa, 
                                    $getSuratDetail->rekomendasi, $getSuratDetail->dokter, date_format(date_create($getSurat->start), 'd-m-Y H:i').' sd '.date_format(date_create($getSurat->end), 'd-m-Y H:i')],
                        'template'=>1
                    );
                }

                break;
            case 2:
                $getSuratDetail = KeluarKampus::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'status'=>$getSurat->status,
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['No', 'Nama', 'STB', 'Keperluan', 'Jam Mulai', 'Jam Akhir', 'Pendamping', 'Tanggal'],
                        'body'=>['1', $author->name, $author->stb, $getSuratDetail->keperluan, date_format(date_create($getSurat->start), 'H:i'), date_format(date_create($getSurat->end), 'H:i'), $getSuratDetail->pendamping, 
                                   date_format(date_create($getSurat->created_at), 'd-m-Y H:i')],
                        'template'=>1
                    );
                }
                break;
            case 3:
                $getSuratDetail = TrainingCenter::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'status'=>$getSurat->status,
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['No', 'Nama', 'STB', 'Training Center', 'Jam Mulai', 'Jam Akhir', 'Pelatih', 'Tanggal'],
                        'body'=>['1', $author->name, $author->stb, $getSuratDetail->nm_tc, date_format(date_create($getSurat->start), 'H:i'), date_format(date_create($getSurat->end), 'H:i'), $getSuratDetail->pelatih, 
                                   date_format(date_create($getSurat->created_at), 'd-m-Y H:i')],
                        'template'=>1
                     
                    );
                }
                break;
            case 4:
                $getSuratDetail = PernikahanSaudara::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->Keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
                        'template'=>2,
                        'id_surat_cetak'=>$getSurat->id+1
                    );
                }
                break;
            case 5:
                $getSuratDetail = PemakamanKeluarga::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->Keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
                        'template'=>2,
                        'id_surat_cetak'=>$getSurat->id+1
                    );
                }
                break;
            case 6:
                $getSuratDetail = OrangTuaSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->Keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
                        'template'=>2,
                        'id_surat_cetak'=>$getSurat->id+1
                    );
                }
                break;
            case 7:
                $getSuratDetail = Tugas::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'STB', 'Keperluan', 'Tujuan', 'Mulai', 'Akhir', 'Tanggal Pengajuan'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y H:i'), date_format(date_create($getSurat->end), 'd-m-Y H:i'), $getSurat->created_at],
                        'template'=>1
                    );
                }
                break;
            case 8:
                $getSuratDetail = KegiatanDalam::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'STB', 'Keperluan', 'Tujuan', 'Mulai', 'Akhir', 'Tanggal Pengajuan'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y H:i'), date_format(date_create($getSurat->end), 'd-m-Y H:i'), $getSurat->created_at],
                        'template'=>1
                    );
                }
                break;
            case 9:
                $getSuratDetail = KegiatanPesiar::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'STB', 'Keperluan', 'Tujuan', 'Mulai', 'Akhir', 'Tanggal Pengajuan'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y H:i'), date_format(date_create($getSurat->end), 'd-m-Y H:i'), $getSurat->created_at],
                        'template'=>1
         
                    );
                }
                break;
            default:
                $getSuratDetail = [];
                break;
        }
        if(strtotime(date_format(date_create($getSurat->end), 'Y-m-d')) > strtotime(date_format(date_create($getSurat->start), 'Y-m-d'))){
            if(in_array($data['id_category'], ['1', '4', '5', '6', '9'])){
                $data['user_approve_2']=$getSurat->user_approve_2;
                $data['date_approve_2']=$getSurat->date_approve_2;
                $data['user_reason_2']=$getSurat->user_reason_2;
                $data['menginap']='Izin Menginap';
                if($roleName=='Direktur' && $data['status']!=1 && $data['status_level_1']==1){
                    $data['show_persetujuan'] = true;
                }
            }
        }
        return $data;
    }

    public function settoken(Request $request)
    {
        $data = [];
        $data['id_user'] = $request->id_user;
        $data['token'] = $request->fcm_token;
        $set =  $this->saveToken($data);
        if($set==true){
            return $this->sendResponse(true, 'success set token');
        }else{
            return $this->sendResponseError(false, 'failure set token');
        }
        
    }
    
}
