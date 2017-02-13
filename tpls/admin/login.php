<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{{$_CONF.ADMIN_PAGE_TITLE}}</title>
<script type="text/javascript" src="/js/jquery.js" charset="utf-8"></script>
<link href="/css/admin/admin.css" rel="stylesheet" type="text/css" />
<link href="/css/admin/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
if (self != top)
{
    /* 在框架内，则跳出框架 */
    top.location = self.location;
}
$(function(){
    $('#userid').focus();
});
</script>
</head>
<body>
<div id="enter">
    <h1><img alt="管理后台" src="/css/admin/images/enter_logo.jpg" /></h1>
    <table>
    <form method="post">
        <tr>
            <td>用户名:</td>
            <td colspan="3"><input class="text" type="text" id="uname" name="uname" /></td>
        </tr>
        <tr>
            <td>密&nbsp;&nbsp;&nbsp;码:</td>
            <td class="width160"><input class="text" type="password" name="psw" id="psw" /></td>
            <td>验证码:</td>
            <td><input class="text2" type="text" name="captcha"  value=""/> <div class="validates"><img onclick="this.src='/admin/index/captcha/t:' + Math.round(Math.random()*10000)" style="cursor:pointer;" class="validate" src="/admin/index/captcha/t:{{$random_number}}" /></div></td>
        </tr>
        <tr>
            <th colspan="4">
            <input class="btnEnter" type="submit" name="" value="" />
            <input class="btnBack" type="button" name="" value="" onclick="go('/')"/>
            <input type="checkbox" name="remember" value="1" /> 保存登录信息
            <p>Copyright © 2012-{{$smarty.now|date_format:'%Y'}} yunyun.com Rights Reserved</p>
            </th>
        </tr>
    </form>
    </table>
</div>

</body>
</html>