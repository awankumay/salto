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
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends BaseController
{
    use ImageTrait;
    
    public function getprestasi(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'tb_penghargaan.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_penghargaan.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        if($order=='status'){
            $order='tb_penghargaan.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_penghargaan.id';
        }

        $permission = [];
        if($lastId==0){
            if($roleName=='Taruna'){
                $id = [];
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      =  Prestasi::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
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
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
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
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }
        }else {
            if($roleName=='Taruna'){
                $id = [];
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total      =  Prestasi::whereRaw($condition)
                                ->count();  
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
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
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               

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
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               

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
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
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
            if(($roleName=='Taruna' || $roleName=='Super Admin' ) && $value->status!=1){
                $dataPermission = ['edit', 'delete'];
            }

            $result['penghargaan'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'tanggal'=>$value->tanggal,
                'waktu'=>$value->waktu,
                'status_name'=> $status,
                'status'=> $value->status,
                'keterangan'=> substr($value->keterangan, 0, 40).'...',
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
        return $this->sendResponse($result, 'prestasi load successfully.');
    }

    public function prestasidetail(Request $request)
    {
        $id   = $request->id;
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
            return $this->sendResponseFalse($data, 'Penghargaan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
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
            'reason_level_1'=>$getSurat->reason_level_1,
            'show_disposisi'=>false,
            'show_approve'=>false,
            'download'=>'-'
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
    
        if($roleName=='Pembina' && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }
        if(($roleName=='Taruna')) {
            if($getSurat->id_user!=$request->id_user && $getSurat->status!=1){
                $data['permission'] = [];
            }
        }
        if($roleName=='Akademik dan Ketarunaan' && $getSurat->status!=1 && $getSurat->status_disposisi==1){
            $data['show_persetujuan'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetakprestasi/id/'.$request->id.'/id_user/'.$request->id_user;
        }

        return $this->sendResponse($data, 'prestasi load successfully.');
    }

    public function inputprestasi(Request $request)
    {
        if(!empty($request->id)){
            return $this->updateprestasi($request);
        }else {
            return $this->saveprestasi($request);
        }
    }

    public function saveprestasi($request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'tingkat' =>'required',
            'tempat' =>'required',
            'keterangan' =>'required',
            'waktu' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }

        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/prestasi/');
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
                Arr::forget($input, array('file', 'waktu'));
                $getUser = User::where('id', $request->id_user)->first();
                $input['grade'] = $getUser->grade;
                $input['id_user'] = $getUser->id;
                $input['stb']   = $getUser->stb;
                $input['status_disposisi']  = 0;
                $input['status_level_1']   = 0;
                $input['status']   = 0;
                $input['waktu'] = date('Y-m-d h:i:s', strtotime($request->waktu));
                Prestasi::create($input);

            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'prestasi create successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if($image!=false){
                $this->DeleteImage($image, config('app.documentImagePath').'/prestasi/');
            }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'prestasi create failure.');
        }

    }

    public function updateprestasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'tingkat' =>'required',
            'tempat' =>'required',
            'keterangan' =>'required',
            'waktu' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }

        if($request->file!==null){
            $image = $this->UploadImage($request->file, config('app.documentImagePath').'/prestasi/');
            if($image==false){
                return $this->sendResponseFalse($data, 'failed upload');  
            }
        }

        try {

            DB::beginTransaction();
            $prestasi = Prestasi::where('id_user', $request->id_user)->where('id', $request->id)->first();
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $getUser = User::where('id', $request->id_user)->first();
            $input['grade'] = $getUser->grade;
            if(isset($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                    $this->DeleteImage($prestasi->photo, config('app.documentImagePath').'/prestasi/');
                }
            }
            $input = $request->all();
            $input['status']   = 0;
            $input['status_disposisi']  = 0;
            $input['status_level_1']   = 0;
            $prestasi->update($input);
            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'prestasi updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.documentImagePath').'/prestasi/');
                }
            }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'prestasi failure updated.');

        }
    
    }

    public function deleteprestasi(Request $request)
    {
        $prestasi = Prestasi::where('id_user', $request->id_user)->where('id', $request->id)->first();
        $data=[];
        try {
            DB::beginTransaction();
            if($prestasi->photo){
                $this->DeleteImage($prestasi->photo, config('app.documentImagePath').'/prestasi/');
            }
            $prestasi->user_deleted = $request->id_user;
            $prestasi->save();
            $prestasi->delete();
            DB::commit();
            $data['status']=true;
            return $this->sendResponse($data, 'prestasi deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            $data['status']=false;
            return $this->sendResponseFalse($data, 'prestasi deleted failure.');
        }
        return $this->sendResponse($result, 'prestasi delete successfully.');
    }

    public function penghargaantaruna($condition, $limit, $order, $dir)
    {
        return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_penghargaan.created_at))as tanggal"),'users.name', 'tb_penghargaan.status', 'tb_penghargaan.keterangan', 'tb_penghargaan.id as id')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }

    public function cetakprestasi(Request $request){
        $data   = [];
        $res    = [];
        $request->request->add(['cetak'=> true]);
        $getData   = $this->prestasidetail($request);
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

    public function disposisiprestasi(Request $request)
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
        $prestasi = Prestasi::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $prestasi->user_disposisi=$request->id_user;
        $prestasi->date_disposisi=date('Y-m-d H:i:s');
        $prestasi->reason_disposisi=$request->reason;
        $prestasi->status_disposisi=$request->status;
        $prestasi->save();
        $data['status'] = true;
        return $this->sendResponse($data, 'disposisi prestasi success');
    }

    public function approveprestasi(Request $request)
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
        $prestasi = Prestasi::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();
        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan' || $getUser->getRoleNames()[0]=='Super Admin'){
            $prestasi->user_approve_level_1=$request->id_user;
            $prestasi->date_approve_level_1=date('Y-m-d H:i:s');
            $prestasi->status_level_1=$request->status;
            $prestasi->status=$request->status;
            $prestasi->reason_level_1=$request->reason;
            $prestasi->save();
            $data['status'] = true;
            return $this->sendResponse($data, 'approve prestasi success');
        }
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'approve prestasi failure');
    }
}