<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>我的街友会</title>
</head>

<body>

<table width="600" align="center">
    <tr>
        <td width="100">我的奖金：{{$myBonus}}</td>
        <td></td>
    </tr>
</table>

<hr>

<table width="600" align="center">
    <tr>
        <td width="100">天天向上</td>
        <td>天天向上<br>活动截止：{{$task_end_time}}<br>每天上传20件商品并且通过审核。<br>今日完成 {{$myTodayUploads}} / 20<br>奖励：5 元现金</td>
    </tr>
</table>

<hr>

<table width="600" align="center">
    <tr>
        <td width="100">十全大补</td>
        <td>十全大补<br>活动截止：{{$task_end_time}}<br>连续10天完成“天天向上”，中断则重新计算。<br>今日完成 {{$myTenDays}} / 10<br>奖励：50 元现金</td>
    </tr>
</table>

<hr>

<table width="600" align="center">
    <tr>
        <td width="100">畅游迪拜</td>
        <td>畅游迪拜<br>活动截止：{{$task_end_time}}<br>活动截止时贡献值最大（上传验证商品数量）的前10名，可获得价值10000元的迪拜游大礼包。<br>当前完成：{{$myTotalUploads}} / {{$maxUploads}} （当前最高）<br>奖励：畅游迪拜</td>
    </tr>
</table>

<hr>

<table width="600" align="center">
    <tr>
        <td width="100">街友最划算</td>
        <td>街友最划算   <em>{{$myClientEffectiveNum}}</em><br>活动截止：{{$task_end_time}}<br>购买名品街优惠券并完成消费验证，即可获得一张刮刮卡。<br>立刻刮奖<br>奖励：现金刮刮卡</td>
    </tr>
</table>
{{if $user.user_type eq 3}}
<hr>

<table width="600" align="center">
    <tr>
        <td width="100">店员最划算</td>
        <td>店员最划算   <em>{{$myClerkEffectiveNum}}</em><br>活动截止：{{$task_end_time}}<br>帮助用户完成一次优惠券消费验证，即可获得一张刮刮卡。<br>立刻刮奖<br>奖励：现金刮刮卡</td>
    </tr>
</table>
{{/if}}
</body>
</html>