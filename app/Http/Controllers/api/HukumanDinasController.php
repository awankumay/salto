<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Grade;
use App\OrangTua;
use App\WaliAsuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\Prestasi;
use App\HukumanDinas;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class HukumanDinasController extends BaseController
{
    use ImageTrait;
    
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
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
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
            }else if($roleName=='OrangTua'){
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
                $condition  = 'tb_hukdis.id_user='.$request->id_user;
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
            }else if($roleName=='OrangTua'){
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
            if(($roleName=='Pembina' || $roleName=='Super Admin' ) && $value->status!=1){
                $dataPermission = ['edit', 'delete'];
            }
            
            $result['hukdis'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'status_name'=> $status,
                'status'=> $value->status,
                'hukuman'=> substr($value->hukuman, 0, 40).'...',
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
        return $this->sendResponse($result, 'hukdis load successfully.');
    }

    public function hukdisdetail(Request $request)
    {
        $id   = $request->id;
        $getSurat = Prestasi::join('users as taruna', 'taruna.id', '=', 'tb_hukdis.id_taruna')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_hukdis.user_approve_level_1')
                                    ->leftjoin('users as user_approve_2', 'user_approve_2.id', '=', 'tb_hukdis.user_approve_level_2')
                                    ->leftjoin('users as pembina', 'id_user.id', '=', 'tb_hukdis.id_user')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_hukdis.grade')
                                    ->select('tb_hukdis.id as id', 
                                            'tb_hukdis.id_user as id_user',
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
                                    ->where('tb_penghargaan.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseFalse($data, 'Penghargaan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'stb'=>$getSurat->stb,
            'nama_taruna'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'keterangan'=>$getSurat->keterangan,
            'tingkat'=>$getSurat->tingkat,
            'hukuman'=>$getSurat->hukuman,
            'start_time'=>date('Y-m-d H:i', strtotime($getSurat->start_time)),
            'end_time'=>date('Y-m-d H:i', strtotime($getSurat->end_time)),
            'start_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->start_time)),
            'end_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->end_time)),
            'nama_pembina'=>$getSurat->nama_pembina,
            'created_at'=>date('Y-m-d', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/hukdis/".$getSurat->photo : '',
            'form'=>['keterangan', 'tingkat', 'hukuman', 'id_taruna', 'start_time', 'end_time', 'id_user'],
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->reason_level_1,
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
        if($roleName=='Pembina' && $getSurat->status_level_1!=1){
            $data['permission'] = ['edit', 'delete'];
        }
        if($roleName=='Akademik dan Ketarunaan' && $getSurat->status!=1 && $roleName=='Super Admin'){
            $data['show_persetujuan'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetakhukdis/id/'.$request->id.'/id_user/'.$request->id_user;
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
        $image='';
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
                HukumanDinas::create($input);

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
        $image='';
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
            $input = $request->all();
            $input['status']   = 0;
            $input['status_disposisi']  = 0;
            $input['status_level_1']   = 0;
            $prestasi->update($input);
            DB::commit();
            $data['status'] = true;
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
            ->select(DB::raw("(DATE(tb_hukdis.created_at))as tanggal"),'users.name', 'tb_hukdis.status', 'tb_hukdis.hukuman', 'tb_hukdis.id as id')
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
            'name'=>$getData['name'],
            'category_name'=>'DATA PENGHARGAAN',
            'tanggal_cetak'=>\Carbon\Carbon::parse($getData['date_approve_1'])->isoFormat('D MMMM Y'),
            'user_approve_1' =>$getData['user_approve_1'],
            'date_approve_1' =>$getData['date_approve_1'],
            'header'=>['No', 'Nama', 'No.STB', 'Keterangan Penghargaan', 'Tingkat', 'Tempat', 'Waktu', 'Tanggal Pengajuan'],
            'body'=>['1', $getData['name'], $getData['stb'], $getData['keterangan'], $getData['tingkat'], $getData['tempat'], $getData['waktu'], $getData['created_at_bi']],
            'template'=>1
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
            return $this->sendResponse($data, 'approve hukdis success');
        }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'approve hukdis failure');
    }
}