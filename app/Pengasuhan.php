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

class Pengasuhan extends Authenticatable
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
    protected $table = 'tb_pengasuhan_daring';
    protected $fillable = [
        'keluarga_asuh', 'media', 'waktu', 'id_media', 'password', 
        'status', 'datetime', 'created_at', 'updated_at',
        'id_user', 'user_created', 'user_updated', 'deleted_at', 'created_at', 'updated_at',
        'reason', 'user_approve', 'date_approve', 'start_time', 'end_time', 'keluarga_asuh_id',
        'judul'
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
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Pengasuhan::count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Pengasuhan::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga_asuh_id', '=', 'tb_pengasuhan_daring.keluarga_asuh_id')
                    ->where('taruna_keluarga_asuh.taruna_id', $currentUser->id)
                    ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Pengasuhan::join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'tb_pengasuhan_daring.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('tb_pengasuhan_daring.id_user', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Pengasuhan::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga_asuh_id', '=', 'tb_pengasuhan_daring.keluarga_asuh_id')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_pengasuhan_daring')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->count();
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {

        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga_asuh_id', '=', 'tb_pengasuhan_daring.keluarga_asuh_id')
                            ->where('taruna_keluarga_asuh.taruna_id', $currentUser->id)
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'tb_pengasuhan_daring.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('tb_pengasuhan_daring.id_user', $currentUser->id)
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*',
                                    'users.name as name', 
                                    'tb_pengasuhan_daring.keluarga_asuh', 
                                    'tb_pengasuhan_daring.media', 
                                    'tb_pengasuhan_daring.start_time', 
                                    'tb_pengasuhan_daring.end_time', 
                                    'tb_pengasuhan_daring.id_media', 
                                    'tb_pengasuhan_daring.password', 
                                    'tb_pengasuhan_daring.status')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
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
            return Pengasuhan::join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_pengasuhan_daring.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_pengasuhan_daring.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('tb_pengasuhan_daring.id as id', 'tb_pengasuhan_daring.*', 'users.name as name', 'tb_pengasuhan_daring.status', 'tb_pengasuhan_daring.created_at')
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
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_pengasuhan_daring.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                             ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                             ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.keluarga', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return Pengasuhan::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'tb_pengasuhan_daring.id_category')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->join('users', 'users.id', '=', 'tb_pengasuhan_daring.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }
        return $data;
    }
}
