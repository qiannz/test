{{include file='admin/header.php'}}
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>用户奖励明细</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                用户名：
                <input class="queryInput" type="text" name="user_name" value="{{$smarty.request.user_name|escape}}" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/details/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>

<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        {{if $detailsList}}
        <tr class="tatr1">
            <td width="20%">用户名</td>
            <td width="20%">获奖时间</td>
            <td width="20%">获奖类型</td>
            <td width="20%">获奖金额</td>
        </tr>
        {{/if}}
        {{foreach from=$detailsList item=item}}
        <tr class="tatr2">
            <td class="firstCell">{{$item.user_name}}</td>
            <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>
            {{if $item.task_type eq 1}}天天向上
            {{elseif $item.task_type eq 2}}十全大补
            {{elseif $item.task_type eq 3}}街友最划算
            {{elseif $item.task_type eq 4}}店员最划算
            {{elseif $item.task_type eq 6}}营业员推荐返利
            {{elseif $item.task_type eq 7}}星期六活动推荐返利
            {{elseif $item.task_type eq 8}}上传商品返利
            {{elseif $item.task_type eq 9}}推荐返利
            {{elseif $item.task_type eq 10}}返利分成
            {{elseif $item.task_type eq 11}}游惠返利
            {{/if}}
            </td>
            <td>{{$item.award}}元</td>
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