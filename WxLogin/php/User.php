<?php

/**
 * 用户模型
 */

require_once 'Config.php';
require_once 'Mysql.php';
require_once 'function.php';

class User
{
    /**
     * 验证登录
     * @param array $data 要验证的账号、密码
     * @return array|string
     */
    private static function runLogin($data){
        $one = Mysql::Db("SELECT * FROM user WHERE account='".$data['account']."' LIMIT 1",'find');
        if (!$one) return '账号不存在';
        $password = md5(substr($one['salt'], 0, 4) . md5($data['password']) . substr($one['salt'], 4));
        if ($one['password'] != $password) return '密码不正确';
        return $one;
    }


    /**
     * 微信账号绑定
     * @param array $data 要绑定的账号密码
     * @return bool 成功返回 true
     * @throws Exception 失败抛出异常
     */
    public static function wxBindAccount($data){
        $one = self::runLogin($data); // 成功返回数组，错误返回提示字符串。
        if (is_array($one)){
            $currentUser = $_SESSION['user'];
            if (!is_array($currentUser) || empty($currentUser['wx_uid'])){
                throw new Exception('数据错误');
            }
            $wxUser = Mysql::Db("SELECT * FROM wx_user WHERE uid='".$one['id']."' LIMIT 1",'find');
            if (!empty($wxUser['id'])) throw new Exception('该账户已绑定，请忽重复绑定');


            $result = Mysql::Db("UPDATE wx_user SET uid='".$one['id']."' WHERE id='".$currentUser['wx_uid']."'",'update');
            if ($result){
                $user = array_merge($currentUser,array('uid' => $one['id']));
                $_SESSION['user'] = $user;
                return true;
            }
            throw new Exception('绑定失败');
        }
        throw new Exception($one);
    }

    /**
     * 微信关联登录
     * 首先是微信登录成功，并且有绑定关联账号
     * @param array $user
     * @return string
     * @throws Exception 失败抛出异常
     */
    public static function wxRelationLogin($user){

        $key = getRandChar(32);
        $res = Mysql::Db("INSERT INTO session (s_id, content) VALUES ('".$key."', '".serialize($user)."' )",'create');
        if (!$res){
            throw new Exception('数据写入失败');
        }
        return $key;
    }

}