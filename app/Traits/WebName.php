<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait WebName
{

    public function getWebNameAttribute()
    {
        $webName = $this->name;
        $webName = str_replace("_", " ", $webName);
        $webName = str_replace("-", " ", $webName);
        $webName = str_replace("/", " ", $webName);
        $webName = Str::title($webName);
        return $webName;
    }
}
