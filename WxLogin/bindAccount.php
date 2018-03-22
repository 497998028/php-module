<?php
//header("Content-type: text/html; charset=utf-8");
require_once "php/WxLogin.php";
$logon =new WxLogin();
if ($_POST){
    $logon->wxBindAccount();
}else{
    $logon->index();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>pc端账号绑定</title>
    <link rel="stylesheet" href="static/style.css"/>
    <script src="static/jquery-1.11.3.min.js"></script>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript">
        var baseUrl = "<?php echo Config::BASE_URL; ?>";
        var baseUrl = "";
    </script>
</head>
<body>
<div class="login wx-bind-account">
    <div class="user-info">
        <img src="<?php echo !empty($logon->user['headimg']) ? $logon->user['headimg'] : '头像.png'; ?>" alt="头像">
        <p><?php echo !empty($logon->user['nickname']) ? $logon->user['nickname'] : '绑定pc账号'; ?></p>
    </div>
    <form action="php/bindAccount.php" method="post">
        <table>
            <tr>
                <th><img src="img/head.png" alt="账号"></th>
                <td><input class="form-control" type="text" name="account" value="" placeholder="账号"></td>
            </tr>
            <tr>
                <th><img src="img/lock.png" alt="密码"></th>
                <td><input class="form-control" type="password" name="password" value=""  placeholder="密码"></td>
            </tr>
            <tr>
                <th> </th>
                <td><input class="form-control col-6" type="text" name="code" value="" placeholder="验证码"><img src="code.png" onclick="this.src=this.src+'?'+Math.random()" class="code" /></td>
            </tr>
            <tr>
                <th> </th>
                <td><input class="btn btn-primary" type="submit" value="绑定账号" onclick="formDataSend(this)" data-request="" data-jump="1"/></td>
            </tr>
        </table>
    </form>
</div>


<script type="text/javascript">
    /**
     * 表单数据发送
     * 请求地址和回调跳转地址绑定在按钮上的。
     * 发送的表单为元素的父元素表单。
     * @param dom obj 表单下的提交按钮,this
     */
    function formDataSend(obj) {
        $(obj).parents('form').submit(function () {
            return false;
        });
        var request = $(obj).attr('data-request');
        var jump = $(obj).attr('data-jump');
        if (!jump) jump = '0';
        var data = getFormData($(obj).parents('form'));
        ajaxPost(request, data, jump);
    }


    /**
     * 异步发送数据 - POST
     * 简单的发送数据，接受返回的状态码，并跳转（也可以是刷新当前页）
     * @param url 发送地址
     * @param data 发送数据
     * @param jump 成功后跳转页面。0.不操作、1.刷新当前页、其他.跳转到指定页
     */
    function ajaxPost(url, data, jump, loadIndex) {
        $.ajax({
            type: 'post',
            url:  url,
            data: data,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.status == 0){
                    alert(res.msg);
                }else if (res.status == 1){
                    alert(res.msg);
                    if (jump == 1){
                        window.location.reload();
                    }
                }
            }
        });
    }

    /**
     * 获得表单数据
     * 请不要绑定空数组 [] name名称，里面一定要加入键值
     * @param formObj 表单对象
     * @returns {{}} 以键值对，输出表单内的所有数据。
     */
    function getFormData(formObj) {
        var data = formObj.serializeArray();
        var arr = {};
        $(data).each(function (i) {
            arr[this.name] = this.value;
        });
        return arr;
    }
</script>
</body>
</html>