<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Report extends Model
{
    protected $table = 'reports';
    protected $fillable = [
        'users_id', 'report'
    ];
    protected $primaryKey = 'id';

    public function GetCount()
    {
        return Report::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Report::select(DB::raw("reports.id as id, users.name as name, reports.report, reports.created_at, reports.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'reports.users_id')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('reports.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Report::where('reports.id','LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("reports.id as id, users.name as name, reports.report, reports.created_at, reports.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'reports.users_id')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('reports.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Report::where('reports.id','LIKE',"%{$search}%")->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("reports.id as id, users.name as name, reports.report, reports.created_at, reports.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'reports.users_id')
                            ->count();
        return $data;
    }
}
