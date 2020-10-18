<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class Slider extends Model
{
    use SoftDeletes;
    protected $table = 'sliders';
    protected $fillable = [
        'photo',
        'status'
    ];

    public function GetCount()
    {
        return Slider::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Slider::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Slider::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Slider::where('id','LIKE',"%{$search}%")
                        ->count();
        return $data;
    }
}
