<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\WebName;

class Source extends Model
{
    use SoftDeletes, WebName;

    protected $table = 'sources';
    protected $appends = array('web_name');

    protected $fillable = [
                          'id',
                          'name'
                        ];


    public function news()
    {
        return $this->hasMany(News::class, 'source_id');
    }
}
