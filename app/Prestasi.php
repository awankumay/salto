<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
#use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use DB;
use Auth;

class Prestasi extends Authenticatable
{
    #use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'tb_penghargaan';
    protected $fillable = [
        'id_user', 
        'stb', 
        'keterangan', 
        'tingkat', 
        'tempat', 
        'waktu', 
        'status', 
        'user_disposisi', 
        'date_disposisi',
        'status_disposisi', 
        'reason_disposisi', 
        'user_approve_level_1',
        'date_approve_level_1',  
        'status_level_1', 
        'reason_level_1',
        'user_created', 
        'user_updated', 
        'user_deleted', 
        'deleted_at', 
        'created_at', 
        'updated_at', 
        'photo', 
        'grade'
    ];
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id';
    protected $casts = [
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function GetCount()
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Prestasi::count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            $id = [];
            $orangtua   = OrangTua::where('taruna_id', $currentUser->id)->get();
            if(!empty($orangtua)){
                foreach ($orangtua as $key => $value) {
                    $id[]=$value->orangtua_id;
                }
            }
            $id[]=$currentUser->id;
            $getTaruna  = implode(',',$id);
            $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
            return Prestasi::whereRaw($condition)
                            ->count(); 
            
        }/* else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Prestasi::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->count();
        } */else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Prestasi::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
                $taruna     = OrangTua::where('orangtua_id', $currentUser->id)->get();
                $tarunaId   = [];
                if(!empty($taruna)){
                    foreach ($taruna as $key => $value) {
                        $tarunaId[]=$value->taruna_id;
                    }
                }
                $tarunaId[] = $currentUser->id;
                $getTaruna  = implode(',',$tarunaId);
                $condition  = 'tb_penghargaan.id_user in('.$getTaruna.')';
                return Prestasi::whereRaw($condition)
                                ->count(); 
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {

        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->select('tb_penghargaan.*', 'tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->where('tb_penghargaan.id_user', $currentUser->id)
                            ->select('tb_penghargaan.*', 'tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }/* else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->select('tb_penghargaan.*', 'tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        } */else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->select('tb_penghargaan.*', 'tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_penghargaan.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('tb_penghargaan.*', 'tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at')
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
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at', 'tb_penghargaan.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at', 'tb_penghargaan.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }/* else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at', 'tb_penghargaan.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        } */else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at', 'tb_penghargaan.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Prestasi::join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_penghargaan.id_user')
                            ->join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_penghargaan.id', 'users.name as name', 'tb_penghargaan.status', 'tb_penghargaan.created_at', 'tb_penghargaan.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }
        return $data;
    }

    public function GetCountDataFilter($search){
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->where('id_user', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }/* else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        } */else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Prestasi::join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                             ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_penghargaan.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");

                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Prestasi::join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_penghargaan.id_user')
                            ->join('users', 'users.id', '=', 'tb_penghargaan.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_penghargaan.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }
        return $data;
    }
}
