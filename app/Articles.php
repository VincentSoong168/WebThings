<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tag;

class Articles extends Model
{
	protected $table = 'articles';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id' ,'title', 'content', 'slug', 'description', 'image', 'category_id', 'status' 
    ];

    public function author()
    {
        return $this->belongsTo('App\Users', 'author_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function tag()
    {
        return $this->belongsToMany('App\Tag', 'article_inter_tag', 'article_id', 'tag_id');
    }

    public function get_article_for_page($tag_id = null)
    {
        if($tag_id){
            $article_list = $this->whereHas('tag', function($query)use($tag_id){ $query->where('tag_id', $tag_id); })
                            ->where('status', 1)
                            ->orderBy('id', 'DESC')
                            ->paginate(10);
            // $tag = Tag::find($tag_id);
            // $article_list = $tag->article;
        } else {
            $article_list = $this->where('status', 1)->orderBy('id', 'DESC')->paginate(10);
        }

        return $article_list;
    }
}
