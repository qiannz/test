{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>店员最划算</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                开始日期：
                <input class="queryInput" type="text" style="width:140px;" name="start_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="{{$request.start_time}}" />
                &nbsp;
                结束日期：
                <input class="queryInput" type="text" style="width:140px;" name="end_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="{{$request.end_time}}" />
                用户名：
                <input class="queryInput" type="text" name="user_name" value="{{$request.user_name}}" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/clerk/list">撤销检索</a>
        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="mrightTop">
    <div class="fontl">

        <div class="left">
            <span>本时间段内共有<b style="color:red"> {{$statistics.num}} </b>个用户刮奖<b style="color:red"> {{$statistics.used_num}}</b>次，总获得奖金<b style="color:red"> {{ if $statistics.total_amount}}{{$statistics.total_amount}}{{else}}0{{/if}} </b>元</span>
        </div>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        {{if $clerkList}}
        <tr class="tatr1">
            <td width="20%">用户名</td>
            <td width="20%">刮奖时间</td>
            <td width="20%">获得奖励</td>
            <td width="20%">剩余刮奖次数</td>
        </tr>
        {{/if}}
        {{foreach from=$clerkList item=item}}
        <tr class="tatr2">
            <td class="firstCell">{{$item.user_name}}</td>
            <td>{{$item.scratch_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>{{$item.award}}</td>
            <td>{{$item.remaining}}</td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="6">暂无记录</td>
        </tr>
        {{/foreach}}
    </table>

    <div id="dataFuncs">
        <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
        <div class="clear"></div>
    </div>

</div>
{{include file='admin/footer.php'}}