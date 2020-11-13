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
use App\WaliAsuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\Provinces;
use App\Regencies;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Absensi;
use App\JurnalTaruna;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
class LookController extends BaseController
{
    use ImageTrait;
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
        $user = User::where('id', $request->id)->first();
        $user->photo = url('/')."/storage/".config('app.userImagePath')."/".$user->photo;
        $data=[];
        $data['user'] = $user;
        $data['select_grade'] = Grade::where('id', $user->grade)->pluck('grade','id')->all();
        $data['select_provinces'] = Provinces::where('id', $user->province_id)->pluck('name','id')->all();
        $data['select_regencies'] = Regencies::where('id', $user->regencie_id)->pluck('name','id')->all();
        $data['grade'] = Grade::pluck('grade', 'id')->all();
        $data['provinces'] = Provinces::pluck('name','id')->all();
        $data['regencies'] = Regencies::where('province_id', $user->province_id)->pluck('name','id')->all();
        return $this->sendResponse($data, 'profile load successfully.');

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

    public function setprofile(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $data['data']=[];
        $data['data']['user'] = $user;
        $data['data']['all_grade'] = Grade::pluck('grade', 'id')->all();
        $data['data']['all_provinces'] = Provinces::pluck('name','id')->all();
        $data['data']['all_regencies'] = Regencies::pluck('name','id')->all();
        $data['data']['select_regencies'] = Regencies::where('province_id', $user->province_id)->pluck('name','id')->all();
        return $this->sendResponse($data, 'profile load successfully.');

    }

    public function clockin(Request $request)
    {
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
            $file = $this->UploadImage($request->file_clock_in, config('app.documentImagePath'));
            if($file!=false){
                try {
                    DB::beginTransaction();
                    $getUser = User::where('id', $request->id_user)->first();
                    $absensi = new Absensi();
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
        $validator = Validator::make($request->all(), 
                    [ 
                        'id_user' => 'required',
                        'file_clock_out' => 'required|mimes:jpg,jpeg,png|max:2048',
                    ]);   
 
        if ($validator->fails()) {          
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                        
        }  
        if ($files = $request->file('file_clock_out')) {
            $file = $this->UploadImage($request->file_clock_out, config('app.documentImagePath'));
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
                    $jurnal->kegiatan = 'Clock Out / Apel Malam';
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
                //$input['updated_at'] = date('Y-m-d h:i:s');
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
                //$input['created_at'] = date('Y-m-d h:i:s');
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
        $condition = 'jurnal_taruna.tanggal='.$lastDate.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result = [];
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
            }else if($roleName=='OrangTua'){
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
                    $tarunaId[]=$value->taruna_id;
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
                $condition = 'jurnal_taruna.id_user='.$id_user.' AND jurnal_taruna.tanggal < \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw($condition)
                            ->count(DB::raw('DISTINCT tanggal')); 
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);
                $result['info']['permission'] = ['create', 'delete', 'edit'];
            }else if($roleName=='OrangTua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal < \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw('jurnal_taruna.id_user in('.$getTaruna.')')
                        ->count(DB::raw('DISTINCT tanggal'));
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);
            }else if('Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal < \''.$lastDate.'\'';
                $total = JurnalTaruna::whereRaw('jurnal_taruna.id_user in('.$getTaruna.')')
                        ->count(DB::raw('DISTINCT tanggal'));
                
                $count = JurnalTaruna::whereRaw($condition)->count(DB::raw('DISTINCT tanggal'));
                $data = $this->jurnaltaruna($condition, $limit, $order, $dir);

            }else if('Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal < \''.$lastDate.'\'';
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
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'jurnal_taruna.id_user in('.$getTaruna.') AND jurnal_taruna.tanggal < \''.$lastDate.'\'';
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
                    'status'=> $value->status==1 ? 'Terkirim' : 'Belum Terkirim'
                ];
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
        $data = [];
        $date = $request->date;
        $id_user =$request->id_user;
        $jurnal = JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                    ->join('grade_table', 'grade_table.id', '=', 'jurnal_taruna.grade')
                    ->whereRaw('jurnal_taruna.tanggal = ?', $date)
                    ->where('users.id', $id_user)
                    ->select('jurnal_taruna.*', 'users.name as nama_taruna', 'grade_table.grade as nama_grade')
                    ->get();
        foreach ($jurnal as $key => $value) {
            $data[]=array(
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
                'udpated_at'=> date_format(date_create($value->udpated_at), 'd-m-Y H:i:s')

            );
        }
        return $this->sendResponse($data, 'jurnal load successfully.');
        
    }

    public function getjurnaldetailbyid(Request $request)
    {
        $date = $request->date;
        $id   = $request->id;
        $id_user =$request->id_user;
        $jurnal = JurnalTaruna::whereRaw('tanggal = ?', $date)
                    ->where('jurnal_taruna.id', $id)
                    ->where('jurnal_taruna.id_user', $id_user)
                    ->first();
        $jurnal->start_time = date_format(date_create($jurnal->start_time), 'H:i');
        $jurnal->end_time = date_format(date_create($jurnal->end_time), 'H:i');
        return $this->sendResponse($jurnal, 'jurnal load successfully.');
    }

    public function deletejurnal(Request $request)
    {
        $data = [];
        $id   = $request->id;
        $id_user = $request->idUser;
        $jurnal = JurnalTaruna::where('id', $id)->where('id_user', $id_user)->first();
        if(empty($jurnal)){
            return $this->sendResponseFalse($data, 'jurnal not found.');
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
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
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
                foreach ($orangtua as $key => $value) {
                    $id[]=$value->orangtua_id;
                }
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      =  SuratIzin::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
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
                $total      = SuratIziin::whereRaw($condition)
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
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'surat_header.id_user in('.$getTaruna.')';
                $total      = JurnalTaruna::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->suratizintaruna($condition, $limit, $order, $dir);
               
            }
        }else {
            if($roleName=='Taruna'){
                $condition = 'surat_header.id_user='.$id_user.' AND surat_header.id '.$diff.' '.$lastId.'';
                $total = SuratIzin::where('id_user', $id_user)
                            ->count();
                
                $count = SuratIzin::whereRaw($condition)->count();
                $data = $this->suratizintaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
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
               
            }else if('Wali Asuh'){
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
               

            }else if('Pembina'){
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
                    $tarunaId[]=$value->taruna_id;
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
       
            if(in_array($value->id_category, $getCategoryId)){
                $dataPermission = ['edit', 'delete'];
            }
            $result['suratizin'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'tanggal'=>$value->tanggal,
                'jenis_surat'=>$value->category,
                'id_category'=>$value->id_category,
                'status'=> $status,
                'download'=> $download,
                'permission'=>$dataPermission
            ];
                
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
            ->select(DB::raw("(DATE(surat_header.created_at))as tanggal"),'users.name', 'surat_header.status', 'menu_persetujuan.nama_menu as category', 'surat_header.id as id', 'surat_header.id_category as id_category')
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
        $getSurat = SuratIzin::where('id', $id)->first();
        switch ($getSurat->id_category) {
            case 1:
                $getSuratDetail = IzinSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                $data = [];
                $data['header'] = $getSurat;
                $data['body']   = $getSuratDetail;
                return $this->sendResponse($data, 'suratizin load successfully.');
                break;
            case 2:
                $getSuratDetail = KeluarKampus::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 3:
                $getSuratDetail = TrainingCenter::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 4:
                $getSuratDetail = PernikahanSaudara::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 5:
                $getSuratDetail = PemakamanKeluarga::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 6:
                $getSuratDetail = OrangTuaSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 7:
                $getSuratDetail = Tugas::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 8:
                $getSuratDetail = KegiatanDalam::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            case 9:
                $getSuratDetail = KegiatanPesiar::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                break;
            default:
                $getSuratDetail = [];
                break;
        }
    }

    public function gettaruna(Request $request)
    {
        $getUser    = User::find($request->id_user);
        $roleName   = $getUser->getRoleNames()[0];
        if($roleName=='Taruna'){
            $tarunaId   = [];
            //$orangtua   = OrangTua::where('taruna_id', )->get();
            return ['id'=>$getUser->id, 'name'=>$getUser->name];
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
            $tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            return $tarunaData;
        }else if($roleName=='Wali Asuh'){
            $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->join('users', 'users.id', '=', 'taruna_keluarga_asuh.id')
                            ->select('taruna_keluarga_asuh.taruna_id', 'users.name')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                            ->get();

            $tarunaId       = [];
            $tarunaData     = [];
            foreach ($taruna as $key => $value) {
                $tarunaId[]=$value->taruna_id;
                $tarunaWithName[]=['id'=>$value->taruna_id, 'name'=>$value->name];
            }
            $tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            return $tarunaData;
        }else if($roleName=='Pembina'){
            $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->join('users', 'users.id', '=', 'taruna_keluarga_asuh.id')
                            ->select('taruna_keluarga_asuh.taruna_id', 'users.name')
                            ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                            ->get();
  
            $tarunaId       = [];
            $tarunaData     = [];
            foreach ($taruna as $key => $value) {
                $tarunaId[]=$value->taruna_id;
                $tarunaWithName[]=['id'=>$value->taruna_id, 'name'=>$value->name];
            }
            $tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            return $tarunaData;
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
            $tarunaData['id']       = implode(',',$tarunaId);
            $tarunaData['taruna']   = $tarunaWithName;
            return $tarunaData;
        }
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
    
}
