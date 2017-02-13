{{include file='admin/header.php'}}
<div id="rightTop">
<p>
    您好，<b>{{$user.userid}}</b>，欢迎使用 【后台系统】。您上次登录的时间是  {{$user.logintime|date_format:'%Y-%m-%d %H:%M:%S'}} ，IP 是 {{$user.loginip}}
</p>
</div>
<dl id="rightCon">
<dt>系统信息</dt>
<dd>
    <table>
        <tr>
            <th>服务器操作系统:</th>
            <td class="td">{{$welcome.PHP_OS}}</td>
            <th>WEB 服务器:</th>
            <td class="td">{{$welcome.SERVER_SOFTWARE}}</td>
        </tr>
        <tr>
            <th>PHP 版本:</th>
            <td class="td">{{$welcome.PHP_VERSION}}</td>
            <th>MYSQL 版本:</th>
            <td class="td">{{$welcome.MYSQL_VERSION}}</td>
        </tr>
    </table>
</dd>
</dl>
{{include file='admin/footer.php'}}