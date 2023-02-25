<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Reportwbs extends Model
{
    protected $table = 'tb_wbs';
    protected $fillable = ['id_user', 'ewhy','ewhen','ewhere','ewho','what','ehow', 'follow_up','id_materi','materi'];
    protected $primaryKey = 'id';

    public function GetCount()
    {
        return Report::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $currentUser = Auth::user();

        $data = Reportwbs::select('tb_wbs.id','tb_wbs.materi','tb_wbs.follow_up', 'tb_wbs.created_at', 'users.name as username','tb_wbs.ewhat','tb_wbs.ewho','tb_wbs.ewhy','tb_wbs.ewhen','tb_wbs.ewhere')
            ->leftJoin('users', 'users.id', '=', 'tb_wbs.id_user')
            ->offset($start)
            ->limit($limit)
            ->when($currentUser, function($query, $currentUser) {
                if($currentUser->getRoleNames()[0] != "Super Admin") {
                    $query->where('tb_wbs.id_user', $currentUser->id);
                }
            })
            ->orderBy('tb_wbs.'.$order,$dir)
            ->get();

        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $currentUser = Auth::user();
        $data = Report::where('tb_wbs.id','LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("tb_wbs.id','tb_wbs.pengaduan','tb_wbs.id_user','tb_wbs.date_follow_up"))
                            ->leftJoin('users', 'users.id', '=', 'tb_wbs.id_user')
                            ->when($currentUser, function($query, $currentUser) {
                                if($currentUser->getRoleNames()[0] != "Super Admin") {
                                    $query->where('tb_wbs.id_user', $currentUser->id);
                                }
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('tb_wbs.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search)
    {
        $currentUser = Auth::user();
        $data = Report::where('tb_wbs.id','LIKE',"%{$search}%")->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("ttb_wbs.id','tb_wbs.pengaduan','tb_wbs.id_user','tb_wbs.date_follow_up"))
                            ->leftJoin('users', 'users.id', '=', 'tb_wbs.id_user')
                            ->when($currentUser, function($query, $currentUser) {
                                if($currentUser->getRoleNames()[0] != "Super Admin") {
                                    $query->where('tb_wbs.id_user', $currentUser->id);
                                }
                            })
                            ->count();
        return $data;
    }
}
