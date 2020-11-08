<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Content;
use App\Grade;
use App\Provinces;
use App\Regencies;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Absensi;
use App\JurnalTaruna;
use App\Traits\ImageTrait;

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
            return $this->sendResponse($result, 'berhasil clock in');
        }
        
    }

}
