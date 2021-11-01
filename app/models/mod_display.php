<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class mod_display extends Model
{
    //時間戳轉日期時間
    public static function datetime($time, $timezone = null, $format = 'Y/m/d H:i')
    {
        if(empty($time))
        {
            return null;
        }

        $dis_time = date($format,$time);

        return $dis_time;
    }

    //時間戳轉日期
    public static function date($time, $timezone = null, $format = 'Y/m/d')
    {
        if(empty($time))
        {
            return null;
        }

        $dis_time = date($format, $time);

        return $dis_time;
    }

    //图片显示
    public static function img($img, $dir = 'image')
    {
        if(empty($img))
        {
            return $img;
        }

        return Storage::disk('public')->url("{$dir}/{$img}");
    }
}
