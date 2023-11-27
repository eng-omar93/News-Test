<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NewsProvider extends Model
{
    use SoftDeletes;
    protected $table = 'news_providers';
    protected $fillable = [
                          'id',
                          'name',
                          'token',
                          'has_published_at',
                          'auth_url', //for token refresh
                          'user_name', //for token refresh
                          'password' //for token refresh
                        ];


    public function news()
    {
        return $this->hasMany(News::class, 'provider_id');
    }

    public function jobLogs()
    {
        return $this->hasMany(JobLog::class, 'provider_id');
    }


    public function FailedJobLogs()
    {
        return $this->hasMany(JobLog::class, 'provider_id')->where('status',0);
    }
}
