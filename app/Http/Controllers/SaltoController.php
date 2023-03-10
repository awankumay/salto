<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Redirect,Response,File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Regencies;
use App\Provinces;
use App\User;
use App\Grade;
use App\Absensi;
use App\JurnalTaruna;
use App\SuratIzin;
use App\Suket;
use App\Prestasi;
use App\HukumanDinas;
use App\Traits\ImageTrait;
use App\Traits\Firebase;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class SaltoController extends Controller
{
    use ImageTrait;
    use Firebase;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getregencies(Request $request)
    {
        if ($request->ajax()) {
            $city = [];
            $data = Regencies::where('province_id', $request->province_id)->get();
            foreach ($data as $key => $value) {
                $city[] = ['id'=>$value['id'], 'text'=>$value['name']];
            }
            echo json_encode($city);
            return;
        }
    }

    public function editprofile()
    {
        $user = User::where('id', Auth::user()->id)->first();
        
        $data=['id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'phone'=>$user->phone,
                'whatsapp'=>$user->whatsapp,
                'alamat'=>$user->address,
                'sex_name'=>$user->sex==1 ? 'Laki-laki' : 'Perempuan',
                'sex'=>$user->sex,
                'photo'=>!empty($user->photo) ? url('/')."/storage/".config('app.userImagePath')."/".$user->photo : url('/').'/profile.png',
                'form'=>['name', 'email', 'phone', 'whatsapp', 'address', 'grade', 'sex', 'file', 'password', 'confirm-password']
                ];
        $grade      = Grade::where('id', $user->grade)->first();
        $keluarga   = User::keluargataruna($user->id);
        $provinces  = Provinces::where('id', $user->province_id)->first();
        $regencies  = Regencies::where('id', $user->regencie_id)->first();
        $data['show_grade']=false;
        $data['show_keluarga_asuh']=false;
        $data['keluarga_asuh'] = '';
        $data['grade_select']='';
        $data['grade']='';
        $data['grade_option'] = [];
        if($user->getRoleNames()[0]=='Taruna'){
            $data['grade_select']='';
            $data['grade']='';
            $data['grade_option'] = Grade::pluck('grade', 'id')->all();
            if(!empty($grade)){
                $data['show_grade']=true;
                $data['grade']=$grade->grade;
                $data['grade_select']=$grade->id;
            }
        }
        if(!empty($keluarga)){
            $data['show_keluarga_asuh']=true;
            $data['keluarga_asuh']=$keluarga->name;
          
        }
        $data['provinces']  = !empty($provinces) ? $provinces->name : null;
        $data['regencies']  = !empty($regencies) ? $regencies->name : null;
        return view('salto.profile', compact('data'));
    }

    public function setprofile(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $data = [];

        $data['success'] = false;
        $this->validate($request, 
        [ 
            'file' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'email' => "required|email|unique:users,email,{$user->id},id,deleted_at,NULL",
            'password' => 'same:confirm-password',
            'phone' => "required|numeric|unique:users,phone,{$user->id},id,deleted_at,NULL",
            'whatsapp' => "numeric|unique:users,whatsapp,{$user->id},id,deleted_at,NULL",
            'sex'=>'required',
            'address'=>'required',
            'name'=>'required'
        ]); 

        $image=false;
        if($request->file){
            $image = $this->UploadImage($request->file, config('app.userImagePath'));
            if($image==false){
                return $this->sendResponseError($data, 'failure upload image'); 
            }
            $this->DeleteImage($user->photo, config('app.userImagePath'));
        }
        try {
            if(!empty($image)){
                if($image!=false){
                    $request->request->add(['photo'=> $image]);
                }
            }
            $request->request->add(['user_updated'=> $user->id]);
            $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
            $input = $request->all();

            if(!empty($input['password'])){
                $input['password'] = Hash::make($input['password']);
            }else{
               Arr::forget($input, array('password', 'confirm-password'));
            }
            DB::beginTransaction();
                $user->update($input);
            DB::commit();
            \Session::flash('success','profile berhasil disimpan');
            return redirect()->route('editprofile');
        } catch (\Throwable $th) {
            @dd($th->getMessage());
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.userImagePath'));
                }
                \Session::flash('error','profile gagal disimpan');
                return redirect()->route('editprofile');
        }

    }

    public function gettaruna(Request $request)
    {
        $search =$request->get('search');
        $getTaruna = User::role('Taruna')->where('name', 'like', "%$search%")->get();
        
        if (!empty($getTaruna)) {
            $list = array();
            foreach ($getTaruna as $key => $row) {
                $list[$key]['id'] = $row->id;
                $list[$key]['text'] = $row->name; 
            }
            echo json_encode($list);
        } else {
            echo "hasil kosong";
        }
    }

    public function clockin(Request $request)
    {
        $validator = Validator::make($request->all(), 
                    [ 
                        'id_user' => 'required',
                        'file_clock_in' => 'required|mimes:jpg,jpeg,png|max:2048',
                    ]);   
 
        if ($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 422);                        
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
                    return response()->json(['messages'=>'Terjadi Kesalahan Server'], 500);   
                }
            }else{
                return response()->json(['messages'=>'Terjadi Kesalahan Server'], 500);
            }
              
            return response()->json([
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $file
            ]);
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
            return response()->json(['error'=>$validator->errors()], 422);                        
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
                    @dd($th);
                    DB::rollBack();
                    return response()->json(['messages'=>'Terjadi Kesalahan Server'], 500);   
                }
            }else{
                return response()->json(['messages'=>'Terjadi Kesalahan Server'], 500);
            }
              
            return response()->json([
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $file
            ]);
        }
        
    }

    public function disposisisuratizin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
        }
        $suratIzin = SuratIzin::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $suratIzin->user_disposisi=$request->id_user;
        $suratIzin->date_disposisi=date('Y-m-d H:i:s');
        $suratIzin->reason_disposisi=$request->reason;
        $suratIzin->status_disposisi=$request->status;
        $suratIzin->save();

        $data['status'] = true;
        $keluarga       = User::keluargataruna($suratIzin->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$suratIzin->id_user, 'keluarga_asuh'=>$keluarga_asuh];
        
        $topic = User::topic('disposisisurat', $dataFirebase);
        if(!empty($topic)){
            set_time_limit(60);
            for ($i=0; $i < count($topic); $i++) { 
                $paramsFirebase=['title'=>'Pemberitahuan disposisi perizinan baru',
                'body'=>'perizinan baru telah diposisi',
                'page'=>'/riwayat-izin/detail/id/'.$request->id,
                'token'=>$topic[$i]];
                try {
                    $firebase = $this->pushNotif($paramsFirebase);
                    $data['firebase'][$i] = $firebase;
                } catch (\Throwable $th) {
                    $data['firebase'] = $th->getMessage();
                }
                sleep(1);
            }
        }
        
        return response()->json([
            "success" => true,
            "message" => "Disposisi berhasil",
            "data" => $data
        ]);
    }

    public function disposisisuket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
        }
        $suket = Suket::where('id', $request->id)
                ->where('status', 0)
                ->first();
        $suket->user_disposisi=$request->id_user;
        $suket->date_disposisi=date('Y-m-d H:i:s');
        $suket->reason_disposisi=$request->reason;
        $suket->status_disposisi=$request->status;
        $suket->save();

        $data['status'] = true;
        $data['firebase'] = false;
        $keluarga       = User::keluargataruna($suket->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$suket->id_user, 'keluarga_asuh'=>$keluarga_asuh];

        $topic = User::topic('disposisisurat', $dataFirebase);
        if(!empty($topic)){
            set_time_limit(60);
            for ($i=0; $i < count($topic); $i++) { 
                $paramsFirebase=['title'=>'Pemberitahuan disposisi surat keterangan baru',
                'body'=>'surat keterangan baru telah diposisi',
                'page'=>'/suket/detail/id/'.$request->id,
                'token'=>$topic[$i]];
                try {
                    $firebase = $this->pushNotif($paramsFirebase);
                    $data['firebase'][$i] = $firebase;
                } catch (\Throwable $th) {
                    $data['firebase'] = $th->getMessage();
                }
                sleep(1);
            }
        }
       
        
        return response()->json([
            "success" => true,
            "message" => "Disposisi berhasil",
            "data" => $data
        ]);
    }

    public function disposisiprestasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
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
        $data['firebase'] = false;
        $keluarga       = User::keluargataruna($prestasi->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$prestasi->id_user, 'keluarga_asuh'=>$keluarga_asuh];

        $topic = User::topic('disposisisurat', $dataFirebase);
        if(!empty($topic)){
            set_time_limit(60);
            for ($i=0; $i < count($topic); $i++) { 
                $paramsFirebase=['title'=>'Pemberitahuan disposisi prestasi',
                'body'=>'prestasi telah diposisi',
                'page'=>'/prestasi/detail/id/'.$request->id,
                'token'=>$topic[$i]];
                try {
                    $firebase = $this->pushNotif($paramsFirebase);
                    $data['firebase'][$i] = $firebase;
                } catch (\Throwable $th) {
                    $data['firebase'] = $th->getMessage();
                }
                sleep(1);
            }
        }
       
        
        return response()->json([
            "success" => true,
            "message" => "Disposisi berhasil",
            "data" => $data
        ]);
    }

    public function approvesuratizin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
        }
        $suratIzin = SuratIzin::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();
        
        $keluarga       = User::keluargataruna($suratIzin->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$suratIzin->id_user, 'keluarga_asuh'=>$keluarga_asuh];

        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan'){
            $suratIzin->user_approve_level_1=$request->id_user;
            $suratIzin->date_approve_level_1=date('Y-m-d H:i:s');
            $suratIzin->status_level_1=$request->status;
            $suratIzin->reason_level_1=$request->reason;

            if(strtotime(date_format(date_create($suratIzin->end), 'Y-m-d')) == strtotime(date_format(date_create($suratIzin->start), 'Y-m-d'))){
                $suratIzin->status=$request->status;
                $suratIzin->save();
                $topic  = User::topic('approve-direktur', $dataFirebase);
                if(!empty($topic)){
                    set_time_limit(60);
                    for ($i=0; $i < count($topic); $i++) { 
                        $paramsFirebase=['title'=>'Pemberitahuan persetujuan perizinan baru',
                        'body'=>'perizinan baru telah ditindaklanjuti aak',
                        'page'=>'/riwayat-izin/detail/id/'.$request->id,
                        'token'=>$topic[$i]];
                        try {
                            $firebase = $this->pushNotif($paramsFirebase);
                            $data['firebase'][$i] = $firebase;
                        } catch (\Throwable $th) {
                            $data['firebase'] = $th->getMessage();
                        }
                        sleep(1);
                    }
                }
            }else{
                $topic  = User::topic('approve-aak', $dataFirebase);
                if(!empty($topic)){
                    set_time_limit(60);
                    for ($i=0; $i < count($topic); $i++) { 
                        $paramsFirebase=['title'=>'Pemberitahuan persetujuan perizinan baru',
                        'body'=>'perizinan baru telah ditindaklanjuti oleh aak',
                        'page'=>'/riwayat-izin/detail/id/'.$request->id,
                        'token'=>$topic[$i]];
                        try {
                            $firebase = $this->pushNotif($paramsFirebase);
                            $data['firebase'][$i] = $firebase;
                        } catch (\Throwable $th) {
                            $data['firebase'] = $th->getMessage();
                        }
                        sleep(1);
                    }
                }

            }
            $suratIzin->save();
        }
        if($getUser->getRoleNames()[0]=='Direktur' || $getUser->getRoleNames()[0]=='Super Admin'){
            $suratIzin->user_approve_level_2=$request->id_user;
            $suratIzin->date_approve_level_2=date('Y-m-d H:i:s');
            $suratIzin->status_level_2=$request->status;
            $suratIzin->reason_level_2=$request->reason;
            $suratIzin->status=$request->status;
            $suratIzin->save();
        }
        $data['status'] = true;
        $data['firebase'] = false;
        $topic  = User::topic('approve-direktur', $dataFirebase);
                    if(!empty($topic)){
                        set_time_limit(60);
                        for ($i=0; $i < count($topic); $i++) { 
                            $paramsFirebase=['title'=>'Pemberitahuan persetujuan perizinan baru',
                            'body'=>'perizinan baru telah ditindaklanjuti direktur',
                            'page'=>'/riwayat-izin/detail/id/'.$request->id,
                            'token'=>$topic[$i]];
                            try {
                                $firebase = $this->pushNotif($paramsFirebase);
                                $data['firebase'][$i] = $firebase;
                            } catch (\Throwable $th) {
                                $data['firebase'] = $th->getMessage();
                            }
                            sleep(1);
                        }
                    }
        return response()->json([
            "success" => true,
            "message" => "Persetujuan berhasil",
            "data" => $data
        ]);
    }

    public function approvesuket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
        }
        
        $suket = Suket::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();

        $keluarga       = User::keluargataruna($suket->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$suket->id_user, 'keluarga_asuh'=>$keluarga_asuh];
        
        $data['firebase'] = false;

        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan'){
            $suket->user_approve_level_1=$request->id_user;
            $suket->date_approve_level_1=date('Y-m-d H:i:s');
            $suket->status_level_1=$request->status;
            $suket->reason_level_1=$request->reason;
            $suket->save();
            $data['status'] = true;

            $topic  = User::topic('approve-aak', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan persetujuan surat keterangan baru',
                    'body'=>'surat keterangan baru telah disetuji oleh aak',
                    'page'=>'/suket/detail/id/'.$request->id,
                    'token'=>$topic[$i]];
                    try {
                        $firebase = $this->pushNotif($paramsFirebase);
                        $data['firebase'][$i] = $firebase;
                    } catch (\Throwable $th) {
                        $data['firebase'] = $th->getMessage();
                    }
                    sleep(1);
                }
            }

            return response()->json([
                "success" => true,
                "message" => "Persetujuan berhasil",
                "data" => $data
            ]);
        }

        if($getUser->getRoleNames()[0]=='Direktur' || $getUser->getRoleNames()[0]=='Super Admin'){
            $suket->user_approve_level_2=$request->id_user;
            $suket->date_approve_level_2=date('Y-m-d H:i:s');
            $suket->status_level_2=$request->status;
            $suket->reason_level_2=$request->reason;
            $suket->status=$request->status;
            $suket->save();
            $data['status'] = true;
            $topic  = User::topic('approve-direktur', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan persetujuan surat keterangan baru',
                    'body'=>'surat keterangan baru telah disetuji oleh direktur',
                    'page'=>'/suket/detail/id/'.$request->id,
                    'token'=>$topic[$i]];
                    try {
                        $firebase = $this->pushNotif($paramsFirebase);
                        $data['firebase'][$i] = $firebase;
                    } catch (\Throwable $th) {
                        $data['firebase'] = $th->getMessage();
                    }
                    sleep(1);
                }
            }
            return response()->json([
                "success" => true,
                "message" => "Persetujuan berhasil",
                "data" => $data
            ]); 
            
        }
        $response = [
            'success' => false,
            'data'    => [],
            'message' => 'Persetujuan gagal',
        ];
        return response()->json($response, 500); 
    }

    public function approveprestasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
        }
        
        $prestasi = Prestasi::where('id', $request->id)
                                ->where('status', 0)
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();

        $keluarga       = User::keluargataruna($prestasi->id_user);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$prestasi->id_user, 'keluarga_asuh'=>$keluarga_asuh];
        
        $data['firebase'] = false;

        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan' || $getUser->getRoleNames()[0]=='Super Admin'){
            $prestasi->user_approve_level_1=$request->id_user;
            $prestasi->date_approve_level_1=date('Y-m-d H:i:s');
            $prestasi->status_level_1=$request->status;
            $prestasi->status=$request->status;
            $prestasi->reason_level_1=$request->reason;
            $prestasi->save();
            $data['status'] = true;

            $topic  = User::topic('approve-aak', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan persetujuan prestasi',
                    'body'=>'prestasi telah disetuji oleh aak',
                    'page'=>'/prestasi/detail/id/'.$request->id,
                    'token'=>$topic[$i]];
                    try {
                        $firebase = $this->pushNotif($paramsFirebase);
                        $data['firebase'][$i] = $firebase;
                    } catch (\Throwable $th) {
                        $data['firebase'] = $th->getMessage();
                    }
                    sleep(1);
                }
            }

            return response()->json([
                "success" => true,
                "message" => "Persetujuan berhasil",
                "data" => $data
            ]);
        }

        $response = [
            'success' => false,
            'data'    => [],
            'message' => 'Persetujuan gagal',
        ];
        return response()->json($response, 500); 
    }

    public function approvehukdis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'status' => 'required',
            'id'=>'required'
        ]);
        $data['status']=false;
        $data['firebase']=false;
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json($response, 422);                     
        }
        
        $hukdis = HukumanDinas::where('id', $request->id)
                                ->where('status', '0')
                                ->first();
        $getUser = User::where('id', $request->id_user)->first();

        $keluarga       = User::keluargataruna($hukdis->id_taruna);
        $keluarga_asuh  = !empty($keluarga) ? strtolower($keluarga->name) : null;
        $dataFirebase   = [];
        $dataFirebase   = ['id'=>$hukdis->id_taruna, 'keluarga_asuh'=>$keluarga_asuh];
        
        $data['firebase'] = false;

        if($getUser->getRoleNames()[0]=='Akademik dan Ketarunaan' || $getUser->getRoleNames()[0]=='Super Admin'){
            $hukdis->user_approve_level_1=$request->id_user;
            $hukdis->date_approve_level_1=date('Y-m-d H:i:s');
            $hukdis->status_level_1=$request->status;
            $hukdis->status=$request->status;
            $hukdis->reason_level_1=$request->reason;
            $hukdis->save();
            $data['status'] = true;

            $topic  = User::topic('approve-aak', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan persetujuan hukuman disiplin',
                    'body'=>'hukuman disiplin telah disetuji oleh aak',
                    'page'=>'/hukdis/detail/id/'.$request->id,
                    'token'=>$topic[$i]];
                    try {
                        $firebase = $this->pushNotif($paramsFirebase);
                        $data['firebase'][$i] = $firebase;
                    } catch (\Throwable $th) {
                        $data['firebase'] = $th->getMessage();
                    }
                    sleep(1);
                }
            }

            return response()->json([
                "success" => true,
                "message" => "Persetujuan berhasil",
                "data" => $data
            ]);
        }

        $response = [
            'success' => false,
            'data'    => [],
            'message' => 'Persetujuan gagal',
        ];
        return response()->json($response, 500); 
    }

    public function cetaksurat(Request $request){
        $data   = ['id'=>$request->id, 'id_user'=>$request->id_user, 'cetak'=>$request->cetak];
        $res    = [];
        $data   = SuratIzin::tmpreport($data);
        
        $res['link'] = $data; 
        $res['status'] = true;
        return response()->json([
            "success" => true,
            "message" => "Link generate berhasil",
            "data" => $res
        ]);
    }

    public function inputjurnal(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $data = [];
        $validator = Validator::make($request->all(), 
                    [ 
                        'id_user' => 'required',
                        'kegiatan' => 'required',
                        'start_time' => 'required',
                        'end_time' => 'required',
                    ]);   
 
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data'    => [],
                'message' => ['error'=>$validator->errors()],
            ];
            return response()->json(['error'=>$validator->errors()], 422);                     
        }

        $getUser = User::where('id', $request->id_user)->first();
        
        if($request->id){
            try {
                DB::beginTransaction();
                $jurnal = JurnalTaruna::where('id', $request->id)->first();
                $input = $request->all();
                Arr::forget($input, array('start_time', 'end_time'));
                $input['start_time'] = date_create(date('Y-m-d').' '.$request->start_time);
                $input['end_time'] = date_create(date('Y-m-d').' '.$request->end_time);
                //$input['updated_at'] = date('Y-m-d H:i:s');
                $input['status'] = 0;
                $input['grade'] = !empty($getUser->grade) ? $getUser->grade : null;
                $jurnal->update($input);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                $response = [
                    'success' => false,
                    'data'    => [],
                    'message' => 'Terjadi Kesalahan Server',
                ];
                return response()->json($response, 500);
            }
        }else{
            try {
                DB::beginTransaction();
                $input = $request->all();
                Arr::forget($input, array('start_time', 'end_time'));
                $input['start_time'] = date_create(date('Y-m-d').' '.$request->start_time);
                $input['end_time'] = date_create(date('Y-m-d').' '.$request->end_time);
                //$input['created_at'] = date('Y-m-d H:i:s');
                $input['status'] = 0;
                $input['tanggal'] = date('Y-m-d');
                $input['grade'] = !empty($getUser->grade) ? $getUser->grade : null;
                JurnalTaruna::create($input);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                $response = [
                    'success' => false,
                    'data'    => [],
                    'message' => 'Terjadi Kesalahan Server',
                ];
                return response()->json($response, 500);
            }
        }

        return response()->json([
            "success" => true,
            "message" => "Data berhasil disimpan",
            "data" => []
        ]);
        
    }

    public function totaluser()
    {
        $taruna =  DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                    ->select('users.id', 'users.name')
                    ->where('model_has_roles.role_id', 7)
                    ->whereNull('users.deleted_at')
                    ->where('users.status', 1)
                    ->count();

        $orangtua =  DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                    ->select('users.id', 'users.name')
                    ->where('model_has_roles.role_id', 8)
                    ->whereNull('users.deleted_at')
                    ->where('users.status', 1)
                    ->count();

        $waliasuh =  DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                    ->select('users.id', 'users.name')
                    ->where('model_has_roles.role_id', 4)
                    ->whereNull('users.deleted_at')
                    ->where('users.status', 1)
                    ->count();

        $pembina =  DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                    ->select('users.id', 'users.name')
                    ->where('model_has_roles.role_id', 5)
                    ->whereNull('users.deleted_at')
                    ->where('users.status', 1)
                    ->count();
        
        

        return response()->json([
            "success" => true,
            "data" => ['total_taruna'=>$taruna, 'total_orang_tua'=>$orangtua, 'total_waliasuh'=>$waliasuh, 'total_pembina'=>$pembina]
        ]);
    }

    public function totalSurat()
    {
              
        $suratTotal = DB::table('surat_header')
                        ->count();

        $suratPending = DB::table('surat_header')
                        ->where('surat_header.status', 0)
                        ->count();
        
        $suratDateNow = DB::table('surat_header')
                        ->whereDate('created_at','like',Carbon::now())
                        ->count();



        return response()->json([
            "success" => true,
            "data" => ['total_surat'=> $suratTotal,'total_suratPending'=>$suratPending, 'total_suratDateNow'=>$suratDateNow ]
        ]);
    }

    public function absen(Request $request)
    {
        
        $taruna =  DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                    ->select('users.id', 'users.name')
                    ->where('model_has_roles.role_id', 7)
                    ->whereRaw('users.id not in('.$id.')')
                    ->whereNull('users.deleted_at')
                    ->get();
    }

    public function exportdata(Request $request)
    {
        switch ($request->data) {
            case 'absensi':
                return $this->exportAbsensi($request->date_1, $request->date_2);
                break;
            // case 'prestasi':
            //     return $this->exportPrestasi($request->date_1, $request->date_2);
            //     break;
            case 'surat-izin':
                return $this->exportSuratIzi($request->date_1, $request->date_2);
                break;
            
            default:
                return redirect()->back();;
                break;
        }
    }


    public function exportSuratIzi($date_1, $date_2)
    {
        $currentUser = Auth::user();
        $data        = [];
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            // Kosong
            $data = SuratIzin::Join('users','surat_header.id_user','=','users.id')
                    ->Join('opsi_persetujuan','opsi_persetujuan.kode_opsi','=','surat_header.status')
                    ->Join('menu_persetujuan','menu_persetujuan.id','=','surat_header.id_category')
                    ->select('users.name','menu_persetujuan.nama_menu','opsi_persetujuan.opsi','surat_header.created_at')
                    ->whereDate('surat_header.created_at', '>=', $date_1)
                    ->whereDate('surat_header.created_at', '<=', $date_2)
                    ->orderBy('surat_header.created_at', 'DESC')
                    ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            // Kosong
            $data = SuratIzin::Join('users','surat_header.id_user','=','users.id')
                    ->Join('opsi_persetujuan','opsi_persetujuan.kode_opsi','=','surat_header.status')
                    ->Join('menu_persetujuan','menu_persetujuan.id','=','surat_header.id_category')
                    ->where('surat_header.id_user', $currentUser->id)
                    ->select('users.name','menu_persetujuan.nama_menu','opsi_persetujuan.opsi','surat_header.created_at')
                    ->whereDate('surat_header.created_at', '>=', $date_1)
                    ->whereDate('surat_header.created_at', '<=', $date_2)
                    ->orderBy('surat_header.created_at', 'DESC')
                    ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            $data = SuratIzin::Join('users','surat_header.id_user','=','users.id')
                    ->Join('opsi_persetujuan','opsi_persetujuan.kode_opsi','=','surat_header.status')
                    ->Join('menu_persetujuan','menu_persetujuan.id','=','surat_header.id_category')
                    ->Join('taruna_keluarga_asuh','taruna_keluarga_asuh.taruna_id','=','id_user')
                    ->Join('waliasuh_keluarga_asuh','waliasuh_keluarga_asuh.keluarga_asuh_id','=','taruna_keluarga_asuh.keluarga_asuh_id')
                    ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                    ->select('users.name','menu_persetujuan.nama_menu','opsi_persetujuan.opsi','surat_header.created_at')
                    ->whereDate('surat_header.created_at', '>=', $date_1)
                    ->whereDate('surat_header.created_at', '<=', $date_2)
                    ->orderBy('surat_header.created_at', 'DESC')
                    ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            $data = SuratIzin::Join('users','surat_header.id_user','=','users.id')
                    ->Join('opsi_persetujuan','opsi_persetujuan.kode_opsi','=','surat_header.status')
                    ->Join('menu_persetujuan','menu_persetujuan.id','=','surat_header.id_category')
                    ->Join('orang_tua_taruna','orang_tua_taruna.taruna_id','=','id_user')
                    ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                    ->select('users.name','menu_persetujuan.nama_menu','opsi_persetujuan.opsi','surat_header.created_at')
                    ->whereDate('surat_header.created_at', '>=', $date_1)
                    ->whereDate('surat_header.created_at', '<=', $date_2)
                    ->orderBy('surat_header.created_at', 'DESC')
                    ->get();
            
        }
        $report=[];
        foreach ($data as $key => $value) {
            $report['body'][]=['no'=>$key+1, 'name'=>$value->name, 'jenis_izin'=>$value->nama_menu, 'status_izin'=>$value->opsi, 'create_date'=>$value->created_at];
        }
        if(!empty($data)){
            $report['judul']    = 'Laporan Surat Izin Periode '.date('d-m-Y', strtotime($date_1)). ' - '.date('d-m-Y', strtotime($date_2));
            $report['header']   = ['No', 'Nama', 'Jenis Izin', 'Status', 'Dibuat',];
            $report['template'] = 1;
            
            $data   = $report;
            return view('cetakreportizin', compact('data'));
            
        }
        // dd($data);
        return $data;
    }
    public function exportAbsensi($date_1, $date_2)
    {
       
        $currentUser = Auth::user();
        $data        = [];
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            $data = User::leftJoin('absensi_taruna', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                            ->select('users.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->whereDate('absensi_taruna.created_at','<=', $date_2)
                            ->whereDate('absensi_taruna.created_at','>=', $date_1)
                            ->where('users.status','=', '1')
                            ->where('model_has_roles.role_id', 7)
                            ->orWhere(function($q) {
                                $q->whereNull('absensi_taruna.created_at')
                                ->where('model_has_roles.role_id', 7)
                                ->where('users.status','=', '1');
             
                            })
                            ->orderBy('absensi_taruna.created_at', 'DESC')
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            $data = User::leftJoin('absensi_taruna', 'users.id', '=', 'absensi_taruna.id_user')
                            ->where('users.id', $currentUser->id)
                            ->select('users.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->whereDate('absensi_taruna.created_at','<=', $date_2)
                            ->whereDate('absensi_taruna.created_at','>=', $date_1)
                            ->orderBy('absensi_taruna.created_at', 'DESC')
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            $data = User::leftJoin('absensi_taruna', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->select('users.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->whereDate('absensi_taruna.created_at','<=', $date_2)
                            ->whereDate('absensi_taruna.created_at','>=', $date_1)
                            ->orderBy('absensi_taruna.created_at', 'DESC')
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            $data = User::leftJoin('absensi_taruna', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('orang_tua_taruna', 'orang_tua_taruna.taruna_id', '=', 'absensi_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('users.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->whereDate('absensi_taruna.created_at','<=', $date_2)
                            ->whereDate('absensi_taruna.created_at','>=', $date_1)
                            ->orderBy('absensi_taruna.created_at', 'DESC')
                            ->get();
        }
        $report=[];
        foreach ($data as $key => $value) {
            $report['body'][]=['no'=>$key+1, 'nama'=>$value->name, 'stb'=>$value->stb, 'in'=>$value->clock_in, 'out'=>$value->clock_out];
        }
        if(!empty($data)){
            $report['judul']    = 'Laporan Absensi Periode '.date('d-m-Y', strtotime($date_1)). ' - '.date('d-m-Y', strtotime($date_2));
            $report['header']   = ['No', 'Nama', 'STB', 'Clock In', 'Clock Out',];
            $report['template'] = 1;
            
            $data   = $report;
            return view('cetakabsensi', compact('data'));
            
        }
        return $data;
    }

}
