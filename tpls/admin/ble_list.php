{{include file='admin/header.php'}}
<div id="rightTop">
    <p>活动管理</p>
    <ul class="subnav">
        <li><span>活动管理</span></li>
        <li><a class="btn1" href="/admin/active/add">新增活动</a></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                活动名：
                <input class="queryInput" type="text" name="act_name" value="{{$smarty.request.act_name}}" />
                <input type="submit" class="formbtn" value="查询" />
            </div>

            <a class="left formbtn1" href="/admin/active/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table width="100%" cellspacing="0" class="dataTable">
        {{if $activeList}}
        <tr class="tatr1">
            <td>活动标题</td>
            <td>活动标识</td>
            <td>参与人数</td>
            <td>活动分享上限数</td>
            <td>活动中奖上限数</td>
            <td>活动开始时间</td>
            <td>活动结束时间</td>
            <td width="20%">操作</td>
        </tr>
        {{/if}}
        {{foreach from=$activeList item=item}}
        <tr class="tatr2">
            <td>{{$item.act_name}}</td>
            <td>{{$item.act_mart}}</td>
            <td>{{$item.attend_num}}  / {{$item.shareNum}}（<a href="/admin/active/attend-list/act_id:{{$item.act_id}}"> 查看 </a>）</td>
            <td>{{$item.share_num}}</td>
            <td>{{$item.win_num}}</td>
            <td>{{$item.start_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>{{$item.end_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>
                <a href="/admin/active/edit/act_id:{{$item.act_id}}">编辑</a> 　| 　
                <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/active/del/act_id:{{$item.act_id}}');">删除</a> 　|　
                <!--      	<a href="/admin/active/export/act_id:{{$item.act_id}}/share_num:{{$item.share_num}}/act_mart:50元中奖名单">导出50元中奖名单</a> |
                        <a href="/admin/active/export-second/act_id:{{$item.act_id}}/share_num:{{$item.share_num}}/act_mart:10元中奖名单">导出10元中奖名单</a> |-->
                <a href="/admin/active/verify/act_id:{{$item.act_id}}/act_name:{{$item.act_name}}">验证</a>
            </td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="8">暂无活动记录</td>
        </tr>
        {{/foreach}}
    </table>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
<script type="text/javascript">
    $(".dataTable tr.tatr2").mouseover(function(){
        $(this).addClass("over");
    })

    $(".dataTable tr.tatr2").mouseout(function(){
        $(this).removeClass("over");
    })
</script>
{{include file='admin/footer.php'}}