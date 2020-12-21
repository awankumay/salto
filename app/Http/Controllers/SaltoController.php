<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Redirect,Response,File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Regencies;
use App\User;
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
        return view('salto.profile');
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
                                ->where('status', 0)
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


}
