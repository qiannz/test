{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>活动管理</p>
  <ul class="subnav">
  	<li><a class="btn1" href="/admin/active/list">活动列表</a></li>
    <li><span>参与者列表</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
    <input type="hidden" name="act_id" value="{{$act_id}}" />
       <div class="left">
          参与者手机：
           <input class="queryInput" type="text" name="mobile" value="{{$smarty.request.mobile}}" />
          <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/active/attend-list/act_id:{{$act_id}}">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $attendList}}
    <tr class="tatr1">
      <td width="20" class="firstCell"></td>
      <td>参与者姓名</td>
      <td>参与者手机</td>
      <td>参与时间</td>
      <td>分享次数</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$attendList item=item}}
    <tr class="tatr2">
      <td class="firstCell"></td>
      <td>{{$item.nick_name}}</td>
      <td>{{$item.phone}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{$item.shareNum}}</td>
      <td>      	
      	<a href="/admin/active/share-list/act_id:{{$item.act_id}}/mobile:{{$item.phone}}">查看分享用户名单  （人数：{{$item.shareNum}}）</a>
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无参与人员</td>
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