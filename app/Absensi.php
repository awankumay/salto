<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Absensi extends Model
{
    //use SoftDeletes;
    protected $fillable = ['clock_in', 'clock_out', 'file_clock_in', 'file_clock_out',
                             'id_user', 'lat_in', 'long_in', 'lat_out', 'long_out', 
                             'grade', 'created_at', 'updated_at'];
   // protected $dates = ['deleted_at'];
    protected $table = 'absensi_taruna';
    protected $primaryKey = 'id';

    public function GetCount()
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Absensi::count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
                $id = [];
                $id[]=$currentUser->id;
                //$getTaruna  = implode(',',$id);
            return Absensi::whereIn('id_user', $id)->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Absensi::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Absensi::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Absensi::join('orang_tua_taruna', 'taruna_id.id', '=', 'absensi_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->count();
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {

        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->where('absensi_taruna.id_user', $currentUser->id)
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'absensi_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }
       
       
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('absensi_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('absensi_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Absensi::leftJoin('orang_tua_taruna', 'taruna_id.id', '=', 'absensi_taruna.id_user')
                            ->join('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('absensi_taruna.id as id', 'users.name as name', 'users.stb as stb', 'absensi_taruna.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }
        return $data;
    }

    public function GetCountDataFilter($search){
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('absensi_taruna.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Absensi::leftJoin('users', 'users.id', '=', 'absensi_taruna.id_user')
                             ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'absensi_taruna.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Absensi::leftJoin('orang_tua_taruna', 'taruna_id.id', '=', 'absensi_taruna.id_user')
                            ->join('users', 'users.id', '=', 'absensi_taruna.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }
        return $data;
    }
}
