<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class VisitView extends Model
{
    protected $table = 'data_appointment';
    protected $fillable = [
        'id_users', 'visitor_name', 'type', 'schedule', 'date', 'status', 'no_antrian', 'convicts_id'
    ];
    protected $primaryKey = 'id';
    public function GetCount()
    {
        return VisitView::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = VisitView::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = VisitView::where('visitor','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = VisitView::where('visitor','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
