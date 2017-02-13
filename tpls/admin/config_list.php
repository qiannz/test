{{include file='admin/header.php'}}
<div id="rightTop">
  <p>全局配置管理</p>
  <ul class="subnav">
    <li><span>全局配置管理</span></li>
    <li><a class="btn4" href="/admin/config/add-one">新增配置(单例)</a></li>
    <li><a class="btn4" href="/admin/config/add-more">新增配置(多例)</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
          全局标识解释：
           <input class="queryInput" type="text" name="config_ex" value="{{$smarty.request.config_ex}}" />
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/config/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $configs}}
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>全局设置标识(key)</td>
      <td>全局标识解释</td>
      <td>设置时间</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$configs item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.brand_id}}" /></td>
      <td>{{$item.config_key}}</td>
      <td>{{$item.config_ex}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>      	
      	<a href="/admin/config/show/config_key:{{$item.config_key}}/page:{{$page}}">查看详情</a>  
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无品牌记录</td>
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