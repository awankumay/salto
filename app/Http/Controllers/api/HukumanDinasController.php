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
use App\HukumanDinas;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use App\Traits\Firebase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class HukumanDinasController extends BaseController
{
    use ImageTrait;
    use Firebase;
    
    public function gethukdis(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'tb_hukdis.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_hukdis.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $author = $getUser;
        $roleAuthor = $author->getRoleNames()[0];
        if(!empty($search)){
            $getUser = User::find($search);
            $id_user = $getUser->id;
        }
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        $data=[];
        if($order=='status'){
            $order='tb_hukdis.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_hukdis.id';
        }

        $permission = [];
        if($lastId==0){
            if($roleName=='Taruna'){
                $id = [];
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'tb_hukdis.id_taruna in('.$getTaruna.')';
                $total      = HukumanDinas::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->hukdistaruna($condition, $limit, $order, $dir);
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
                $condition  = 'tb_hukdis.id_taruna in('.$getTaruna.')';
                $total      = HukumanDinas::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->hukdistaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_hukdis.id_taruna in('.$getTaruna.')';;
                $total      = HukumanDinas::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->hukdistaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Pembina'){
                $condition  = 'tb_hukdis.id_user='.$id_user;
                $total      = HukumanDinas::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->hukdistaruna($condition, $limit, $order, $dir);
               
            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {

                $condition  = 'tb_hukdis.id_taruna is not null';
                $total      = HukumanDinas::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->hukdistaruna($condition, $limit, $order, $dir);
               
            }
        }else {
            if($roleName=='Taruna'){
                $condition  = 'tb_hukdis.id_taruna ='.$id_user.' AND tb_hukdis.id '.$diff.' '.$lastId.'';
                $total      =  HukumanDinas::whereRaw($condition)
                                ->count();  
                $count = HukumanDinas::whereRaw($condition)->count();
                $data = $this->hukdistaruna($condition, $limit, $order, $dir);
            }else if($roleName=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_hukdis.id_taruna in('.$getTaruna.') AND tb_hukdis.id '.$diff.' '.$lastId.'';
                $total = HukumanDinas::whereRaw('tb_hukdis.id_taruna in('.$getTaruna.')')
                            ->count();
                
                $count = HukumanDinas::whereRaw($condition)->count();
                $data = $this->hukdistaruna($condition, $limit, $order, $dir);
               
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
                $condition = 'tb_hukdis.id_taruna in('.$getTaruna.') AND tb_hukdis.id '.$diff.' '.$lastId.'';
                $total = HukumanDinas::whereRaw('tb_hukdis.id_taruna in('.$getTaruna.')')
                            ->count();
                
                $count = HukumanDinas::whereRaw($condition)->count();
                $data = $this->hukdistaruna($condition, $limit, $order, $dir);
               

            }else if($roleName=='Pembina'){
                $condition = 'tb_hukdis.id_user ='.$id_user.' AND tb_hukdis.id '.$diff.' '.$lastId.'';
                $total = HukumanDinas::whereRaw('tb_hukdis.id_user ='.$id_user)
                            ->count();
                
                $count = HukumanDinas::whereRaw($condition)->count();
                $data = $this->hukdistaruna($condition, $limit, $order, $dir);
               

            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {

                $condition = 'tb_hukdis.id_taruna is not null AND tb_hukdis.id '.$diff.' '.$lastId.'';
                $total = HukumanDinas::whereRaw('tb_hukdis.id_taruna is not null')
                            ->count();
                
                $count = HukumanDinas::whereRaw($condition)->count();
                $data = $this->hukdistaruna($condition, $limit, $order, $dir);
               
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
            if(($roleName=='Pembina') && $value->status!=1){
                $dataPermission = ['edit', 'delete'];
            }
            if(!empty($search)){
                if($roleAuthor=='Pembina' && $value->status!=1){
                    $dataPermission = ['edit', 'delete'];
                }
            }
            switch ($value->tingkat) {
                case 1:
                    $tingkat = 'Ringan';
                    break;
                case 2:
                    $tingkat = 'Sedang';
                    break;
                case 2:
                    $tingkat = 'Berat';
                    break;
                
                default:
                    $tingkat = 'Ringan';
                    break;
            }
            $result['hukdis'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'tingkat'=>$tingkat,
                'created_at_bi'=>date('d-m-Y H:i', strtotime($value->tanggal)),
                'status_name'=> $status,
                'status'=> $value->status,
                'hukuman'=> substr($value->hukuman, 0, 40).'...',
                'permission'=>$dataPermission
            ];
        }       
        
        $result['info']['permissionCreate'] = false;
        if($roleName=='Pembina'){
            $result['info']['permissionCreate'] = true;
        }
        if(!empty($search)){
            if($roleAuthor=='Pembina'){
                $result['info']['permissionCreate'] = true;
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
        return $this->sendResponse($result, 'hukdis load successfully.');
    }

    public function hukdisdetail(Request $request)
    {
        $id   = $request->id;
        $getSurat = HukumanDinas::join('users as taruna', 'taruna.id', '=', 'tb_hukdis.id_taruna')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_hukdis.user_approve_level_1')
                                    ->leftjoin('users as pembina', 'pembina.id', '=', 'tb_hukdis.id_user')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_hukdis.grade')
                                    ->select('tb_hukdis.id as id', 
                                            'tb_hukdis.id_user as id_user',
                                            'tb_hukdis.id_taruna as id_taruna',
                                            'tb_hukdis.stb as stb',
                                            'taruna.name as nama_taruna',
                                            'pembina.name as nama_pembina',
                                            'tb_hukdis.photo as photo',
                                            'tb_hukdis.keterangan as keterangan',
                                            'tb_hukdis.tingkat as tingkat',
                                            'tb_hukdis.hukuman as hukuman',
                                            'tb_hukdis.start_time as start_time',
                                            'tb_hukdis.end_time as end_time',
                                            'tb_hukdis.status as status',
                                            'tb_hukdis.updated_at as updated_at',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_hukdis.date_approve_level_1 as date_approve_1',
                                            'tb_hukdis.reason_level_1 as user_reason_1',
                                            'tb_hukdis.status_level_1 as status_level_1',
                                            'grade.grade as grade'
                                            )
                                    ->where('tb_hukdis.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseError($data, 'Hukuman Dinas Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];

        switch ($getSurat->tingkat) {
            case 1:
                $tingkat = 'Ringan';
                break;
            case 2:
                $tingkat = 'Sedang';
                break;
            case 2:
                $tingkat = 'Berat';
                break;
            
            default:
                $tingkat = 'Ringan';
                break;
        }

        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'id_taruna'=>$getSurat->id_taruna,
            'stb'=>$getSurat->stb,
            'nama_taruna'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'keterangan'=>$getSurat->keterangan,
            'tingkat'=>$getSurat->tingkat,
            'tingkat_name'=>$tingkat,
            'hukuman'=>$getSurat->hukuman,
            'start_time'=>date('Y-m-d H:i', strtotime($getSurat->start_time)),
            'end_time'=>date('Y-m-d H:i', strtotime($getSurat->end_time)),
            'start_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->start_time)),
            'end_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->end_time)),
            'nama_pembina'=>$getSurat->nama_pembina,
            'created_at'=>date('Y-m-d H:i', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y H:i', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/hukdis/".$getSurat->photo : '',
            'form'=>['keterangan', 'tingkat', 'hukuman', 'id_taruna', 'start_time', 'end_time', 'id_user'],
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'show_persetujuan'=>false,
            'download'=>'-'
        );
        if(!empty($request->cetak)){
            return $data;
        }

        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
        $data['permission'] = [];
        if($roleName=='Pembina' && $getSurat->status_level_1!=1 && $getSurat->status!=1){
            $data['permission'] = ['edit', 'delete'];
        }
        if(($roleName=='Akademik dan Ketarunaan' || $roleName=='Super Admin') && $getSurat->status!=1){
            $data['show_persetujuan'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetaksurat/id/'.$request->id.'/id_user/'.$request->id_user.'/cetak/hukdis';
        }

        return $this->sendResponse($data, 'prestasi load successfully.');
    }

    public function inputhukdis(Request $request)
    {
        if(!empty($request->id)){
            return $this->updatehukdis($request);
        }else {
            return $this->savehukdis($request);
        }
    }

    public function savehukdis($request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_taruna' => 'required',
            'tingkat' =>'required',
            'hukuman' =>'required',
            'keterangan' =>'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $image=false;
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/hukdis/');
            if($image==false){
                return $this->sendResponseFalse($data, 'failed upload');  
            }
        }

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
                $getTaruna = User::where('id', $request->id_taruna)->first();
                if(empty($getTaruna)){
                    return $this->sendResponseError($data, 'taruna tidak ditemukan');
                }
                $input['grade']             = $getTaruna->grade;
                $input['stb']               = $getTaruna->stb;
                $input['status_level_1']    = 0;
                $input['status']            = 0;
                $input['start_time']        = date('Y-m-d H:i:s', strtotime($request->start_time));
                $input['end_time']          = date('Y-m-d H:i:s', strtotime($request->end_time));
               
                $id                         = DB::table('tb_hukdis')->insertGetId($input);
                
                $data['firebase']           = false;
                $keluarga                   = User::keluargataruna($getTaruna->id);
                $keluarga_asuh              = !empty($keluarga) ? strtolower($keluarga->name) : null;
                
                $dataFirebase = [];
                $dataFirebase = ['id'=>$getTaruna->id, 'keluarga_asuh'=>$keluarga_asuh];
                $topic = User::topic('createhukdis', $dataFirebase);
                if(!empty($topic)){
                    set_time_limit(60);
                    for ($i=0; $i < count($topic); $i++) { 
                        $paramsFirebase=['title'=>'Pemberitahuan hukuman dinas baru',
                        'body'=>'hukuman dinas baru telah dibuat',
                        'page'=>'/hukdis/detail/id/'.$id,
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
            return $this->sendResponse($data, 'hukdis create successfully.');
        } catch (\Throwable $th) {
            //@dd($th->getMessage());
            DB::rollBack();
            if($image!=false){
                $this->DeleteImage($image, config('app.documentImagePath').'/hukdis/');
            }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'hukdis create failure.');
        }

    }

    public function updatehukdis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_taruna' => 'required',
            'tingkat' =>'required',
            'hukuman' =>'required',
            'keterangan' =>'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $image=false;
        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/hukdis/');
            if($image==false){
                return $this->sendResponseFalse($data, 'failed upload');  
            }
        }

        try {

            if(!empty($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                    $this->DeleteImage($prestasi->photo, config('app.documentImagePath').'/hukdis/');
                }
            }

            DB::beginTransaction();
            $prestasi = HukumanDinas::where('id_user', $request->id_user)->where('id', $request->id)->first();
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $getTaruna = User::where('id', $request->id_taruna)->first();
            if(empty($getTaruna)){
                return $this->sendResponseError($data, 'taruna tidak ditemukan');
            }
            $input['grade'] = $getTaruna->grade;
            $input['stb'] = $getTaruna->stb;
            $input = $request->all();
            $input['status']   = 0;
            $input['status_disposisi']  = 0;
            $input['status_level_1']   = 0;
            $prestasi->update($input);
            DB::commit();
            $data['status']     = true;
            $data['firebase']   = false;

            $keluarga       = User::keluargataruna($getTaruna->id);
            $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase   = [];
            $dataFirebase   = ['id'=>$getTaruna->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic          = User::topic('createhukdis', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan hukuman dinas baru',
                    'body'=>'hukuman dinas baru telah dibuat',
                    'page'=>'/hukdis/detail/id/'.$id,
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
            return $this->sendResponse($data, 'hukdis updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath').'/hukdis/');
                }
            }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'hukdis failure updated.');

        }
    
    }

    public function deletehukdis(Request $request)
    {
        $prestasi = HukumanDinas::where('id_user', $request->id_user)->where('id', $request->id)->first();
        $data=[];
        try {
            DB::beginTransaction();
         /*    if($prestasi->photo){
                $this->DeleteImage($prestasi->photo, config('app.documentImagePath').'/hukdis/');
            } */
            $prestasi->user_deleted = $request->id_user;
            $prestasi->save();
            $prestasi->delete();
            DB::commit();
            $data['status']=true;
            return $this->sendResponse($data, 'hukdis deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            $data['status']=false;
            return $this->sendResponseFalse($data, 'hukdis deleted failure.');
        }
        return $this->sendResponse($result, 'hukdis delete successfully.');
    }

    public function hukdistaruna($condition, $limit, $order, $dir)
    {
        return HukumanDinas::join('users', 'users.id', '=', 'tb_hukdis.id_taruna')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_hukdis.updated_at))as tanggal"),'users.name', 'tb_hukdis.status', 'tb_hukdis.hukuman', 'tb_hukdis.id as id')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }

    public function cetakhukdis(Request $request){
        $data   = [];
        $res    = [];
        $request->request->add(['cetak'=> true]);
        $getData   = $this->hukdisdetail($request);
        $data   = array(
            'name'=>$getData['nama_taruna'],
            'category_name'=>'DATA HUKUMAN DISIPILIN',
            'tanggal_cetak'=>\Carbon\Carbon::parse($getData['date_approve_1'])->isoFormat('D MMMM Y'),
            'user_approve_1' =>$getData['user_approve_1'],
            'date_approve_1' =>$getData['date_approve_1'],
            'header'=>['No', 'Nama', 'No.STB', 'Keterangan', 'Tingkat', 'Hukuman', 'Waktu', 'TGL Pengajuan'],
            'body'=>['1', $getData['nama_taruna'], $getData['stb'], $getData['keterangan'], $getData['tingkat_name'], $getData['hukuman'], $getData['start_time_bi'].' sd '.$getData['end_time_bi'], $getData['created_at_bi']],
            'template'=>1
        );
        if($getData['status']==0){
            return $this->sendResponse($res, 'link surat generate failure');
        }
        if(!empty($getData)){
            $pdf = app()->make('dompdf.wrapper');
            $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
            return $pdf->stream();
        }
           return $this->sendResponse($res, 'link surat generate failure');
    }

    public function approvehukdis(Request $request)
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
        $hukdis = HukumanDinas::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();
        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan' || $getUser->getRoleNames()[0]=='Super Admin'){
            $hukdis->user_approve_level_1=$request->id_user;
            $hukdis->date_approve_level_1=date('Y-m-d H:i:s');
            $hukdis->status_level_1=$request->status;
            $hukdis->status=$request->status;
            $hukdis->reason_level_1=$request->reason;
            $hukdis->save();
            $data['status'] = true;
            $data['firebase'] = false;

            $keluarga       = User::keluargataruna($hukdis->taruna_id);
            $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
            $dataFirebase   = [];
            $dataFirebase   = ['id'=>$hukdis->taruna_id, 'keluarga_asuh'=>$keluarga_asuh];
            $firebase       = [];
            $topic          = User::topic('approve-aak', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan persetujuan hukuman dinas baru',
                    'body'=>'hukuman dinas baru telah disetujui aak',
                    'page'=>'/hukdis/detail/id/'.$request->id,
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

            return $this->sendResponse($data, 'approve hukdis success');
        }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'approve hukdis failure');
    }
}