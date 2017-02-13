{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><span>商品图片</span></li>
    <li><input type="button" class="formbtn" value="增加尺寸" id="add" /></li>
  </ul>
</div>
<div class="tdare">
  <table width="400" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>图片名称</td>
      <td>宽度</td>
      <td>高度</td>
      <td>是否加水印</td>
      <td></td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.config_value.name}}</td>
      <td>{{$item.config_value.width}}</td>
      <td>{{$item.config_value.height}}</td>
      {{if $item.config_value.water eq 1 }}
      <td>是</td>
      {{else}}
      <td>否</td>
      {{/if}}
      <td><span style="width: 100px"><a href="javascript:drop_confirm({{$item.config_id}}, '{{$item.config_value.name}}')" title="删除"><img src="/images/x.png" /></a></span></td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="2">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
</div>
<script type="text/javascript">
function drop_confirm(id, pname) {
	$.dialog({
		title:'警告',
		content:'确认删除？该动作不可逆转！',
		ok: function() {
			var url = '/' + _M + '/' + _C + '/del';
			$.post(url, {id:id, pname:pname}, function(data){
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

$(function(){
	$('#add').click(function(){
		$.dialog({
			title: '商品图片',
			content: '图片名称：<br/>'
				 + '<input type="text" name="name" id="name" /><br/><br/>' 
				 + '宽度：<br/>'
				 + '<input type="text" name="width" id="width" /><br/><br/>'
				 + '高度：<br/>'
				 + '<input type="text" name="height" id="height" /><br/><br/>'
				 + '是否加水印：<br/>'
				 + '<input type="radio" name="water" value="1"  />是 &nbsp;&nbsp;&nbsp;'
				 + '<input type="radio" name="water" value="0" checked="checked"/>否<br/><br/>',
			ok: function () {
				var name = $('#name').val();
				var width = $('#width').val();
				var height = $('#height').val();
				if(name == '') {
					alert('商品图片名称不能为空');
					return false;
				} else if (width == '') {
					alert('商品图片宽度不能为空');
					return false;
				} else {
					var url = '/' + _M + '/' + _C + '/add';
					$.post(url, {name:name, width:width, height:height, water:$('input[name=water]:checked').val()}, function(data){
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

$(".dataTable tr.tatr2").mouseover(function(){  
	$(this).addClass("over");
})

$(".dataTable tr.tatr2").mouseout(function(){
	$(this).removeClass("over");
})
</script>
{{include file='admin/footer.php'}}