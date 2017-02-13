{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><span>商品分类</span></li>
    <li><input type="button" class="formbtn" value="增加分类" id="add" /></li>
  </ul>
</div>
<div class="tdare">
  <table width="500" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>分类名称</td>
      <td>分类标记</td>
      <td>排序</td>
      <td>APP显示</td>
      <td></td>
      <td></td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.store_name}}</td>
      <td>{{$item.mark}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.store_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>{{if $item.is_app eq 1}}是{{else}}否{{/if}}</td>
      <td><a href="javascript:edit({{$item.store_id}}, '{{$item.store_name}}', '{{$item.mark}}', {{$item.is_app}})" title="编辑">编辑</a></td>
      <td><span style="width: 100px"><a href="javascript:drop_confirm({{$item.store_id}}, '{{$item.store_name}}')" title="删除"><img src="/images/x.png" /></a></span></td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
</div>
<script type="text/javascript">
function drop_confirm(id, sname) {
	$.dialog({
		title:'警告',
		content:'确认删除？该动作不可逆转！',
		ok: function() {
			var url = '/' + _M + '/' + _C + '/del';
			$.post(url, {id:id, sname:sname}, function(data){
				var obj = eval('(' + data + ')');
				if (obj.res == 1) {
					$.dialog({
						title : '结果',
						content : obj.msg, 
						ok : function () {location.href =  '/' + _M + '/' + _C + '/list';},
						cancel : false
					});							
				}
			});			
		},
		cancel: true
	});
}


function edit(id, sname, mark, is_app) {
	var content = '分类名称：<br/><br/>'
		          + '<input type="text" name="sname" id="sname" value = "' + sname  + '" /><br/><br/>'
				  +'分类标记：<br/><br/>'
		          + '<input type="text" name="mark" id="mark" value = "' + mark  + '" /><br/><br/>'
				  + '是否APP显示：<br/>';
				  if(is_app == 1) {
					content += '<input type="radio" name="is_app" id="is_app" value="1" checked="checked" />是 &nbsp;&nbsp;&nbsp;';
					content	+= '<input type="radio" name="is_app" id="is_app" value="0" />否<br/><br/>';
				  } else {
					content += '<input type="radio" name="is_app" id="is_app" value="1" />是 &nbsp;&nbsp;&nbsp;'
					content	+= '<input type="radio" name="is_app" id="is_app" value="0" checked="checked" />否<br/><br/>';	
				  }
	$.dialog({
		title: '分类名称',
		content: content,
		ok: function () {
			var sname = $('#sname').val();
			var mark = $('#mark').val();
			if(sname == '') {
				alert('分类名称不能为空');
				return false;
			} else if(mark == ''){
				alert('分类标记不能为空');
				return false;
			}else {
				var url = '/' + _M + '/' + _C + '/edit';
				$.post(url, { id:id, sname:sname, mark:mark, is_app:$('input[name=is_app]:checked').val()}, function(data){
					var obj = eval('(' + data + ')');
					if (obj.res == 1) {
						$.dialog({
							title : '结果',
							content : obj.msg, 
							ok : function () {location.href =  '/' + _M + '/' + _C + '/list/id:' + id;},
							cancel : false
						});							
					}
				});
			}
		},
		cancel: true
	});
}


$(function(){
	$('#add').click(function(){
		$.dialog({
			title: '分类名称',
			content: '<input type="text" name="sname" id="sname" /> <br/><br/>'
					  + '是否APP端显示：<br/><br/>'
					  + '<input type="radio" name="is_app" id="is_app" value="1" checked="checked"/>是 &nbsp;&nbsp;&nbsp;'
					  + '<input type="radio" name="is_app" id="is_app" value="0" />否<br/><br/>',
			ok: function () {
				var sname = $('#sname').val();
				if(sname == '') {
					alert('分类名称不能为空');
					return false;
				} else {
					var url = '/' + _M + '/' + _C + '/add';
					$.post(url, {sname:sname, is_app:$('input[name=is_app]:checked').val()}, function(data){
						var obj = eval('(' + data + ')');
						if (obj.res == 1) {
							$.dialog({
								title : '结果',
								content : obj.msg, 
								ok : function () {location.href =  '/' + _M + '/' + _C + '/list';},
								cancel : false
							});							
						}
					});
				}
			},
			cancel : true
		});
	});
});
</script>
{{include file='admin/footer.php'}}