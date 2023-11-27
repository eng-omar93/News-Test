<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\WebName;

class Author extends Model
{
    use SoftDeletes, WebName;

    protected $table = 'authors';
    protected $appends = array('web_name');

    protected $fillable = [
                          'id',
                          'name'
                        ];


    public function news()
    {
        return $this->hasMany(News::class, 'author_id');
    }


}
