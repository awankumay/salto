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

class LookController extends BaseController
{
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
                    'file'=> url('/')."/storage/".config('app.documentImagePath')."/".$value->file
                ];
            }else{
                $result['berita'][]= [ 
                    'id'=>$value->id,
                    'title'=>$value->title,
                    'excerpt'=>$value->excerpt,
                    'photo'=> url('/')."/storage/".config('app.postImagePath')."/".$value->photo
                ];
            }
        }

        if($count > $limit){
            $result['info']['lastId'] = $data[count($data)-1]->id;
            $result['info']['loadmore'] = true;
        }else{
            $result['info']['lastId'] = 0;
            $result['info']['loadmore'] = false;
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

}
