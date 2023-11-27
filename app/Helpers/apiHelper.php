<?php
namespace App\Helpers;

use Illuminate\Support\Arr;
class apiHelper
{
    public static function okResponse($data = null)
    {
        return response()->json(['status_code'=> '1','data' => $data], 200);
    }


    public static function failResponse($message = '')
    {
        return response()->json(['status_code'=> '0', 'message' => $message],200);
    }

}
