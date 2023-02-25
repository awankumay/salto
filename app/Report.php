<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'tb_pengaduan';
    protected $fillable = [
        'id_user', 'report', 'follow_up'
    ];
    protected $primaryKey = 'id';

    public function GetCount()
    {
        return Report::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $currentUser = Auth::user();

        return Report::select('tb_pengaduan.id','tb_pengaduan.pengaduan','tb_pengaduan.id_user','tb_pengaduan.date_follow_up')
            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_pengaduan.id_user', $currentUser->id);
                }
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('tb_pengaduan.'.$order,$dir)
            ->get();
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $currentUser = Auth::user();

        return Report::where('tb_pengaduan.id','LIKE',"%{$search}%")
            ->select('tb_pengaduan.id','tb_pengaduan.pengaduan','tb_pengaduan.id_user','tb_pengaduan.date_follow_up')
            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_pengaduan.id_user', $currentUser->id);
                }
            })
            ->orWhere('users.name', 'LIKE', "%{$search}%")
            ->offset($start)
            ->limit($limit)
            ->orderBy('tb_pengaduan.'.$order,$dir)
            ->get();
    }

    public function GetCountDataFilter($search){
        $currentUser = Auth::user();

        return Report::where('tb_pengaduan.id','LIKE',"%{$search}%")->orWhere('users.name', 'LIKE', "%{$search}%")
            ->select('tb_pengaduan.id','tb_pengaduan.pengaduan','tb_pengaduan.id_user','tb_pengaduan.date_follow_up')
            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_pengaduan.id_user', $currentUser->id);
                }
            })
            ->count();
    }
}
