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

        $data = Report::select('tb_pengaduan.id','tb_pengaduan.pengaduan','tb_pengaduan.id_user','tb_pengaduan.date_follow_up')
            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
            ->offset($start)
            ->limit($limit)
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_pengaduan.id_user', $currentUser->id);
                }
            })
            ->orderBy('tb_pengaduan.'.$order,$dir)
            ->get();

        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Report::where('tb_pengaduan.id','LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("tb_pengaduan.id as id, users.name as name, tb_pengaduan.report, tb_pengaduan.created_at, tb_pengaduan.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('tb_pengaduan.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Report::where('tb_pengaduan.id','LIKE',"%{$search}%")->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("tb_pengaduan.id as id, users.name as name, tb_pengaduan.report, tb_pengaduan.created_at, tb_pengaduan.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'tb_pengaduan.id_user')
                            ->count();
        return $data;
    }
}
