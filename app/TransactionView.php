<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class TransactionView extends Model
{
    protected $table = 'transaction_header';
    protected $primaryKey = 'id';
    public function GetCount()
    {
        return TransactionView::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = TransactionView::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = TransactionView::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = TransactionView::where('id','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
