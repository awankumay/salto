<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use SoftDeletes;
    protected $table = 'posts';
    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'excerpt',
        'content',
        'headline',
        'status',
        'photo',
        'post_categories_id',
        'tags',
        'author',
        'user_created',
        'user_updated',
        'date_published',
        'file'
    ];
    protected $dates = ['deleted_at'];

    public function Author()
    {
    	return $this->belongsTo(User::class, 'author', 'id');
    }

    public function PostCategory(){
    	return $this->hasOne(PostCategory::class, 'id', 'post_categories_id');
    }

    public function GetCount()
    {
        return Content::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Content::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Content::where('title','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Content::where('title','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
