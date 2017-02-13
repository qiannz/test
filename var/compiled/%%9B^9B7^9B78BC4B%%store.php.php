<?php /* Smarty version 2.6.27, created on 2016-12-01 16:29:57
         compiled from admin/store.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['store_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['mark']; ?>
</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['store_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
      <td><?php if ($this->_tpl_vars['item']['is_app'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
      <td><a href="javascript:edit(<?php echo $this->_tpl_vars['item']['store_id']; ?>
, '<?php echo $this->_tpl_vars['item']['store_name']; ?>
', '<?php echo $this->_tpl_vars['item']['mark']; ?>
', <?php echo $this->_tpl_vars['item']['is_app']; ?>
)" title="编辑">编辑</a></td>
      <td><span style="width: 100px"><a href="javascript:drop_confirm(<?php echo $this->_tpl_vars['item']['store_id']; ?>
, '<?php echo $this->_tpl_vars['item']['store_name']; ?>
')" title="删除"><img src="/images/x.png" /></a></span></td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="6">暂无记录</td>
    </tr>
  <?php endif; unset($_from); ?>
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
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>