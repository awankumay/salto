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

    public function getsliderdetail(Request $request)
    {
        $data = Content::where('status', 1)->where('headline', 1)->where('id', $request->id)->select('id','photo','title','content','created_at')->first();
        $data->photo = url('/')."/storage/".config('app.postImagePath')."/".$data->photo;
        return $this->sendResponse($data, 'detail slider load successfully.');

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
