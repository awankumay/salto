<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Content;
use App\Grade;
use App\SuratIzin;
use App\IzinSakit;
use App\KeluarKampus;
use App\TrainingCenter;
use App\PernikahanSaudara;
use App\PemakamanKeluarga;
use App\OrangTuaSakit;
use App\KegiatanDalam;
use App\Tugas;
use App\KegiatanPesiar;
use App\OrangTua;
use App\WaliAsuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\Provinces;
use App\Regencies;
use App\Permission;
use App\Prestasi;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Absensi;
use App\JurnalTaruna;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class MonitoringController extends BaseController
{
    use ImageTrait;
    
    public function getprestasi(Request $request){
        $limit  = 5;
        $id_user = $request->idUser;
        $lastId = !empty($request->lastId) ? $request->lastId : 0;
        $order  = !empty($request->order) ? $request->order : 'surat_header.id';
        $search  = !empty($request->search) ? $request->search : '';
        $dir    = !empty($request->dir) ? $request->dir : 'DESC';
        $diff   = ($dir=='DESC') ? '<' : '>';
        $condition = 'tb_penghargaan.id='.$lastId.'';
        $getUser = User::find($request->idUser);
        $roleName = $getUser->getRoleNames()[0];
        $result =[];
        if($order=='status'){
            $order='tb_penghargaan.status';
        }
        if($order=='name'){
            $order='users.name';
        }
        if($order=='id'){
            $order='tb_penghargaan.id';
        }

        $permission = [];
        if($lastId==0){
            if($roleName=='Taruna'){
                $id = [];
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      =  Prestasi::whereRaw($condition)
                                ->count();   
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $tarunaId[] = $id_user;
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $taruna     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 7)
                                ->whereNull('users.deleted_at')
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                $total      = Prestasi::whereRaw($condition)
                                ->count();     
                $count  = $total;
                $data   = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }
        }else {
            if($roleName=='Taruna'){
                $id = [];
                $id[]=$id_user;
                $getTaruna  = implode(',',$id);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total      =  Prestasi::whereRaw($condition)
                                ->count();  
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
            }else if($roleName=='OrangTua'){
                $taruna     = OrangTua::where('orangtua_id', $id_user)->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }else if($roleName=='Wali Asuh'){
                $taruna     = WaliasuhKeluargaAsuh::join('taruna_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               

            }else if($roleName=='Pembina'){
                $taruna     = PembinaKeluargaAsuh::join('taruna_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->select('taruna_keluarga_asuh.taruna_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $id_user)
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->taruna_id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               

            }else if ($roleName=='Akademik dan Ketarunaan' || $roleName=='Direktur' || $roleName=='Super Admin') {
                $taruna     = DB::table('users')
                                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
                                ->select('users.id', 'users.name')
                                ->where('model_has_roles.role_id', 7)
                                ->whereNull('users.deleted_at')
                                ->get();
                $tarunaId   = [];
                foreach ($taruna as $key => $value) {
                    $tarunaId[]=$value->id;
                }
                $getTaruna  = implode(',',$tarunaId);
                $condition = 'tb_penghargaan.id_user in('.$getTaruna.') AND tb_penghargaan.id '.$diff.' '.$lastId.'';
                $total = Prestasi::whereRaw('tb_penghargaan.id_user in('.$getTaruna.')')
                            ->count();
                
                $count = Prestasi::whereRaw($condition)->count();
                $data = $this->penghargaantaruna($condition, $limit, $order, $dir);
               
            }
        }
        foreach ($data as $key => $value) {
            if($value->status==1){
                $status='Disetujui';
            }else if ($value->status==0) {
                $status='Belum Disetuji';
                $download = '-';
            }else{
                $status='Tidak Disetuji';
                $download = '-';
            }
            $dataPermission = [];
            if($roleName=='Taruna' || $roleName=='Super Admin'){
                $dataPermission = ['edit', 'delete'];
            }

            $result['penghargaan'][]= [ 
                'id'=>$value->id,
                'name'=>$value->name,
                'tanggal'=>$value->tanggal,
                'status_name'=> $status,
                'status'=> $value->status,
                'keterangan'=> substr($value->keterangan, 0, 40).'...',
                'permission'=>$dataPermission
            ];
                
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
        return $this->sendResponse($result, 'prestasi load successfully.');
    }

    public function prestasidetail(Request $request)
    {
        $id   = $request->id;
        $getSurat = Prestasi::join('users as author', 'author.id', '=', 'surat_header.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'surat_header.user_approve_level_1')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'surat_header.user_disposisi')
                                    ->select('tb_penghargaan.id as id', 
                                            'tb_penghargaan.id_user as id_user',
                                            'author.name as nama_taruna',
                                            'tb_penghargaan.photo as photo',
                                            'tb_penghargaan.status as status',
                                            'tb_penghargaan.updated_at as diajukan',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_penghargaan.date_approve_level_1 as date_approve_1',
                                            'tb_penghargaan.reason_level_1 as user_reason_1',
                                            'tb_penghargaan.status_level_1 as status_level_1',
                                            'user_disposisi.name as user_disposisi',
                                            'tb_penghargaan.date_disposisi as date_disposisi',
                                            'tb_penghargaan.status_disposisi as status_disposisi',
                                            'tb_penghargaan.reason_disposisi as reason_disposisi'
                                            )
                                    ->where('tb_penghargaan.id', $id)
                                    ->where('tb_penghargaan.id_user', $request->id_user)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseFalse($data, 'Penghargaan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        foreach ($getSurat as $key => $value) {
            $data[]=$value;
        }
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }
    
        if($roleName=='Pembina' && $data['status']!=1){
            $data['show_disposisi'] = true;
        }
        if(($roleName=='Taruna')) {
            if($getSurat->id_user!=$request->id_user){
                $data['permission'] = [];
            }
        }
        if($roleName=='Akademik dan Ketarunaan' && $data['status']!=1 && $data['status_disposisi']==1){
            $data['show_persetujuan'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = '-';
        }
        $data['start_format_1'] = date('Y-m-d', strtotime($getSurat->diajukan));
        $data['start_format_2'] = date('d/m/Y', strtotime($getSurat->diajukan));

        return $this->sendResponse($data, 'surat izin detail load successfully.');
    }

    public function deleteprestasi(Request $request)
    {
        return $this->sendResponse($result, 'prestasi delete successfully.');
    }

    public function penghargaantaruna($condition, $limit, $order, $dir)
    {
        return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
            ->whereRaw($condition)
            ->select(DB::raw("(DATE(tb_penghargaan.created_at))as tanggal"),'users.name', 'tb_penghargaan.status', 'tb_penghargaan.keterangan', 'tb_penghargaan.id as id')
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }
}