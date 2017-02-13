<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> 个人站点 -- 提示信息 </title>
<link href="/css/admin/admin.css" rel="stylesheet" type="text/css" />
<style>
<!--
body {background: none}
h1 {font-size: 12px; color: #444; line-height: 55px; background: url(/css/admin/images/welcome_h1.gif); padding-left: 2%}
dl {line-height: 40px; background: url(/css/admin/images/welcome.gif) no-repeat left 10px; padding-left: 40px; margin: 35px 0 45px 15%}
dt {color: #009de6}
dd {color: #444;}
a {color: #06c}
a:hover {color: #09f}
p {color: #999; border-top: 1px solid #cbe4f5; text-align: center; padding-top: 20px;}
-->
</style>
</head>

<body>
<h1>系统消息</h1>
<dl>
    <dt>{{$message}}</dt>
    {{if $redirect}}
     <a class="forward" href="{{$redirect}}">返回上一页</a>
     <dd>如果您不做出选择，系统将自动跳转</dd>
    {{/if}}
    {{foreach from=$links item=item}}
    <dd><a href="{{$item.href}}" class="forward">{{$item.text}}</a></dd>
    {{/foreach}}
</dl>
{{if $redirect}}
<script type="text/javascript">
<!--
window.setTimeout("location.href='{{$redirect}}'", 5000);
//-->
</script>
{{/if}}
<p>Copyright © 2004-{{$smarty.now|date_format:'%Y'}} mplife.com Rights Reserved</p>
</body>
</html>