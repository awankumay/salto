<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Content;
use App\SurveyIkm;
use App\Visit;
use App\TransHistory;
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
        $getExist = \App\SurveyIkm::where('users_id', $request->user_id)->first();
        if(!empty($getExist)){
            $success=false;
            return $this->sendResponse($success, 'Anda sudah mengisi survey');
        }
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

    public function visit(Request $request)
    {
        $visit = New Visit();
        $success=[];
        $getDate = date_create($request->date);
        $setDate = date_format($getDate, 'Y-m-d');
        $cekStatusNapi = \App\Convict::where('id', $request->convict_id)->where('type_convict', 1)->first();
        if(!empty($cekStatusNapi)){
            if(empty($cekStatusNapi->document)){
                $success['available']=false;
                return $this->sendResponse($success, 'mohon maaf surat izin belum ada, silahkan menghubungi operator');
            }
        }
            $existToday=DB::table('appointments')
            ->where('date', $setDate)
            ->where('convicts_id', $request->convict_id)->get();
            if(!empty($existToday)){
                $success['available']=false;
                return $this->sendResponse($success, 'anda sudah memilih jadwal ditanggal ini');
            } 
        try {
            DB::beginTransaction();

           
           $check=DB::table('appointments')->where('date', $setDate)
                                    ->where('schedule', $request->schedule)
                                    ->sharedLock()->get();

            if(!empty($check)){
                $countData = count($check);
            }else{
                $countData = 0;
            }
            if($countData>=10){
                $success['available']=false;
                DB::commit();
                return $this->sendResponse($success, 'jadwal penuh pilih yang lain');
            }else{
                $antrian = $countData+1;
                $visit->id_users        = $request->user_id;
                $visit->type            = $request->type;
                $visit->visitor_name    = $request->visitor_name;
                $visit->date            = $setDate;
                $visit->schedule        = $request->schedule;
                $visit->convicts_id     = $request->convict_id;
                $visit->status          = 1;
                $visit->no_antrian      = $antrian;
                $visit->save();
                DB::commit();
                if($request->type==1){
                    $success['visit_id']=$visit->id;
                    $success['convict_id']=$request->convict_id;
                }
                $success['available']=true;
                return $this->sendResponse($success, 'berhasil, anda segera dihubungi petugas.anda akan visit pada '. $request->date. ' antrian '. $antrian);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $success['available']=true;
            var_dump($th->getMessage());
            return $this->sendResponseFalse($success, 'terjadi kesalah server');
        }
    }

    public function getschedule(Request $request)
    {
        $id=$request->user_id;
        $specificDate = strtotime($request->date);
        $day = date('l', $specificDate);
        if($day=='Monday' || $day=='Tuesday' || $day=='Wednesday' || $day=='Thursday'){
            $success['jadwal']=['9-10', '10-11', '11-12'];
        }
        if($day=='Friday'){
            $success['jadwal']=['9-12', '13-15'];
        }
        if($day=='Sabtu'){
            $success['jadwal']=['9-12'];
        }
        $userConvict = DB::table('user_has_convicts')
            ->where('user_has_convicts.users_id', $id)
            ->leftJoin('convicts', 'user_has_convicts.convicts_id', '=', 'convicts.id')
            ->get();
        $success['napi']=$userConvict;
        if(empty($success['napi'])){
            return $this->sendResponse($success, 'silahkan hubungi petugas, tahanan belum ada');
        }
        return $this->sendResponse($success, 'get jadwal');
    }

    public function historykunjungan($id)
    {
        $history = DB::table('appointments')
        ->where('appointments.id_users', $id)
        ->leftJoin('convicts', 'appointments.convicts_id', '=', 'convicts.id')
        ->orderByDesc('appointments.created_at')
        ->limit(3)
        ->get();
        $success['histori']=$history;
        return $this->sendResponse($success, 'histori terakhir berkunjung');
    }

    public function product($id)
    {   
        $data=[];
        $product = DB::table('products')->select('*')->where('type', $id)->where('status', 1)->orderByDesc('products.id_categories')->get();
        foreach ($product as $key => $vals) {
            $data[]=array('id' => $vals->id,
                            'name'=> $vals->name,
                            'type'=>$vals->type,
                            'photo'=>$vals->photo,
                            'status'=>$vals->status,
                            'price'=>$vals->price,
                            'price_rp'=>number_format($vals->price,0,",","."),
                            'created_at'=>$vals->created_at,
                            'updated_at'=>$vals->updated_at,
                            'id_categories'=>$vals->id_categories
                            );
        }
        $userConvict = DB::table('user_has_convicts')
            ->where('user_has_convicts.users_id', $id)
            ->leftJoin('convicts', 'user_has_convicts.convicts_id', '=', 'convicts.id')
            ->get();
        $success['napi']=$userConvict;
        $success['product']=$data;
        return $this->sendResponse($success, 'daftar barang');
    }

    public function transaction(Request $request)
    {
        $success=[];
        $total_data = $request->totaldata;
        $data  = $request->formData;
        $setIdTrans = date("dmyhis").$request->user_id;
        //try {
               DB::beginTransaction();

                
                    for ($i=0; $i < $total_data; $i++) { 
                        if($request->formData['qty_'.$i.'']>0 && $request->formData['qty_'.$i.'']!='' && $request->formData['qty_'.$i.'']!=null){
                            $trans = New TransHistory();
                            $trans->users_id        = $request->user_id;
                            $trans->id_trans        = $setIdTrans;
                            $trans->id_product      = $request->formData['id_product_'.$i.''];
                            $trans->qty             = $request->formData['qty_'.$i.''];
                            $trans->type_product    = $request->formData['type_'.$i.''];
                            $trans->price           = $request->formData['price_'.$i.''];
                            $trans->convicts_id     = $request->convict_id;
                            $trans->status          = 1;
                            if(!empty($request->visit)){
                                $trans->id_visit      = $request->visit;
                            }
                            $trans->save();
                            DB::commit(); 
                        } 
                    }
                
                $success['available']=true;
                return $this->sendResponse($success, 'berhasil, anda segera dihubungi petugas. no transaksi '.$setIdTrans);
        //} catch (\Throwable $th) {
            //return $this->sendResponseFalse($success, 'terjadi kesalah server');
        //}
    }
    
}