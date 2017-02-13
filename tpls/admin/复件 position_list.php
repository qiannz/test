{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>推荐位管理</p>
  <ul class="subnav">
    <li><span>推荐位列表</span></li>
    <li><a class="btn1" href="/admin/position/add">新增推荐位</a></li>
  </ul>
</div>
<div class="info2">
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $plist}}
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>推荐位名称</td>
      <td>推荐位标识</td>
      <td>宽度</td>
      <td>高度</td>
      <td>创建时间</td>
      <td>修改时间</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$plist item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.pos_id}}" /></td>
      <td>{{$item.pos_name}}</td>
      <td>{{$item.identifier}}</td>
      <td>{{$item.width}}</td>
      <td>{{if $item.height}}{{$item.height}}{{/if}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{if $item.updated neq 0}}{{$item.updated|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>      	
      	<a href="/admin/position/edit/id:{{$item.pos_id}}">编辑</a> | 
      	<a href="javascript:drop_confim({{$item.pos_id}})">删除</a>
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无推荐位记录</td>
    </tr>
  {{/foreach}}
  </table>
</div>
<script type="text/javascript">
function drop_confim(pid) {
	$.dialog({
		title: '提示',
		content: '确定删除？' ,
		okValue: '确定',
		ok: function () {
			location.href = '/admin/position/del/id:' + pid;
		},
		cancelValue: '取消',
		cancel : true
	});	
}
</script>
{{include file='admin/footer.php'}}