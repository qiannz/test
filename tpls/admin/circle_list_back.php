{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script type="text/javascript">
$(function(){
	$('#region_id').change(function(){
		var r_id = $(this).val();
		location.href = "/admin/circle/list/id:" + r_id;
	});
});
</script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><span>商圈管理</span></li>
    <li><input type="button" class="formbtn" value="增加商圈" id="add" /></li>
  </ul>
</div>
<div class="tdare">
  <table width="400" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>
      <select id="region_id" name="region_id">
            <option value="">请选择执行区</option>
            {{foreach item=item key=key from=$region}}
            <option value="{{$key}}" {{if $key eq $region_id}} selected="selected"{{/if}} >{{$item}}</option>
            {{/foreach}}
      </select>
      </td>
    </tr>
  
    <tr class="tatr1">
      <td>商圈名称</td>
      <td>是否热门</td>
      <td></td>
      <td></td>
    </tr>
    {{foreach from=$circle key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.circle_name}}</td>
      {{if $item.is_hot eq 1}}
      <td>热门</td>
      {{else}}
      <td>非热门</td>
      {{/if}}
      <td><a href="javascript:edit({{$item.circle_id}}, '{{$item.circle_name}}', '{{$item.region_id}}', '{{$item.is_hot}}')" title="编辑">编辑</a></td>
      <td><span style="width: 100px"><a href="javascript:drop_confirm({{$item.circle_id}}, '{{$item.circle_name}}', '{{$item.region_id}}')" title="删除"><img src="/images/x.png" /></a></span></td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="2">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
</div>
<script type="text/javascript">
function drop_confirm(id, cname, region_id) {
	$.dialog({
		title:'警告',
		content:'确认删除？该动作不可逆转！',
		ok: function() {
			var url = '/' + _M + '/' + _C + '/del';
			$.post(url, {id:id, cname:cname}, function(data){
				var obj = eval('(' + data + ')');
				if (obj.res == 1) {
					$.dialog({
						title : '结果',
						content : obj.msg, 
						ok : function () {location.href =  '/' + _M + '/' + _C + '/list/id:' + region_id;},
						cancel : false
					});							
				}
			});			
		},
		cancel: true
	});
}


function edit(id, cname, region_id, is_hot) {
	var content = '商圈名称：<br/>'
		 + '<input type="text" name="cname" id="cname" value = ' + cname  + ' /><br/><br/>'
		 + '是否热门：<br/>';
	 	if(is_hot == 1) {
	 		content += '<input type="radio" name="is_hot" id="is_hot" value="1" checked="checked" />是 &nbsp;&nbsp;&nbsp;';
	 		content	+= '<input type="radio" name="is_hot" id="is_hot" value="0" />否<br/><br/>';
	 	} else {
	 		content += '<input type="radio" name="is_hot" id="is_hot" value="1" />是 &nbsp;&nbsp;&nbsp;'
	 		content	+= '<input type="radio" name="is_hot" id="is_hot" value="0" checked="checked" />否<br/><br/>';	
		}
	$.dialog({
		title: '商圈名称',
		content: content,
		ok: function () {
			var cname = $('#cname').val();
			if(cname == '') {
				alert('商圈名称不能为空');
				return false;
			} else {
				var url = '/' + _M + '/' + _C + '/edit';
				$.post(url, {id:id, region_id:region_id , cname:cname, is_hot:$('input[name=is_hot]:checked').val()}, function(data){
					var obj = eval('(' + data + ')');
					if (obj.res == 1) {
						$.dialog({
							title : '结果',
							content : obj.msg, 
							ok : function () {location.href =  '/' + _M + '/' + _C + '/list/id:' + region_id;},
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
		var region_id = $('#region_id').val();
		if (!region_id) {
			alert('请选择行政区');
			return false;
		}
		$.dialog({
			title: '商圈名称',
			content: '商圈名称：<br/>'
					 + '<input type="text" name="cname" id="cname" /><br/><br/>'
					 + '是否热门：<br/>'
					 + '<input type="radio" name="is_hot" id="is_hot" value="1"  />是 &nbsp;&nbsp;&nbsp;'
					 + '<input type="radio" name="is_hot" id="is_hot" value="0" checked="checked"/>否<br/><br/>',
			ok: function () {
				var cname = $('#cname').val();
				if(cname == '') {
					alert('商圈名称不能为空');
					return false;
				} else {
					var url = '/' + _M + '/' + _C + '/add';
					$.post(url, {region_id:region_id , cname:cname, is_hot:$('input[name=is_hot]:checked').val()}, function(data){
						var obj = eval('(' + data + ')');
						if (obj.res == 1) {
							$.dialog({
								title : '结果',
								content : obj.msg, 
								ok : function () {location.href =  '/' + _M + '/' + _C + '/list/id:' + region_id;},
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