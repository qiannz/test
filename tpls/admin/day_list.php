{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>天天向上</span></li>
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
            <a class="left formbtn1" href="/admin/day/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="mrightTop">
	<div class="fontl">
        
        <div class="left">
            <span>本时间段内共有<b style="color:red"> {{$statistics.total}} </b>个用户完成本任务，共奖励<b style="color:red"> {{if $statistics.total_amount}} {{$statistics.total_amount}} {{else}} 0 {{/if}} </b>元</span>
        </div>    
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
    	{{if $dayList}}
        <tr class="tatr1">
            <td width="16%">用户名</td>
            <td width="16%">日期</td>
            <td width="16%">有效上传商品</td>
            <td width="16%">获得奖励</td>
            <td width="16%">连续天数</td>
            <td width="16%">审核时间</td>
        </tr>
        {{/if}}
        {{foreach from=$dayList item=item}}
        <tr class="tatr2">
            <td class="firstCell">{{$item.user_name}}</td>
            <td>{{$item.day_date}}</td>
            <td>{{$item.effective_upload}}</td>
            <td>{{$item.award}}</td>
            <td>{{$item.consecutive_day}}</td>
            <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
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