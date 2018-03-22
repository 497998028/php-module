<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/23 0023
 * Time: 10:58
 */

/**
 * 调试打印函数
 * @param $data
 */
function p($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * 调试打印函数
 * @param $data
 */
function p2($data){
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * 生成随机字符串
 * @param $length 长度
 * @return null|string
 */
function getRandChar($length){
    $str = null;
    $strPol = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789"; //里面随机取值
    $max = strlen($strPol) - 1;

    for ($i=0; $i<$length; $i++){
        $str .= $strPol[rand(0,$max)];
    }
    return $str;
}