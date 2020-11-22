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

class Suket extends Authenticatable
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
    protected $table = 'tb_suket';
    protected $fillable = [
        'id_user', 
        'stb', 
        'nama', 
        'ttl', 
        'orangtua', 
        'pekerjaan', 
        'alamat', 
        'keperluan', 
        'status',
        'user_disposisi', 
        'date_disposisi',
        'status_disposisi', 
        'reason_disposisi', 
        'user_approve_level_1',
        'date_approve_level_1',  
        'status_level_1', 
        'reason_level_1',
        'user_approve_level_2',
        'date_approve_level_2',  
        'status_level_2', 
        'reason_level_2',
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
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return SuratIzin::count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return SuratIzin::where('user_created', $currentUser->id)->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return SuratIzin::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.user_created')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return SuratIzin::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.user_created')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return SuratIzin::join('orang_tua_taruna', 'taruna_id.id', '=', 'surat_header.user_created')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->count();
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {

        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->where('surat_header.id_user', $currentUser->id)
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'surat_header.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
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
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('surat_header.id', 'LIKE',"%{$search}%");
                            })
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('surat_header.id', 'LIKE',"%{$search}%");
                            })
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'surat_header.id_user')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->select('surat_header.id', 'users.name as name', 'menu_persetujuan.nama_menu', 'surat_header.status', 'surat_header.created_at')
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
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('surat_header.id', 'LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->where('user_created', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.id_user')
                            ->join('pembina_keluarga_asuh', 'pembina_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('pembina_keluarga_asuh.pembina_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                             ->join('users', 'users.id', '=', 'surat_header.id_user')
                             ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'surat_header.id_user')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return SuratIzin::join('menu_persetujuan', 'menu_persetujuan.id', '=', 'surat_header.id_category')
                            ->join('orang_tua_taruna', 'taruna_id.id', '=', 'surat_header.id_user')
                            ->join('users', 'users.id', '=', 'surat_header.id_user')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%");
                            })
                            ->count();
        }
        return $data;
    }
}