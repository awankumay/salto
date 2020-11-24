<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Grade;
use App\OrangTua;
use App\WaliasuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\Prestasi;
use App\Suket;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SuketController extends BaseController
{
    use ImageTrait;
    
    public function getsuket(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'tb_suket.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_suket.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        $data=[];
        if($order=='status'){
            $order='tb_suket.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_suket.id';
        }

        $permission = [];
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
                $condition  = 'tb_suket.id_user in('.$getTaruna.')';
                $total      =  Suket::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->sukettaruna($condition, $limit, $order, $dir);
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
                $condition  = 'tb_suket.id_user in('.$getTaruna.')';
                $total      = Suket::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->sukettaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_suket.id_user in('.$getTaruna.')';
                $total      = Suket::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->sukettaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_suket.id_user in('.$getTaruna.')';
                $total      = Suket::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->sukettaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_suket.id_user in('.$getTaruna.')';
                $total      = Suket::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->sukettaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_suket.id_user in('.$getTaruna.') AND tb_suket.id '.$diff.' '.$lastId.'';
                $total      =  Suket::whereRaw($condition)
                                ->count();  
                $count = Suket::whereRaw($condition)->count();
                $data = $this->sukettaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                if(!empty($taruna)){
                    foreach ($taruna as $key => $value) {
                        $tarunaId[]=$value->taruna_id;
                    }
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_suket.id_user in('.$getTaruna.') AND tb_suket.id '.$diff.' '.$lastId.'';
                $total = Suket::whereRaw('tb_suket.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Suket::whereRaw($condition)->count();
                $data = $this->sukettaruna($condition, $limit, $order, $dir);
               
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
                $condition = 'tb_suket.id_user in('.$getTaruna.') AND tb_suket.id '.$diff.' '.$lastId.'';
                $total = Suket::whereRaw('tb_suket.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Suket::whereRaw($condition)->count();
                $data = $this->sukettaruna($condition, $limit, $order, $dir);
               

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
                $condition = 'tb_suket.id_user in('.$getTaruna.') AND tb_suket.id '.$diff.' '.$lastId.'';
                $total = Suket::whereRaw('tb_suket.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Suket::whereRaw($condition)->count();
                $data = $this->sukettaruna($condition, $limit, $order, $dir);
               

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
                $condition = 'tb_suket.id_user in('.$getTaruna.') AND tb_suket.id '.$diff.' '.$lastId.'';
                $total = Suket::whereRaw('tb_suket.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Suket::whereRaw($condition)->count();
                $data = $this->sukettaruna($condition, $limit, $order, $dir);
               
            }
        }
        foreach ($data as $key => $value) {
            if($value->status==1){
                $status='Disetujui';
            }else if ($value->status==0) {
                $status='Belum Disetuji';
                $download = '-';
            }else{
                $status='Tidak Disetuji';
                $download = '-';
            }

            $dataPermission = [];
            if($roleName=='Taruna' || $roleName=='Orang Tua'){
                if(($roleName=='Taruna' || $roleName=='Orang Tua') && $value->status_disposisi!=1 && $value->status!=1){
                    if($value->user_created==$id_user){
                        $dataPermission = ['edit', 'delete'];
                    }
                }
            }else {
                $dataPermission = [];
            }

            $result['suket'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'tanggal'=>$value->tanggal,
                'status_name'=> $status,
                'status'=> $value->status,
                'keperluan'=> substr($value->keperluan, 0, 40).'...',
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
        return $this->sendResponse($result, 'suket load successfully.');
    }

    public function suketdetail(Request $request)
    {
        $id   = $request->id;
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
            return $this->sendResponseError($data, 'Suket Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
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
    
        if($roleName=='Pembina' && $data['status_level_1']!=1 && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }

        $data['permission'] = [];
        if(($roleName=='Taruna' || $roleName=='Orang Tua') && $getSurat->status_disposisi!=1 && $getSurat->status!=1) {
            if($getSurat->user_created==$request->id_user){
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
            $data['download'] = \URL::to('/').'/api/cetaksuket/id/'.$request->id.'/id_user/'.$request->id_user;
        }

        return $this->sendResponse($data, 'suket load successfully.');
    }

    public function inputsuket(Request $request)
    {
        if(!empty($request->id)){
            return $this->updatesuket($request);
        }else {
            return $this->savesuket($request);
        }
    }

    public function savesuket($request)
    {
        $validator = Validator::make($request->all(), [
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
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $image = '';
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/suket/');
            if($image==false){
                return $this->sendResponseFalse($data, 'failed upload');  
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
                Arr::forget($input, array('file'));
                $getUser = User::where('id', $request->id_user)->first();
                if($getUser->getRoleNames()[0]=='Orang Tua'){
                    $taruna     = OrangTua::where('orangtua_id', $id_user)->first(); 
                    if(empty($taruna)){
                        return $this->sendResponseError($data, 'taruna tidak ditemukan');  
                    }
                    $getUser    = User::where('id', $taruna->taruna_id)->first();
                }
                $input['grade']             = $getUser->grade;
                $input['nama']              = $getUser->name;
                $input['id_user']           = $getUser->id;
                $input['stb']               = $getUser->stb;
                $input['status_disposisi']  = 0;
                $input['status_level_1']    = 0;
                $input['status_level_2']    = 0;
                $input['status']            = 0;
                Suket::create($input);

            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'suket create successfully.');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
            if($image!=false){
                $this->DeleteImage($image, config('app.documentImagePath').'/suket/');
            }
            $data['status'] = false;
            return $this->sendResponseError($data, 'suket create failure.');
        }

    }

    public function updatesuket(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $image = '';
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/suket/');
            if($image==false){
                return $this->sendResponseFalse($data, 'failed upload');  
            }
        }
        $id_user = $request->id_user;
        try {

            DB::beginTransaction();
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $getUser = User::where('id', $request->id_user)->first();
            if($getUser->getRoleNames()[0]=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->first(); 
                if(empty($taruna)){
                    return $this->sendResponseError($data, 'taruna tidak ditemukan');  
                }
                $getUser    = User::where('id', $taruna->taruna_id)->first();
            }
            $suket      = Suket::where('id_user', $getUser->id)->where('id', $request->id)->first();
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
            $input['status']   = 0;
            $input['status_disposisi']  = 0;
            $input['status_level_1']   = 0;
            $input['status_level_2']   = 0;
            $suket->update($input);
            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'suket updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath').'/suket/');
                }
            }
            $data['status'] = false;
            return $this->sendResponseError($data, 'suket failure updated.');

        }
    
    }

    public function deletesuket(Request $request)
    {
        $suket = Suket::where('user_created', $request->id_user)->where('id', $request->id)->first();
        $data=[];
        try {
            DB::beginTransaction();
            /* if($suket->photo){
                $this->DeleteImage($suket->photo, config('app.documentImagePath').'/suket/');
                $suket->photo=NULL;
            } */
            $suket->user_deleted = $request->id_user;
            $suket->save();
            $suket->delete();
            DB::commit();
            $data['status']=true;
            return $this->sendResponse($data, 'suket deleted successfully.');
        } catch (\Throwable $th) {
            //@dd($th->getMessage());
            DB::rollback();
            $data['status']=false;
            return $this->sendResponseError($data, 'suket deleted failure.');
        }
        return $this->sendResponse($result, 'suket delete successfully.');
    }

    public function sukettaruna($condition, $limit, $order, $dir)
    {
        return Suket::join('users', 'users.id', '=', 'tb_suket.id_user')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_suket.updated_at))as tanggal"),'users.name', 'tb_suket.keperluan', 'tb_suket.status', 'tb_suket.id as id', 'tb_suket.user_created as user_created', 'tb_suket.status_disposisi as status_disposisi')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }

    public function cetaksuket(Request $request){
        $data   = [];
        $res    = [];
        $request->request->add(['cetak'=> true]);
        $getData   = $this->suketdetail($request);
        $data   = array(
            'name'=>$getData['name'],
            'category_name'=>'SURAT KETERANGAN',
            'tanggal_cetak'=>\Carbon\Carbon::parse($getData['date_approve_2'])->isoFormat('D MMMM Y'),
            'header'=>['Nama', 'No.STB', 'Tempat, Tanggal Lahir', 'Anak Dari : ', 
                        'Nama Orang Tua', 'Pekerjaan', 'Alamat'],
            'body'=>[$getData['name'], $getData['stb'], $getData['ttl'], '', $getData['orangtua'], $getData['pekerjaan'], $getData['alamat']],
            'template'=>3,
            'id_surat_cetak'=>$getData['id']+1
        );
        if($getData['status']==0){
            return $this->sendResponse($res, 'link surat generate failure');
        }
        if(!empty($getData)){
            $pdf = app()->make('dompdf.wrapper');
            $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
            /* $content = $pdf->download()->getOriginalContent();
            $name = \Str::slug($data['category_name'].'-'.$data['name'].'-'.date('dmyhis')).".pdf";
            Storage::put('public/'.config('app.documentImagePath').'/temp/'.$name, $content) ;
           
            //\Storage::put(config('app.documentImagePath').$name, $pdf->output());
            //$data->storeAs('public/'.config('app.documentImagePath'), $file_name);
            $link =  \URL::to('/').'/storage/'.config('app.documentImagePath').'/temp/'.$name;
            $res['link'] = $link; */
            return $pdf->stream();
        }
           return $this->sendResponse($res, 'link surat generate failure');
    }

    public function disposisisuket(Request $request)
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
        $suket = Suket::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $suket->user_disposisi=$request->id_user;
        $suket->date_disposisi=date('Y-m-d H:i:s');
        $suket->reason_disposisi=$request->reason;
        $suket->status_disposisi=$request->status;
        $suket->save();
        $data['status'] = true;
        return $this->sendResponse($data, 'disposisi suket success');
    }

    public function approvesuket(Request $request)
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
        $suket = Suket::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();
        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan'){
            $suket->user_approve_level_1=$request->id_user;
            $suket->date_approve_level_1=date('Y-m-d H:i:s');
            $suket->status_level_1=$request->status;
            $suket->reason_level_1=$request->reason;
            $suket->save();
            $data['status'] = true;
            return $this->sendResponse($data, 'approve suket success');
        }

        if($getUser->getRoleNames()[0]=='Direktur' || $getUser->getRoleNames()[0]=='Super Admin'){
            $suket->user_approve_level_2=$request->id_user;
            $suket->date_approve_level_2=date('Y-m-d H:i:s');
            $suket->status_level_2=$request->status;
            $suket->reason_level_2=$request->reason;
            $suket->status=$request->status;
            $suket->save();
            $data['status'] = true;
            return $this->sendResponse($data, 'approve suket success');
        }

            $data['status'] = false;
            return $this->sendResponseFalse($data, 'approve suket failure');
    }
}