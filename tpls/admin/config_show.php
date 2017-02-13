{{include file='admin/header.php'}}
<div id="rightTop">
  <p>全局配置</p>
  <ul class="subnav">
    <li><span>全局配置详情</span></li>
    <li><a class="btn4" href="/admin/config/list">全局配置列表</a></li>
  </ul>
</div>

<div class="mrightTop">

</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $configs}}
    <tr class="tatr1">
      <td width="20" class="firstCell"></td>
      <td>全局设置标识(key)</td>
      <td>全局标识解释</td>
      <td>全局设置内容(value)</td>
      <td>设置时间</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$configs item=item}}
    <tr class="tatr2">
      <td class="firstCell"></td>
      <td>{{$item.config_key}}</td>
      <td>{{$item.config_ex}}</td>
      <td>{{$item.config_value}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>      	
      	<a href="/admin/config/edit/id:{{$item.config_id}}">编辑</a> | 
      	<a href="/admin/config/delete/id:{{$item.config_id}}">删除</a>  
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无配置详情记录</td>
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