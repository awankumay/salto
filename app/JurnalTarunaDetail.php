<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class JurnalTarunaDetail extends Model
{
    protected $fillable = ['id_user', 'tanggal', 'start_time', 'end_time', 'kegiatan', 'status', 'grade'];
    protected $table = 'jurnal_taruna';
    protected $casts = [
        'start_time'  => 'time:H:i:s',
        'end_time' => 'time:H:i:s'
    ];

    public function GetCount($id_user, $date)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTarunaDetail::count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
                //$getTaruna  = implode(',',$id);
            return JurnalTarunaDetail::where('jurnal_taruna.id_user', $id_user)
                                     ->where('jurnal_taruna.tanggal', $date)
                                     ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTarunaDetail::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->where('jurnal_taruna.id_user', $id_user)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTarunaDetail::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->where('jurnal_taruna.id_user', $id_user)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTarunaDetail::join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->where('jurnal_taruna.id_user', $id_user)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->count();
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir, $id_user, $date)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('jurnal_taruna.id_user', $id_user)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('jurnal_taruna.id_user', $currentUser->id)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->where('jurnal_taruna.id_user', $id_user)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->where('jurnal_taruna.id_user', $id_user)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->where('jurnal_taruna.tanggal', $date)
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }
       
       
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search, $id_user, $date)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('jurnal_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('jurnal_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTarunaDetail::join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('jurnal_taruna.id', 'users.name as nama', 'jurnal_taruna.*', 'jurnal_taruna.status', 'jurnal_taruna.tanggal as tanggal')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }
        return $data;
    }

    public function GetCountDataFilter($search, $id_user, $date){
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('jurnal_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return JurnalTarunaDetail::join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                             ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'jurnal_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return JurnalTarunaDetail::join('orang_tua_taruna', 'taruna_id.id', '=', 'jurnal_taruna.id_user')
                            ->join('users', 'users.id', '=', 'jurnal_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }
        return $data;
    }
}
