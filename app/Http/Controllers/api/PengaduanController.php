<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Grade;
use App\OrangTua;
use App\WaliAsuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\TarunaKeluargaAsuh;
use App\Pengaduan;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends BaseController
{
    use ImageTrait;
    
    public function getpengaduan(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'tb_pengaduan.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_pengaduan.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        if($order=='status'){
            $order='tb_pengaduan.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_pengaduan.id';
        }

        $permission = [];
        if($lastId==0){
            if($roleName=='Admin' || $roleName=='Super Admin'){
                $condition  = 'tb_pengaduan.id is not null';
                $total      =  Pengaduan::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->pengaduan($condition, $limit, $order, $dir);
            }else{
                $condition  = 'tb_pengaduan.id_user='.$id_user;
                $total      =  Pengaduan::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->pengaduan($condition, $limit, $order, $dir);
            }
        }else {
            if($roleName=='Admin' || $roleName=='Super Admin'){
                $condition  = 'tb_pengaduan.id is not null AND tb_pengaduan.id '.$diff.' '.$lastId.'';
                $total      =  Pengaduan::whereRaw('tb_pengaduan.id is not null')
                                ->count();  
                $count = Pengaduan::whereRaw($condition)->count();
                $data = $this->pengaduan($condition, $limit, $order, $dir);
            }else{
                $condition  = 'tb_pengaduan.id_user='.$id_user.' AND tb_pengaduan.id '.$diff.' '.$lastId.'';
                $total      =  Pengaduan::whereRaw('tb_pengaduan.id_user='.$id_user)
                                ->count();  
                $count = Pengaduan::whereRaw($condition)->count();
                $data = $this->pengaduan($condition, $limit, $order, $dir);
            }
        }
        foreach ($data as $key => $value) {
            if($value->status==1){
                $status='Selesai';
            }else if ($value->status==0) {
                $status='Proses';
            }else{
                $status='Tidak Disetuji';
            }
            $dataPermission = [];

            $result['pengaduan'][]= [ 
                'id'=>$value->id,
                'id_user'=>$value->id_user,
                'nama'=>$value->name,
                'pengaduan'=>substr($value->pengaduan, 0, 30).'...',
                'created_at'=>date('Y-m-d', strtotime($value->created_at)),
                'created_at_bi'=>date('d-m-Y', strtotime($value->created_at)),
                'status'=>$value->status,
                'status_name'=>$status
            ];
                
        }

        $result['info']['permissionCreate'] = true;

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
        return $this->sendResponse($result, 'pengaduan load successfully.');
    }

    public function pengaduandetail(Request $request)
    {
        $id   = $request->id;
        $getSurat = Pengaduan::join('users as author', 'author.id', '=', 'tb_pengaduan.id_user')
                                ->leftjoin('users as admin', 'admin.id', '=', 'tb_pengaduan.user_follow_up')
                                    ->select('tb_pengaduan.id as id', 
                                            'tb_pengaduan.id_user as id_user',
                                            'author.name as nama',
                                            'tb_pengaduan.pengaduan as pengaduan',
                                            'tb_pengaduan.follow_up as tanggapan',
                                            'tb_pengaduan.created_at as created_at',
                                            'tb_pengaduan.status as status',
                                            'admin.name as user_follow_up',
                                            'tb_pengaduan.status as status',
                                            'tb_pengaduan.user_created as user_created',
                                            'tb_pengaduan.date_follow_up as date_follow_up'
                                            )
                                    ->where('tb_pengaduan.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseFalse($data, 'Pengaduan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'name'=>$getSurat->nama,
            'pengaduan'=>$getSurat->pengaduan,
            'tanggapan'=>$getSurat->tanggapan,
            'created_at'=>date('Y-m-d', strtotime($getSurat->created_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->created_at)),
            'status'=>$getSurat->status,
            'user_follow_up'=>$getSurat->user_follow_up,
            'end_time'=>$getSurat->end_time,
            'date_follow_up'=>date('Y-m-d H:i', strtotime($getSurat->date_follow_up)),
            'created_at_bi'=>date('d-m-Y H:i', strtotime($getSurat->created_at)),
            'form'=>['id_user', 'pengaduan'],
            'follow_up'=>false
        );
        $data['permission']=[];
        if($roleName=='Admin' || $roleName=='Super Admin') {
            $data['follow_up']=true;
        }

        return $this->sendResponse($data, 'Pengaduan load successfully.');
    }

    public function inputpengaduan(Request $request)
    {
        if(!empty($request->id)){
            return $this->updatepengaduan($request);
        }else {
            return $this->savepengaduan($request);
        }
    }

    public function savepengaduan($request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'pengaduan' =>'required'
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
                $input['id_user'] = $request->id_user;
                $input['pengaduan']   = $request->pengaduan;
                Pengaduan::create($input);

            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'pengaduan create successfully.');
        } catch (\Throwable $th) {
            //@dd($th->getMessage());
            DB::rollBack();
            $data['status'] = false;
            return $this->sendResponseError($data, 'pengaduan create failure.');
        }

    }

    public function updatepengaduan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'pengaduan' =>'required'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        try {

            DB::beginTransaction();
            $pengaduan = Pengaduan::where('id_user', $request->id_user)->where('id', $request->id)->first();
            if(empty($pengaduan)){
                return $this->sendResponseError($data, 'data not found');   
            }
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);

            $input = $request->all();
            $input['id_user'] = $request->id_user;
            $input['pengaduan']   = $request->pengaduan;

            $pengaduan->update($input);
            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'pengaduan updated successfully.');
        } catch (\Throwable $th) {
            //@dd($th->getMessage());
            DB::rollBack();
            $data['status'] = false;
            return $this->sendResponseFalse($data, 'pengaduan failure updated.');

        }
    
    }

    public function deletepengaduan(Request $request)
    {
        $pengaduan = Pengaduan::where('id_user', $request->id_user)->where('id', $request->id)->first();
        $data=[];
        try {
            DB::beginTransaction();
  /*           if($pengasuhan->photo){
                $this->DeleteImage($pengasuhan->photo, config('app.documentImagePath').'/pengasuhan/');
            } */
            $pengaduan->user_deleted = $request->id_user;
            $pengaduan->save();
            $pengaduan->delete();
            DB::commit();
            $data['status']=true;
            return $this->sendResponse($data, 'pengaduan deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            $data['status']=false;
            return $this->sendResponseFalse($data, 'pengaduan deleted failure.');
        }
        return $this->sendResponse($result, 'pengaduan delete successfully.');
    }

    public function pengaduan($condition, $limit, $order, $dir)
    {
        return Pengaduan::join('users', 'users.id', '=', 'tb_pengaduan.id_user')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_pengaduan.created_at))as tanggal"),'users.name', 'tb_pengaduan.status', 'tb_pengaduan.pengaduan', 'tb_pengaduan.id as id', 'tb_pengaduan.*')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }

    public function tanggapan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'follow_up' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        $pengaduan = Pengaduan::where('id', $request->id)
                                ->first();
        if(!empty($pengaduan)){
            $pengaduan->user_follow_up=$request->id_user;
            $pengaduan->date_follow_up=date('Y-m-d H:i:s');
            $pengaduan->follow_up=$request->follow_up;
            $pengaduan->status=1;
            $pengaduan->save();
        }else{
            $data['status'] = false;
            return $this->sendResponseError($data, 'Data Not Found or Deleted');
        }
        $data['status'] = true;
        return $this->sendResponse($data, 'tanggapan success');
    }
}