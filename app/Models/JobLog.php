<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JobLog extends Model
{
    use SoftDeletes;

    protected $table = 'job_logs';

    protected $fillable = [
                          'id',
                          'provider_id',
                          'status',
                          'request',
                          'response',
                          'error'
                        ];


    public function provider()
    {
        return $this->belongsTo(NewsProvider::class, 'provider_id');
    }
}
