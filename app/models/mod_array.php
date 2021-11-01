<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class mod_array extends Model
{
    //转一维数组
    public static function one_array(array $array,array $key_pair)
    {
        list($key,$vkey) = $key_pair;

        $result_array = array_column($array, $vkey, $key);
        $result_array = array_filter($result_array); //遍历数组中每个值并干掉值为0

        return $result_array;
    }

    //IN (ID) 使用的一维数组
    public static function sql_in(array $array, $field)
    {
        return array_unique(array_column($array,$field)) + [-1]; //array_unique去重
    }
}
