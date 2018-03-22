<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/23 0023
 * Time: 10:46
 */
require_once 'Config.php';

class Mysql
{

    /**
     * 数据库链接
     * 封装了数据库链接，数据库选则
     * @param string $sql 查询语句
     * @param string $type 表示SQL类型，然后根据类型返回对应格式
     *      支持类型：find、select、create、update
     * @return array 没数据返回空数组，否则返回关联数组。
     */
    public static function Db($sql='', $type='find'){
        // MySQL 链接
        $db = mysql_connect(Config::DB_SERVER.':'.Config::DB_PORT, Config::DB_USERNAME, Config::DB_PASSWORD);
        // 指定数据库链接时的编码
        mysql_query("SET NAMES '".Config::DB_CHARSET."'");
        if (!$db){
            die('数据库错误：'.mysql_error());
        }
        // 选中数据库
        mysql_select_db(Config::DB_DATABASE, $db);
        // 发送 SQL 查询 (返回资源类型数据)
        $result = mysql_query($sql);
        // 取出 SQL 资源数据 （没有返回 false）
        if ($type === 'find'){
            // 返回单条数据
            $data = mysql_fetch_array($result);
        }elseif ($type === 'select'){
            // 返回多条数据
            $data = array();
            while($row = mysql_fetch_array($result))
            {
                $data[] = $row;
            }
        }elseif($type === 'create'){
            // 返回插入数据后的主键
            $data = mysql_insert_id();
        }elseif ($type === 'update'){
            // 更新后 返回bool
            $data = mysql_affected_rows();
        }
        mysql_close($db);
        return $data;
    }

    /**
     * 获得数组 key、value的字符串结果
     * SQL 插入数据专用
     * @param $data 一维数组
     * @return array 分别是 数组的key、value 字符串格式（每个value 还附带单引号，必须）
     */
    public static function getArrayKeyValueStr($data){
        $keys = array_keys($data);
        $keys = implode(', ', $keys);
        $value = array();
        foreach ($data as $v){
            $value[] = '\''.$v.'\'';
        }
        $value= implode(', ', $value);
        return array(
            'key' => $keys,
            'value' => $value,
        );
    }

}