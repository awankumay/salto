<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Tags extends Model
{
    protected $table = 'tags';
    protected $fillable = [
        'name', 'user_created', 'user_updated', 'author'
    ];
    protected $primaryKey = 'id';

    /*public function Tags(){
    	return $this->belongsTo(Post::class, 'id', 'id_tags');
    }*/

    public function GetCount()
    {
        return Tags::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Tags::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search){
        $data = Tags::where('name','LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Tags::where('name','LIKE',"%{$search}%")
                                ->count();
        return $data;
    }
}
