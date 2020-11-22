<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Grade;
use App\OrangTua;
use App\WaliAsuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\Pengasuhan;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PengasuhanController extends BaseController
{
    use ImageTrait;
    
    public function getpengasuhan(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'tb_pengasuhan_daring.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_pengasuhan_daring.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        if($order=='status'){
            $order='tb_pengasuhan_daring.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_pengasuhan_daring.id';
        }

        $permission = [];
        if($lastId==0){
            if($roleName=='Taruna' || $roleName=='Pembina'){
                if($roleName=='Taruna'){
                    $getKeluargaAsuh = TarunaKeluargaAsuh::where('taruna_id', $getUser->id)->first();
                }else{
                    $getKeluargaAsuh = PembinaKeluargaAsuh::where('pembina_id', $getUser->id)->first();
                }
                $condition  = 'tb_pengasuhan_daring.keluarga_asuh_id='.$getKeluargaAsuh->keluarga_asuh_id;
                $total      =  Pengasuhan::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->pengasuhantaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                $total  = 0; 
                $count  = $total;
                $data   = [];
               
            }else if($roleName=='Wali Asuh'){
                $condition  = 'tb_pengasuhan_daring.id_user='.$getUser->id;
                $total      = Pengasuhan::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->pengasuhantaruna($condition, $limit, $order, $dir);
               
            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $waliasuh     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 4)
                                ->whereNull('users.deleted_at')
                                ->get();
                $waliasuhId   = [];
                foreach ($waliasuh as $key => $value) {
                    $waliasuhId[]=$value->id;
                }
                $getWaliasuh  = implode(',',$waliasuhId);
                $condition  = 'tb_pengasuhan_daring.id_user in('.$getWaliasuh.')';
                $total      = Pengasuhan::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->pengasuhantaruna($condition, $limit, $order, $dir);
               
            }
        }else {
            if($roleName=='Taruna' || $roleName=='Pembina'){
                if($roleName=='Taruna'){
                    $getKeluargaAsuh = TarunaKeluargaAsuh::where('taruna_id', $getUser->id)->first();
                }else{
                    $getKeluargaAsuh = PembinaKeluargaAsuh::where('pembina_id', $getUser->id)->first();
                }
                $condition  = 'tb_pengasuhan_daring.keluarga_asuh_id='.$getKeluargaAsuh->keluarga_asuh_id.'AND tb_pengasuhan_daring.id '.$diff.' '.$lastId.'';
                $total      =  Pengasuhan::whereRaw('tb_pengasuhan_daring.keluarga_asuh_id='.$getKeluargaAsuh->keluarga_asuh_id)
                                ->count();  
                $count = Pengasuhan::whereRaw('tb_pengasuhan_daring.keluarga_asuh_id='.$getKeluargaAsuh->keluarga_asuh_id)->count();
                $data = $this->pengasuhantaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                
                $count = 0;
                $data = [];
               
            }else if($roleName=='Wali Asuh'){
                $condition  = 'tb_pengasuhan_daring.id_user='.$getUser->id;
               
                $condition = 'tb_pengasuhan_daring.id_user='.$getUser->id.' AND tb_pengasuhan_daring.id '.$diff.' '.$lastId.'';
                $total      = Pengasuhan::whereRaw('tb_pengasuhan_daring.id_user='.$getUser->id)->count();
                $count      = Pengasuhan::whereRaw($condition)->count();
                $data = $this->pengasuhantaruna($condition, $limit, $order, $dir);
               

            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $waliasuh     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 4)
                                ->whereNull('users.deleted_at')
                                ->get();
                $waliasuhId   = [];
                foreach ($waliasuh as $key => $value) {
                    $waliasuhId[]=$value->id;
                }
                $getWaliasuh  = implode(',',$waliasuhId);
                $condition = 'tb_pengasuhan_daring.id_user in('.$getWaliasuh.') AND tb_pengasuhan_daring.id '.$diff.' '.$lastId.'';
                $total = Pengasuhan::whereRaw('tb_pengasuhan_daring.id_user in('.$getWaliasuh.')')
                            ->count();
                
                $count = Pengasuhan::whereRaw($condition)->count();
                $data = $this->pengasuhantaruna($condition, $limit, $order, $dir);
               
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
            if(($roleName=='Wali Asuh' || $roleName=='Super Admin' )){
                $dataPermission = ['edit', 'delete'];
            }

            $result['pengasuhan'][]= [ 
                'id'=>$value->id,
                'id_user'=>$value->id_user,
                'keluarga_asuh'=>$value->keluarga_asuh,
                'judul'=>substr($value->judul, 0, 15).'...',
                'waktu'=>$value->start_time.' s/d '.$value->end_time,
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
        return $this->sendResponse($result, 'pengasuhan load successfully.');
    }

    public function pengasuhandetail(Request $request)
    {
        $id   = $request->id;
        $getSurat = Pengasuhan::join('users as author', 'author.id', '=', 'tb_pengasuhan_daring.id_user')
                                    ->select('tb_pengasuhan_daring.id as id', 
                                            'tb_pengasuhan_daring.id_user as id_user',
                                            'author.name as nama_waliasuh',
                                            'tb_pengasuhan_daring.keluarga_asuh as keluarga_asuh',
                                            'tb_pengasuhan_daring.judul as judul',
                                            'tb_pengasuhan_daring.media as media',
                                            'tb_pengasuhan_daring.id_media as id_media',
                                            'tb_pengasuhan_daring.password as password',
                                            'tb_pengasuhan_daring.start_time as start_time',
                                            'tb_pengasuhan_daring.end_time as end_time',
                                            'tb_pengasuhan_daring.status as status',
                                            'tb_pengasuhan_daring.updated_at as updated_at'
                                            )
                                    ->where('tb_pengasuhan_daring.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseFalse($data, 'Pengasuhan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'name'=>$getSurat->nama_waliasuh,
            'keluarga_asuh'=>$getSurat->keluarga_asuh,
            'judul'=>$getSurat->judul,
            'media'=>$getSurat->media,
            'id_media'=>$getSurat->id_media,
            'password'=>$getSurat->password,
            'start_time'=>$getSurat->start_time,
            'end_time'=>$getSurat->end_time,
            'created_at'=>date('Y-m-d', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->updated_at)),
            'form'=>['judul', 'media', 'id_media', 'password', 'start_time', 'end_time']
        );
        
        if(($roleName=='Wali Asuh')) {
            if($getSurat->id_user==$request->id_user){
                $data['permission'] = ['edit', 'delete'];
            }
        }

        return $this->sendResponse($data, 'prestasi load successfully.');
    }

    public function inputpengasuhan(Request $request)
    {
        if(!empty($request->id)){
            return $this->updateprestasi($request);
        }else {
            return $this->saveprestasi($request);
        }
    }

    public function savepengasuhan($request)
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

        try {
            DB::beginTransaction();
                $request->request->add(['user_created'=> $request->id_user]);
                $request->request->add(['user_updated'=> $request->id_user]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                //Arr::forget($input, array('file', 'waktu'));
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

    public function updatepengasuhan(Request $request)
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

    public function deletepengasuhan(Request $request)
    {
        $prestasi = Prestasi::where('id_user', $request->id_user)->where('id', $request->id)->first();
        $data=[];
        try {
            DB::beginTransaction();
  /*           if($prestasi->photo){
                $this->DeleteImage($prestasi->photo, config('app.documentImagePath').'/prestasi/');
            } */
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

    public function pengasuhantaruna($condition, $limit, $order, $dir)
    {
        return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_pengasuhan_daring.created_at))as tanggal"),'users.name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.judul', 'tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }
}