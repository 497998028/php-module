<?php

/**
 * 配置信息
 */
class Config
{

    // ===================【基本信息配置】===============================================
    /**
     *
     *
     * WX_CODE：接收微信返回code码地址
     *
     * BASE_URL：程序的基础url（所有页面跳转都建立在此基础之上）
     */
    const WX_CODE = 'http://www.xuejiaoso.com/MPM/WxLogin/bindAccount.php';
    const BASE_URL = 'http://www.xuejiaoso.com/MPM/WxLogin/bindAccount.php';
    const DOMAIN = 'http://mpm.xuejiaoso.com/index/Login/index.html';


    // ===================【微信信息配置】===============================================
    /**
     * APP_ID: 公众号APPID（必须配置）
     *
     * APP_SECRET：公众号密匙（必须配置）
     */
    const APP_ID = 'wx223cce1f1b22cccd';
    const APP_SECRET = '25af8b9baea426215ff281a4399fd038';


    // ===================【数据库配置】===============================================
    /**
     * DB_SERVER: 数据库地址
     *
     * DB_HOSTPORT：端口
     *
     * DB_USERNAME：用户名
     *
     * DB_PASSWORD：密码
     *
     * DB_CHARSET：链接字符集
     *
     * DB_DATABASE：数据库名
     */
     const DB_SERVER = '112.74.34.182';
     const DB_PORT = '3306';
     const DB_USERNAME = 'root';
     const DB_PASSWORD = '168168';
     const DB_CHARSET = 'utf8';
     const DB_DATABASE = 'mpm';

}