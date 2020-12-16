<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class JurnalTaruna extends Model
{
    protected $fillable = ['id_user', 'tanggal', 'start_time', 'end_time', 'kegiatan', 'status', 'grade'];
    protected $table = 'jurnal_taruna';
    protected $casts = [
        'created_at'  => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s'
    ];
    public function GetCount()
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTaruna::groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal')->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
                $id = [];
                $orangtua   = OrangTua::where('taruna_id', $currentUser->id)->get();
                foreach ($orangtua as $key => $value) {
                    $id[]=$value->orangtua_id;
                }
                $id[]=$currentUser->id;
                //$getTaruna  = implode(',',$id);
            return JurnalTaruna::whereIn('id_user', $id)->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTaruna::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.user_created')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal')
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTaruna::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.user_created')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal')
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTaruna::join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.user_created')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal')
                            ->count();
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {

        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('jurnal_taruna.id_user', $currentUser->id)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }
       
       
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('jurnal_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('jurnal_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTaruna::join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->orderBy($order,$dir)
                            ->get();
        }
        return $data;
    }

    public function GetCountDataFilter($search){
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('jurnal_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTaruna::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                             ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTaruna::join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->groupBy('jurnal_taruna.id_user','jurnal_taruna.tanggal','users.name')
                            ->count();
        }
        return $data;
    }
}
