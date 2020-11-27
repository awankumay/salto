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

class Pengaduan extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens;
    use HasRoles;

    protected $table = 'tb_pengaduan';
    protected $fillable = [
        'bukti',
        'id_user',
        'status',
        'user_follow_up',
        'date_follow_up',
        'follow_up',
        'pengaduan',
        'user_created',
        'user_updated',
        'user_deleted'
    ];
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id';
    protected $casts = [
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function GetCount()
    {
        return Pengaduan::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Pengaduan::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Pengaduan::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Pengaduan::where('id','LIKE',"%{$search}%")
                        ->count();
        return $data;
    }
}
