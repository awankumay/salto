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
use App\WBS;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class WBSController extends BaseController
{
    use ImageTrait;
    
    public function getwbs(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'tb_wbs.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_wbs.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        if($order=='status'){
            $order='tb_wbs.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_wbs.id';
        }

        $permission = [];
        if($lastId==0){
            if($roleName=='Admin' || $roleName=='Super Admin'){
                $condition  = 'tb_wbs.id is not null';
                $total      =  WBS::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->wbs($condition, $limit, $order, $dir);
            }else{
                $condition  = 'tb_wbs.id_user='.$id_user;
                $total      =  WBS::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->wbs($condition, $limit, $order, $dir);
            }
        }else {
            if($roleName=='Admin' || $roleName=='Super Admin'){
                $condition  = 'tb_wbs.id is not null AND tb_wbs.id '.$diff.' '.$lastId.'';
                $total      =  WBS::whereRaw('tb_wbs.id is not null')
                                ->count();  
                $count = WBS::whereRaw($condition)->count();
                $data = $this->wbs($condition, $limit, $order, $dir);
            }else{
                $condition  = 'tb_wbs.id_user='.$id_user.' AND tb_wbs.id '.$diff.' '.$lastId.'';
                $total      =  WBS::whereRaw('tb_wbs.id_user='.$id_user)
                                ->count();  
                $count = WBS::whereRaw($condition)->count();
                $data = $this->wbs($condition, $limit, $order, $dir);
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

            $result['wbs'][]= [ 
                'id'=>$value->id,
                'id_user'=>$value->id_user,
                'nama'=>$value->name,
                'ewhat'=>substr($value->ewhat, 0, 30).'...',
                'created_at'=>date('Y-m-d', strtotime($getSurat->created_at)),
                'created_at_bi'=>date('d-m-Y', strtotime($getSurat->created_at)),
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
        return $this->sendResponse($result, 'wbs load successfully.');
    }

    public function wbsdetail(Request $request)
    {
        $id   = $request->id;
        $getSurat = WBS::join('users as author', 'author.id', '=', 'tb_wbs.id_user')
                                ->leftjoin('users as admin', 'admin.id', '=', 'tb_wbs.user_follow_up')
                                ->leftjoin('materi_wbs as materi', 'materi.id', '=', 'tb_wbs.materi')
                                    ->select('tb_wbs.id as id', 
                                            'tb_wbs.id_user as id_user',
                                            'author.name as nama',
                                            'tb_wbs.materi as materi',
                                            'materi.nama_materi as materi_name',
                                            'tb_wbs.ewhat as ewhat',
                                            'tb_wbs.ewho as ewho',
                                            'tb_wbs.ewhere as ewhere',
                                            'tb_wbs.ewhen as ewhen',
                                            'tb_wbs.ewhy as ewhy',
                                            'tb_wbs.ehow as ehow',
                                            'tb_wbs.created_at as created_at',
                                            'tb_wbs.status as status',
                                            'tb_wbs.follow_up as tanggapan',
                                            'admin.name as user_follow_up',
                                            'tb_wbs.status as status',
                                            'tb_wbs.user_created as user_created',
                                            'tb_wbs.date_follow_up as date_follow_up'
                                            )
                                    ->where('tb_wbs.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseError($data, 'Pengaduan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'name'=>$getSurat->nama,
            'materi'=>$getSurat->materi,
            'materi_name'=>$getSurat->materi_name,
            'ewhat'=>$getSurat->ewhat,
            'ewho'=>$getSurat->ewho,
            'ewhere'=>$getSurat->ewhere,
            'ewhen'=>$getSurat->ewhen,
            'ewhy'=>$getSurat->ewhy,
            'ehow'=>$getSurat->ehow,
            'tanggapan'=>$getSurat->tanggapan,
            'created_at'=>date('Y-m-d', strtotime($getSurat->created_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->created_at)),
            'status'=>$getSurat->status,
            'status_name'=>$getSurat->status==1 ? 'Selesai' : 'Proses',
            'user_follow_up'=>$getSurat->user_follow_up,
            'end_time'=>$getSurat->end_time,
            'date_follow_up'=>!empty($getSurat->date_follow_up) ? date('Y-m-d H:i', strtotime($getSurat->date_follow_up)) : null,
            'created_at_bi'=>date('d-m-Y H:i', strtotime($getSurat->created_at)),
            'form'=>['id_user', 'materi', 'ewhat', 'ewho', 'ewhere', 'ewhy', 'ewhen', 'ehow'],
            'follow_up'=>false
        );
        $data['permission']=[];
        if($roleName=='Admin' || $roleName=='Super Admin') {
            $data['follow_up']=true;
        }

        return $this->sendResponse($data, 'Wbs load successfully.');
    }

    public function inputwbs(Request $request)
    {
        if(!empty($request->id)){
            return $this->updatewbs($request);
        }else {
            return $this->savewbs($request);
        }
    }

    public function category()
    {
        $data = DB::table('materi_wbs')->orderBy('nama_materi', 'ASC')->get();
        return $this->sendResponse($data, 'Wbs load successfully.');
    }

    public function savewbs($request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_materi' =>'required',
            'ewhy'=>'required',
            'ewhat'=>'required',
            'ewho'=>'required',
            'ewhere'=>'required',
            'ewhen'=>'required',
            'ewhy'=>'required',
            'ehow'=>'required'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        try {
            DB::beginTransaction();
                $getmateri = DB::table('materi_wbs')->where('id', $request->id_materi)->first();
                $request->request->add(['materi'=>$getmateri->nama_materi]);
                $request->request->add(['user_created'=> $request->id_user]);
                $request->request->add(['user_updated'=> $request->id_user]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                WBS::create($input);

            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'wbs create successfully.');
        } catch (\Throwable $th) {
            //@dd($th->getMessage());
            DB::rollBack();
            $data['status'] = false;
            return $this->sendResponseError($data, 'wbs create failure.');
        }

    }

    public function updatewbs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_materi' =>'required',
            'ewhy'=>'required',
            'ewhat'=>'required',
            'ewho'=>'required',
            'ewhere'=>'required',
            'ewhen'=>'required',
            'ewhy'=>'required',
            'ehow'=>'required'
        ]);
        $data=[];
        $data['status'] = false;
        if ($validator->fails()) {
            return $this->sendResponseFalse($data, ['error'=>$validator->errors()]);                            
        }
        try {

            DB::beginTransaction();
            $wbs = WBS::where('id_user', $request->id_user)->where('id', $request->id)->first();
            if(empty($wbs)){
                return $this->sendResponseError($data, 'data not found');   
            }
            $getmateri = DB::table('materi_wbs')->where('id', $request->id_materi)->first();
            $request->request->add(['materi'=>$getmateri->nama_materi]);
            $request->request->add(['user_updated'=> $request->id_user]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);

            $input = $request->all();

            $wbs->update($input);
            DB::commit();
            $data['status'] = true;
            return $this->sendResponse($data, 'wbs updated successfully.');
        } catch (\Throwable $th) {
            //@dd($th->getMessage());
            DB::rollBack();
            $data['status'] = false;
            return $this->sendResponseError($data, 'wbs failure updated.');

        }
    
    }

    public function deletewbs(Request $request)
    {
        $wbs = WBS::where('id_user', $request->id_user)->where('id', $request->id)->first();
        $data=[];
        try {
            DB::beginTransaction();
  /*           if($pengasuhan->photo){
                $this->DeleteImage($pengasuhan->photo, config('app.documentImagePath').'/pengasuhan/');
            } */
            $wbs->user_deleted = $request->id_user;
            $wbs->save();
            $wbs->delete();
            DB::commit();
            $data['status']=true;
            return $this->sendResponse($data, 'wbs deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            $data['status']=false;
            return $this->sendResponseError($data, 'wbs deleted failure.');
        }
        return $this->sendResponse($result, 'wbs delete successfully.');
    }

    public function wbs($condition, $limit, $order, $dir)
    {
        return WBS::join('users', 'users.id', '=', 'tb_wbs.id_user')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_wbs.created_at))as tanggal"),'users.name', 'tb_wbs.status', 'tb_wbs.pengaduan', 'tb_wbs.id as id', 'tb_wbs.*')
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
        $wbs = WBS::where('id', $request->id)
                                ->first();
        if(!empty($wbs)){
            $wbs->user_follow_up=$request->id_user;
            $wbs->date_follow_up=date('Y-m-d H:i:s');
            $wbs->follow_up=$request->follow_up;
            $wbs->status=1;
            $wbs->save();
        }else{
            $data['status'] = false;
            return $this->sendResponseError($data, 'Data Not Found or Deleted');
        }
        $data['status'] = true;
        return $this->sendResponse($data, 'tanggapan success');
    }
}