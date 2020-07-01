<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class PostCategory extends Model
{
    protected $table = 'post_categories';
    protected $fillable = [
        'name', 'description', 'author', 'user_created', 'user_updated'
    ];
    protected $primaryKey = 'id';

    public function Author()
    {
    	return $this->belongsTo(User::class, 'author', 'id');
    }

    public function Content()
    {
    	return $this->belongsTo(Content::class, 'post_categories_id', 'id');
    }

    public function GetCount()
    {
        return PostCategory::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = PostCategory::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = PostCategory::where('description','LIKE',"%{$search}%")
                                ->orWhere('name', 'LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = PostCategory::where('description','LIKE',"%{$search}%")
                                ->orWhere('name', 'LIKE',"%{$search}%")
                                ->count();
        return $data;
    }
}
