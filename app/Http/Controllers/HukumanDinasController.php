<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\HukumanDinas;
use App\User;
use App\WaliasuhKeluargaAsuh;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class HukumanDinasController extends Controller
{
    use ActionTableWithDetail;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:hukuman-dinas-list');
        $this->middleware('permission:hukuman-dinas-create', ['only' => ['create','store']]);
        $this->middleware('permission:hukuman-dinas-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:hukuman-dinas-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'stb',
                2=>'nama_taruna',
                3=>'grade_name',
                4=>'hukuman_taruna',
                5=>'tingkat',
                6=>'keterangan',
                7=>'start_time',
                8=>'end_time',
                9=>'status',
                10=>'created_at',
                11=>'nama_pembina'

            );
            $model  = New HukumanDinas();
            return $this->ActionTableWithDetail($columns, $model, $request, 'hukuman-dinas.edit', 'hukuman-dinas.show', 'hukuman-dinas-edit', 'hukuman-dinas-delete', 'hukuman-dinas-list');
        }
        return view('hukuman-dinas.index');
    }

    public function create()
    {
        $tingkat = ['1'=>'Ringan', '2'=>'Sedang', '3'=>'Berat'];
        return view('hukuman-dinas.create', compact('tingkat'));
        
    }

    public function show($id)
    {
        $getSurat = HukumanDinas::join('users as taruna', 'taruna.id', '=', 'tb_hukdis.id_taruna')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_hukdis.user_approve_level_1')
                                    ->leftjoin('users as pembina', 'pembina.id', '=', 'tb_hukdis.id_user')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_hukdis.grade')
                                    ->select('tb_hukdis.id as id', 
                                            'tb_hukdis.id_user as id_user',
                                            'tb_hukdis.id_taruna as id_taruna',
                                            'tb_hukdis.stb as stb',
                                            'taruna.name as nama_taruna',
                                            'pembina.name as nama_pembina',
                                            'tb_hukdis.photo as photo',
                                            'tb_hukdis.keterangan as keterangan',
                                            'tb_hukdis.tingkat as tingkat',
                                            'tb_hukdis.hukuman as hukuman',
                                            'tb_hukdis.start_time as start_time',
                                            'tb_hukdis.end_time as end_time',
                                            'tb_hukdis.status as status',
                                            'tb_hukdis.updated_at as updated_at',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_hukdis.date_approve_level_1 as date_approve_1',
                                            'tb_hukdis.reason_level_1 as user_reason_1',
                                            'tb_hukdis.status_level_1 as status_level_1',
                                            'grade.grade as grade'
                                            )
                                    ->where('tb_hukdis.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            \Session::flash('error', 'Data tidak ditemukan');
            return redirect()->route('hukuman-dinas.index'); 
        }
        $getUser = Auth::user();
        $roleName = $getUser->getRoleNames()[0];
        if($roleName=='Taruna' || $roleName=='OrangTua'){
            if($getUser->id!=$getSurat->id_taruna){
                return redirect()->route('hukuman-dinas.index'); 
            }
        }
        switch ($getSurat->tingkat) {
            case 1:
                $tingkat = 'Ringan';
                break;
            case 2:
                $tingkat = 'Sedang';
                break;
            case 3:
                $tingkat = 'Berat';
                break;
            
            default:
                $tingkat = 'Ringan';
                break;
        }

        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'id_taruna'=>$getSurat->id_taruna,
            'stb'=>$getSurat->stb,
            'nama_taruna'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'keterangan'=>$getSurat->keterangan,
            'tingkat'=>$getSurat->tingkat,
            'tingkat_name'=>$tingkat,
            'hukuman'=>$getSurat->hukuman,
            'start_time'=>date('Y-m-d H:i', strtotime($getSurat->start_time)),
            'end_time'=>date('Y-m-d H:i', strtotime($getSurat->end_time)),
            'start_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->start_time)),
            'end_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->end_time)),
            'nama_pembina'=>$getSurat->nama_pembina,
            'created_at'=>date('Y-m-d H:i', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y H:i', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/hukdis/".$getSurat->photo : '',
            'form'=>['keterangan', 'tingkat', 'hukuman', 'id_taruna', 'start_time', 'end_time', 'id_user'],
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'show_persetujuan'=>false,
            'download'=>'-'
        );
        if(!empty($request->cetak)){
            return $data;
        }

        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
        $data['permission'] = [];
        if($roleName=='Pembina' && $getSurat->status_level_1!=1 && $getSurat->status!=1){
            $data['permission'] = ['edit', 'delete'];
        }
        if(($roleName=='Akademik dan Ketarunaan' || $roleName=='Super Admin') && $getSurat->status!=1){
            $data['show_persetujuan'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetaksurat/id/'.$request->id.'/id_user/'.$request->id_user.'/cetak/hukdis';
        }

        $data = json_decode(json_encode($data));
        return view('hukuman-dinas.show', compact('data'));
    }

    public function edit($id)
    {
        $hukdis = HukumanDinas::find($id);
        $selectTaruna = User::find($hukdis->id_taruna);
        $tingkat = ['1'=>'Ringan', '2'=>'Sedang', '3'=>'Berat'];
        $hukdis->start_time = date("Y-m-d\TH:i:s", strtotime($hukdis->start_time));
        $hukdis->end_time = date("Y-m-d\TH:i:s", strtotime($hukdis->end_time));
        return view('hukuman-dinas.edit',compact('hukdis', 'tingkat', 'selectTaruna'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['id_taruna' => 'required',
                        'tingkat' =>'required',
                        'hukuman' =>'required',
                        'keterangan' =>'required',
                        'start_time' => 'required',
                        'end_time' => 'required',
                        'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048']
                    );
        try {
            DB::beginTransaction();

                $getTaruna = User::find($request->id_taruna);

                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Arr::forget($input, '_token');
                $input['start_time'] = date('Y-m-d H:i:s', strtotime($request->start_time));
                $input['end_time'] = date('Y-m-d H:i:s', strtotime($request->end_time));
                $input['id_user'] = Auth::user()->id;
                $input['stb'] = $getTaruna->stb;
                $input['grade'] = $getTaruna->grade;
                $input['status'] = 0;
                $id = DB::table('tb_hukdis')->insertGetId($input);
            DB::commit();

            $data['status']     = true;
            $data['firebase']   = false;
            $keluarga           = User::keluargataruna($getTaruna->id);
            $keluarga_asuh      = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$getTaruna->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic = User::topic('createhukdis', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan hukuman dinas baru',
                    'body'=>'hukuman dinas baru telah dibuat',
                    'page'=>'/hukdis/detail/id/'.$id,
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
            \Session::flash('success','Data berhasil ditambah.');
            return redirect()->route('hukuman-dinas.index');

        }catch (\Throwable $th) {
            //@dd($th);
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('hukuman-dinas.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['id_taruna' => 'required',
                            'tingkat' =>'required',
                            'hukuman' =>'required',
                            'keterangan' =>'required',
                            'start_time' => 'required',
                            'end_time' => 'required',
                            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:2048']
                        );
        try {
            DB::beginTransaction();
                $hukdis     = HukumanDinas::find($id);
                $getTaruna  = User::find($request->id_taruna);

                $request->request->add(['id_user'=> Auth::user()->id]);
                $request->request->add(['user_updated'=> Auth::user()->id]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Arr::forget($input, '_token');
                $input['start_time'] = date('Y-m-d H:i:s', strtotime($request->start_time));
                $input['end_time'] = date('Y-m-d H:i:s', strtotime($request->end_time));
                $input['stb'] = $getTaruna->stb;
                $input['grade'] = $getTaruna->grade;
                $input['status'] = 0;

                $hukdis->update($input);
            DB::commit();

            $data['status']     = true;
            $data['firebase']   = false;
            $keluarga           = User::keluargataruna($getTaruna->id);
            $keluarga_asuh      = !empty($keluarga) ? strtolower($keluarga->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$getTaruna->id, 'keluarga_asuh'=>$keluarga_asuh];
            $topic = User::topic('createhukdis', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan hukuman dinas baru',
                    'body'=>'hukuman dinas baru telah dibuat',
                    'page'=>'/hukdis/detail/id/'.$id,
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
            \Session::flash('success','Data berhasil diperbarui.');
            return redirect()->route('hukuman-dinas.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('hukuman-dinas.edit');
        }
    }

    public function destroy($id)
    {
        try {
            $hukdis = HukumanDinas::find($id);
            $hukdis->user_deleted = Auth::user()->id;
            $hukdis->save();
            $hukdis->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
