<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Content;
use App\SurveyIkm;
use Illuminate\Support\Facades\Auth;
use Validator;
   
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

    public function getData(Request $request){
        
        //$success['link']=url()->current();
        if($request->page=='home'){
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
        }else if($request->page=='kunjungan'){
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
        }else if($request->page=='integrasi'){
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
        }
        else if($request->page=='remisi'){
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
            $datapage = \App\Content::where('post_categories_id', 1)->where('status', 1)->get();
        
        }else if($request->page=='informasi'){
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
            $datapage = \App\Content::where('post_categories_id', 3)->where('status', 1)->get();
        }else if($request->page=='pengaduan'){
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
        }else{
            $image = ['/storage/images/slider/slider-1.png',
            '/storage/images/slider/slider-2.png',
            '/storage/images/slider/slider-3.png',
            ];
        }
        $success['slider']=$image;
        $success['datapage']=$datapage;
        return $this->sendResponse($success, 'User login successfully.');
    }

    public function surveyikm(Request $request)
    {
        $surveyIkm = New SurveyIkm();
        if(!empty($request->user_id) && !empty($request->rating)){
            $surveyIkm->users_id=$request->user_id;
            $surveyIkm->rating=$request->rating;
            $success=false;
            if($surveyIkm->save()){
                $success=true;
                return $this->sendResponse($success, 'Terima kasih telah mengisi survey');
            }else{
                $success=false;
                return $this->sendResponseFalse($success, 'Terjadi kesalahan server');
            }

        }
        $success=false;
        return $this->sendResponseFalse($success, 'Parameter tidak ada');
    }
    
}