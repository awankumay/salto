<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Pengasuhan;
use App\User;
use App\WaliasuhKeluargaAsuh;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class PengasuhanController extends Controller
{
    use ActionTable;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pengasuhan-daring-list');
        $this->middleware('permission:pengasuhan-daring-create', ['only' => ['create','store']]);
        $this->middleware('permission:pengasuhan-daring-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:pengasuhan-daring-delete', ['only' => ['destroy']]);
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
                8=>'name',
                1=>'keluarga_asuh',
                2=>'media',
                3=>'start_time',
                4=>'end_time',
                5=>'id_media',
                6=>'password',
                7=>'created_at'
            );
            $model  = New Pengasuhan();
            return $this->ActionTable($columns, $model, $request, 'pengasuhan.edit', 'pengasuhan-daring-edit', 'pengasuhan-daring-delete', 'pengasuhan-daring-list');
        }
        return view('pengasuhan.index');
    }

    public function create()
    {
        $keluarga =WaliasuhKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'waliasuh_keluarga_asuh.keluarga_asuh_id')
                    ->where('waliasuh_keluarga_asuh.waliasuh_id', Auth::user()->id)
                    ->select('keluarga_asuh.id as id', 'keluarga_asuh.name')
                    ->first();
        if (!empty($keluarga)) {
            return view('pengasuhan.create', compact('keluarga'));
        }else {
            \Session::flash('error', 'Keluarga asuh tidak ditemukan');
            return view('pengasuhan.index');
        }
        
    }

    public function edit($id)
    {
        $pengasuhan = Pengasuhan::find($id);
        $pengasuhan->start_time = date("Y-m-d\TH:i:s", strtotime($pengasuhan->start_time));
        $pengasuhan->end_time = date("Y-m-d\TH:i:s", strtotime($pengasuhan->end_time));
        return view('pengasuhan.edit',compact('pengasuhan'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
                        ['id_user' => 'required',
                        'media' =>'required',
                        'id_media' =>'required',
                        'password' =>'required',
                        'start_time' =>'required',
                        'end_time' =>'required',
                        'judul' => 'required']
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Arr::forget($input, '_token');
                $input['start_time'] = date('Y-m-d H:i:s', strtotime($request->start_time));
                $input['end_time'] = date('Y-m-d H:i:s', strtotime($request->end_time));
                $input['keluarga_asuh_id'] = $request->keluarga_asuh_id;
                $id = DB::table('tb_pengasuhan_daring')->insertGetId($input);
            DB::commit();
            $getKeluargaAsuh = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'waliasuh_keluarga_asuh.keluarga_asuh_id')
                                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $request->id_user)
                                                ->select('keluarga_asuh.id', 'keluarga_asuh.name')
                                                ->first();
            $data['status'] = true;
            $data['firebase'] = false;
            $keluarga_asuh = !empty($getKeluargaAsuh) ? strtolower($getKeluargaAsuh->name) : null;
            
            $dataFirebase = [];
            $dataFirebase = ['id'=>$request->id_user, 'keluarga_asuh'=>$keluarga_asuh];
            
            $topic = User::topic('createpengasuhan', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan pengasuhan baru',
                    'body'=>'pengasuhan baru telah dibuat',
                    'page'=>'/pengasuhan/detail/id/'.$id,
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
            return redirect()->route('pengasuhan.index');

        }catch (\Throwable $th) {
            //@dd($th);
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('pengasuhan.create');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
                            ['id_user' => 'required',
                            'media' =>'required',
                            'id_media' =>'required',
                            'password' =>'required',
                            'start_time' =>'required',
                            'end_time' =>'required',
                            'judul' => 'required']
                        );
        try {
            $pengasuhan = Pengasuhan::find($id);
            DB::beginTransaction();
                $request->request->add(['user_updated'=> Auth::user()->id]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Arr::forget($input, '_token');
                $input['start_time'] = date('Y-m-d H:i:s', strtotime($request->start_time));
                $input['end_time'] = date('Y-m-d H:i:s', strtotime($request->end_time));
                $pengasuhan->update($input);
            DB::commit();
            $getKeluargaAsuh = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'waliasuh_keluarga_asuh.keluarga_asuh_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $request->id_user)
                                ->select('keluarga_asuh.id', 'keluarga_asuh.name')
                                ->first();
            $data['status'] = true;
            $data['firebase'] = false;
            $keluarga_asuh = !empty($getKeluargaAsuh) ? strtolower($getKeluargaAsuh->name) : null;

            $dataFirebase = [];
            $dataFirebase = ['id'=>$request->id_user, 'keluarga_asuh'=>$keluarga_asuh];

            $topic = User::topic('createpengasuhan', $dataFirebase);
            if(!empty($topic)){
                set_time_limit(60);
                for ($i=0; $i < count($topic); $i++) { 
                    $paramsFirebase=['title'=>'Pemberitahuan pengasuhan baru',
                    'body'=>'pengasuhan baru telah dibuat',
                    'page'=>'/pengasuhan/detail/id/'.$id,
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
            return redirect()->route('pengasuhan.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('pengasuhan.edit');
        }
    }

    public function destroy($id)
    {
        try {
            $pengasuhan = Pengasuhan::find($id);
            $pengasuhan->user_deleted = Auth::user()->id;
            $pengasuhan->save();
            $pengasuhan->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
