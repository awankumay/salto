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
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class SaltoController extends Controller
{
    use ImageTrait;
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
                        $absensi = new Absensi();
                        $absensi->id_user = $request->id_user;
                        $absensi->clock_in = date('Y-m-d H:i:s');
                        $absensi->file_clock_in = $file;
                        $absensi->created_at = date('Y-m-d H:i:s');
                        $absensi->save();
                        $jurnal = New JurnalTaruna();
                        $jurnal->id_user = $request->id_user;
                        $jurnal->tanggal = date('Y-m-d');
                        $jurnal->kegiatan = 'Clock In / Apel Pagi';
                        $jurnal->status = 0;
                        $jurnal->start = date('Y-m-d H:i:s');
                        $jurnal->end = date('Y-m-d H:i:s');
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
                    DB::beginTransaction();
                        $absensi->file_clock_out = $file;
                        $absensi->clock_out = date('Y-m-d H:i:s');
                        $absensi->updated_at = date('Y-m-d H:i:s');
                        $absensi->update();
                        $jurnal->id_user = $request->id_user;
                        $jurnal->tanggal = date('Y-m-d');
                        $jurnal->kegiatan = 'Clock Out / Apel Malam';
                        $jurnal->status = 1;
                        $jurnal->start = date('Y-m-d H:i:s');
                        $jurnal->end = date('Y-m-d H:i:s');
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
}
