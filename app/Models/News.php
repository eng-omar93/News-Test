<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class News extends Model
{
    use SoftDeletes;
    protected $table = 'news';
    protected $fillable = [
                          'id',
                          'provider_id',
                          'category_id',
                          'source_id',
                          'author_id',
                          'title',
                          'description',
                          'content',
                          'image',
                          'url',
                          'published_at'
                        ];


    public function provider()
    {
        return $this->belongsTo(NewsProvider::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }
}
