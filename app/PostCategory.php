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

    /*public function Post()
    {
        return $this->belongsTo(Post::class);
    }*/
}
