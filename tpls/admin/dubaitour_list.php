{{include file='admin/header.php'}}
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>畅游迪拜</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">

    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>

<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        {{if $dubaitour_list}}
        <tr class="tatr1">
            <td width="20%">当前排名</td>
            <td width="20%">用户名</td>
            <td width="20%">总有效上传商品</td>
            <td width="20%">共获得奖励</td>
        </tr>
        {{/if}}
        {{foreach from=$dubaitour_list key=key item=item}}
        <tr class="tatr2">
            <td class="firstCell">{{math equation="x + y" x=$key y=1}}</td>
            <td>{{$item.user_name}}</td>
            <td>{{$item.effective_upload}}</td>
            <td>{{ if $item.awardSum}}{{$item.awardSum}}{{else}}0{{/if}}</td>
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