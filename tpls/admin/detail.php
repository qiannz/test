{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/rebate/list">店员返利</a></li>
        <li><span>查看明细</span></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">

    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td>用户名</td>
            <td>时间</td>
            <td>返利金额</td>
            <td>券名称</td>
            <td>验证码</td>
        </tr>
        {{foreach from=$detail key=key item=item}}
        <tr class="tatr2">
            <td>{{$item.user_name}}</td>
            <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>{{$item.award}}</td>
            <td>{{$item.ticket_title}}</td>
            <td>{{$item.captcha}}</td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="6">暂无数据</td>
        </tr>
        {{/foreach}}
    </table>
    <div id="dataFuncs">
        <div class="left paddingT15"> &nbsp;&nbsp;

        </div>
        <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
        <div class="clear"></div>
    </div>
</div>

{{include file='admin/footer.php'}}