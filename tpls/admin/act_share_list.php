{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>活动管理</p>
  <ul class="subnav">
  	<li><a class="btn1" href="/admin/active/list">活动列表</a></li>
  	<li><a class="btn1" href="/admin/active/attend-list/act_id:{{$act_id}}">参与者列表</a></li>
    <li><span>分享者列表</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
    <input type="hidden" name="act_id" value="{{$act_id}}" />
    <input type="hidden" name="mobile" value="{{$mobile}}" />
    <input type="hidden" name="page" id="page" value="{{$page}}" />
       <div class="left">
           分享者姓名：
           <input class="queryInput" type="text" name="nick_name" value="{{$smarty.request.nick_name}}" />
            分享者手机：
           <input class="queryInput" type="text" name="customer" value="{{$smarty.request.customer}}" />
           分享者IP：
           <input class="queryInput" type="text" name="ip" value="{{$smarty.request.ip}}" />
          <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/active/share-list/act_id:{{$act_id}}/mobile:{{$mobile}}">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $shareList}}
    <tr class="tatr1">
      <td width="20" class="firstCell"></td>
      <td>分享者姓名</td>
      <td>分享者IP</td>
      <td>发起者手机</td>
      <td>分享者手机</td>
      <td>分享时间</td>
    </tr>
    {{/if}}
    {{foreach from=$shareList item=item}}
    <tr class="tatr2">
      <td class="firstCell"></td>
      <td>{{$item.nick_name}}</td>
      <td>{{$item.ip}}</td>
      <td>{{$item.mobile}}</td>
      <td>{{$item.customer_phone}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无人员分享</td>
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