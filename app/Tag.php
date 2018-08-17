<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name'
    ];

    public function article()
    {
        return $this->belongsToMany('App\Articles', 'article_inter_tag', 'tag_id', 'article_id');
    }
}
