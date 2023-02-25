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

class HukumanDinas extends Authenticatable
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
    protected $table = 'tb_hukdis';
    protected $fillable = [
        'stb', 'keterangan', 'tingkat', 'hukuman', 'waktu', 
        'status', 'datetime', 'photo', 'grade_table', 'id_taruna', 'start_time', 'end_time', 
        'id_user', 'created_at', 'updated_at', 'user_created', 'user_updated', 'deleted_at', 'user_deleted',
        'user_approve_level_1', 'user_approve_level_2',
        'reason_level_1', 'reason_level_2'
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
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Waliasuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return HukumanDinas::count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return HukumanDinas::where('id_taruna', $currentUser->id)->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return HukumanDinas::where('id_user', $currentUser->id)->count();
        }else if ($currentUser->getRoleNames()[0]=='Waliasuh'){
            return HukumanDinas::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_hukdis.id_taruna')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                            ->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return HukumanDinas::join('orang_tua_taruna', 'orang_tua_taruna.taruna_id', '=', 'tb_hukdis.id_taruna')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->count();
        }

        return ;
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {

        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Wali Asuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->where('tb_hukdis.id_taruna', $currentUser->id)
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Waliasuh'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_hukdis.taruna_id')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_di', $currentUser->id)
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->where('tb_hukdis.id_user', $currentUser->id)
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->join('orang_tua_taruna', 'orang_tua_taruna.taruna_id', '=', 'tb_hukdis.taruna_id')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
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
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->Where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'SUBSTRING(tb_hukdis.hukuman, 0, 20) as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                                ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                                ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                                ->where('tb_hukdis.id_taruna', $currentUser->id)
                                ->Where(function($q) use ($search) {
                                    $q->where('users.name','LIKE',"%{$search}%")
                                    ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                                })
                                ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->where('tb_hukdis.id_user', $currentUser->id)
                            ->where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }else if ($currentUser->getRoleNames()[0]=='Wali Asuh'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_hukdis.taruna_id')
                            ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_di', $currentUser->id)
                            ->where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return HukumanDinas::join('users as b', 'b.id', '=', 'tb_hukdis.id_user')
                            ->join('users as c', 'c.id', '=', 'tb_hukdis.id_taruna')
                            ->leftJoin('grade_table', 'grade_table.id', '=', 'tb_hukdis.grade')
                            ->join('orang_tua_taruna', 'orang_tua_taruna.taruna_id', '=', 'tb_hukdis.taruna_id')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                            })
                            ->select('tb_hukdis.id', 'c.name as nama_taruna', 'grade_table.grade as grade_name', 'b.name as nama_pembina', 'tb_hukdis.*', 'tb_hukdis.hukuman as hukuman_taruna')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        }
        return $data;
    }

    public function GetCountDataFilter($search){
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]!='Taruna' && $currentUser->getRoleNames()[0]!='Pembina' && $currentUser->getRoleNames()[0]!='Waliasuh' && $currentUser->getRoleNames()[0]!='Orang Tua') {
            return HukumanDinas::where(function($q) use ($search) {
                                    $q->where('users.name','LIKE',"%{$search}%")
                                    ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                                })->count();
        }else if ($currentUser->getRoleNames()[0]=='Taruna'){
            return HukumanDinas::where(function($q) use ($search) {
                                    $q->where('users.name','LIKE',"%{$search}%")
                                    ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                                })
                                ->where('id_taruna', $currentUser->id)
                                ->count();
        }else if ($currentUser->getRoleNames()[0]=='Pembina'){
            return HukumanDinas::where(function($q) use ($search) {
                                    $q->where('users.name','LIKE',"%{$search}%")
                                    ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                                })
                                ->where('id_user', $currentUser->id)
                                ->count();
        }else if ($currentUser->getRoleNames()[0]=='Waliasuh'){
            return HukumanDinas::join('taruna_keluarga_asuh', 'taruna_keluarga_asuh.taruna_id', '=', 'tb_hukdis.id_taruna')
                                ->join('waliasuh_keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->where('waliasuh_keluarga_asuh.waliasuh_id', $currentUser->id)
                                ->where(function($q) use ($search) {
                                    $q->where('users.name','LIKE',"%{$search}%")
                                    ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                                })->count();
        }else if ($currentUser->getRoleNames()[0]=='Orang Tua'){
            return HukumanDinas::join('orang_tua_taruna', 'orang_tua_taruna.taruna_id', '=', 'tb_hukdis.id_taruna')
                            ->where('orang_tua_taruna.orangtua_id', $currentUser->id)
                            ->where(function($q) use ($search) {
                                $q->where('users.name','LIKE',"%{$search}%")
                                ->orWhere('tb_hukdis.id', 'LIKE',"%{$search}%");
                            })->count();
        }
        return ;
    }
}
