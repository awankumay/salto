<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Slider extends Model
{
    protected $table = 'sliders';
    protected $fillable = [
        'photo',
        'id_categories',
        'status'
    ];

    public function GetCount()
    {
        return Slider::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Slider::select(DB::raw("sliders.id as id, post_categories.name as name, sliders.photo, sliders.status, sliders.created_at, sliders.updated_at"))
                            ->leftJoin('post_categories', 'post_categories.id', '=', 'sliders.id_categories')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('sliders.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Slider::where('sliders.id','LIKE',"%{$search}%")
                            ->orWhere('post_categories.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("sliders.id as id, post_categories.name as name, sliders.photo, sliders.status, sliders.created_at, sliders.updated_at"))
                            ->leftJoin('post_categories', 'post_categories.id', '=', 'sliders.id_categories')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('sliders.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Slider::where('sliders.id','LIKE',"%{$search}%")
                    ->orWhere('post_categories.name', 'LIKE', "%{$search}%")
                    ->select(DB::raw("sliders.id as id, post_categories.name as name, sliders.photo, sliders.status, sliders.created_at, sliders.updated_at"))
                    ->leftJoin('post_categories', 'post_categories.id', '=', 'sliders.id_categories')
                    ->count();
        return $data;
    }
}
