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

class WBS extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens;
    use HasRoles;

    protected $table = 'tb_wbs';
    protected $fillable = [
        'id_user',
        'status',
        'materi',
        'user_follow_up',
        'date_follow_up',
        'follow_up',
        'ewhat',
        'ewho',
        'ewhere',
        'ewhen',
        'ewhy',
        'ehow',
        'user_created',
        'user_updated',
        'user_deleted',
        'id_materi'
    ];
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id';
    protected $casts = [
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function GetCount()
    {
        return WBS::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = WBS::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = WBS::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = WBS::where('id','LIKE',"%{$search}%")
                        ->count();
        return $data;
    }
}
