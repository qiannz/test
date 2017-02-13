{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>放款记录</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                开始日期：
                <input class="queryInput" type="text" style="width:140px;" name="start_time" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="{{$request.start_time}}" />
                &nbsp;
                结束日期：
                <input class="queryInput" type="text" style="width:140px;" name="end_time" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="{{$request.end_time}}" />
                用户名：
                <input class="queryInput" type="text" name="user_name" value="{{$request.user_name}}" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/loans/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="mrightTop">
    <div class="fontl">

        <div class="left">
            <span>本时间段内共有<b style="color:red"> {{$statistics.num}} </b>个用户提现申请处理完毕，总金额<b style="color:red"> {{ if $statistics.total_amount}}{{$statistics.total_amount}}{{else}}0{{/if}} </b>元</span>
        </div>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td width="8%">用户名</td>
            <td width="8%">用户身份</td>
            <td width="14%">申请日期</td>
            <td width="7%">提现金额</td>
            <td width="15%">（支付宝/银行）账号</td>
            <td width="8%">真实姓名</td>
            <td width="8%">放款人</td>
            <td width="14%">放款日期</td>
            <td width="8%">放款状态</td>
            <td width="10%">备注</td>
        </tr>

        {{foreach from=$loanslist item=item}}
        <tr class="tatr2">
            <td>{{$item.user_name}}</td>
            <input type="hidden" value="{{$item.user_id}}"/>
            <td>{{if $item.user_type == 1}}普通用户 {{elseif $item.user_type == 2}}认证商户 {{elseif $item.user_type == 3}}营业员{{/if}}</td>
            <td>{{$item.app_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>{{$item.amount}}</td>
            <td>{{if $item.paypal_account}}{{$item.paypal_account}}{{else}}{{$item.bank_name}}（{{$item.bank_number}}）{{/if}}</td>
            <td>{{$item.paypal_name}}</td>
            <td>{{$item.admin_name}}</td>
            <td>{{$item.loans_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>{{if $item.operat_result == -1}}失败{{else if $item.operat_result == 1 }}成功{{/if}}</td>
            <td><b style="color:red">{{$item.reason_of_failure}}</b></td>
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
