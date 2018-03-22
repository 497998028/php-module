<?php
/**
 * 微信登录 demo
 * php >= 5.3
 *
 * 主要为了跨域微信登录使用，
 */
require_once 'Config.php';
require_once 'User.php';
require_once 'Mysql.php';
require_once 'function.php';

class WxLogin {
    public $user = array();

    public function __construct()
    {
        session_start();//放在第一行
        $this->user = empty($_SESSION['user']) ? $_SESSION['user']=array() : $_SESSION['user'];
    }

    /**
     * 微信登录
     *      判断是否登录，没有就去微信登录
     *      登录后判定是否绑定账号，没有就输入账号密码绑定
     *      没有问题就写入数据到 session 并跳转到首页
     * @return string|void
     */
    public function index()
    {
        $user = $this->user;
        try{
            if(empty($user['wx_uid']))
            {
                $this->code();
            }else{
                if (!empty($user['uid']))
                {
                    $res = User::wxRelationLogin($user);
                    $this->redirect(Config::DOMAIN . '?SID=' . $res);
                }
            }
        }catch (Exception $ex){
            die(json_encode(array(
                'status'    => 0,
                'msg'       => $ex->getMessage(),
            )));
        }
    }

    /**
     * 微信账号绑定
     */
    public function wxBindAccount(){
        if ($_POST){
            $data = $_POST;
            if (empty($data['account']) ||
                empty($data['password']) ||
                empty($data['code']))
            {
                die(json_encode(array(
                    'status'    => 0,
                    'msg'       => '请填写完整信息',
                )));
            }

            try{
                User::wxBindAccount($data);// 成功返回bool，失败抛出异常
                die(json_encode(array(
                    'status'    => 1,
                    'msg'       => '绑定成功',
                )));
            }catch (Exception $ex){
                die(json_encode(array(
                    'status'    => 0,
                    'msg'       => $ex->getMessage(),
                )));
            }
        }
    }

	/**
     * 微信openid
     * 如果session中没有用户id，也没有code，获取code。获取到code保存到session中，
     * @return 用户登录成功跳转到首页。
     */
    public function code(){
        $user = $this->user;
        if(empty($user['wx_uid'])){
            $code = empty($_GET['code']) ? '' : $_GET['code']; //接收数据
            $codeUrl = urlencode(Config::WX_CODE);//服务器接收code地址
            $wxAppID = Config::APP_ID;
            $wxAppSecret = Config::APP_SECRET;
            if($code == ""){
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $wxAppID . "&redirect_uri=" . $codeUrl . "&response_type=code&scope=snsapi_userinfo#wechat_redirect";
                $this->redirect($url);
            }else{
                // 用户如果点击返回之后的操作
                if(!empty($user['code']) && $user['code'] == $code){
                    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wxAppID."&redirect_uri=".$codeUrl."&response_type=code&scope=snsapi_userinfo#wechat_redirect";
                    $this->redirect($url);
                }else{
                    // 获取token
                    $user['code'] = $code;
                    $_SESSION['user'] = $user;
                    $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$wxAppID."&secret=".$wxAppSecret."&code=".$code."&grant_type=authorization_code";
                    $tokem_xml = $this->https_request($token_url);
                    $token_data = json_decode($tokem_xml,true);
                    if (!$token_data['openid']) return $this->redirect(Config::BASE_URL);

                    $is_exit = Mysql::Db("SELECT * FROM wx_user WHERE openid='". $token_data['openid'] ."' LIMIT 1", 'find'); // 数据库查询openid是否存在
                    // 如果没有该用户，就写入用户信息。存在就更新用户信息
                    if(empty($is_exit['id'])){
                        // 拉取用户信息
                        $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$token_data['access_token']."&openid=".$token_data['openid']."&lang=zh_CN ";
                        $user_xml = $this->https_request($user_url);
                        $user_data= json_decode($user_xml,true);

                        // 用户信息写入数据库
                        $data = array(
                            'openid'	=> $user_data["openid"],
                            'nickname'	=> $user_data["nickname"],
                            'headimg'	=> $user_data["headimgurl"],
                            'sex'		=> $user_data["sex"],
                            'province'	=> $user_data['province'],
                            'city'		=> $user_data['city'],
                        );
                        $data['pubdate'] = time();

                        $dataStr = Mysql::getArrayKeyValueStr($data);
                        $id = Mysql::Db("INSERT INTO wx_user (".$dataStr['key'].") VALUES (".$dataStr['value'].")", 'create');

                        $user['wx_uid']     = $id;
                        $user['uid']        = '';
                        $user['nickname']   = $data['nickname'];
                        $user['headimg']    = $data['headimg'];
                    }else{
                        $user['wx_uid']     = $is_exit['id'];
                        $user['uid']        = $is_exit['uid'];
                        $user['nickname']   = $is_exit['nickname'];
                        $user['headimg']    = $is_exit['headimg'];
                    }
                    $_SESSION['user'] = $user;
                    $this->redirect(Config::BASE_URL);
                }
            }
        }else{
            $this->redirect(Config::BASE_URL);
        }
    }

    /**
     * 访问接口
     * @param $url
     * @param null $data
     * @return mixed
     */
    private function https_request($url,$data = null){
        $curl = curl_init();
        //         curl_setopt($curl,CURLOPT_HTTPHEADER,"content-type: application/x-www-form-urlencoded;
        // charset=gb2312");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * 页面重定向
     * @param $url
     */
    private function redirect($url){
        return header('Location: ' . $url);
    }
}