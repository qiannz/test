<?php /* Smarty version 2.6.27, created on 2016-02-02 15:04:15
         compiled from admin/region.php */ ?>
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
    <li><span>行政区管理</span></li>
    <li><input type="button" class="formbtn1" value="增加行政区" id="add" /></li>
  </ul>
</div>
<div class="tdare">
  <table width="500" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="100">区名称</td>
      <td width="150">排序</td>
      <td></td>
      <td></td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['region_name']; ?>
</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['region_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
      <td><a href="javascript:edit(<?php echo $this->_tpl_vars['item']['region_id']; ?>
, '<?php echo $this->_tpl_vars['item']['region_name']; ?>
')" title="编辑">编辑</a></td>
      <td><span style="width: 100px"><a href="javascript:drop_confirm(<?php echo $this->_tpl_vars['item']['region_id']; ?>
, '<?php echo $this->_tpl_vars['item']['region_name']; ?>
')" title="删除"><img src="/images/x.png" /></a></span></td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="4">暂无记录</td>
    </tr>
  <?php endif; unset($_from); ?>
  </table>
</div>
<script type="text/javascript">
function drop_confirm(id, rname) {
	$.dialog({
		title:'警告',
		content:'确认删除？该动作不可逆转！',
		ok: function() {
			var url = '/' + _M + '/' + _C + '/del';
			$.post(url, {id:id, rname:rname}, function(data){
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


function edit(id, rname) {
	var content = '<input type="text" name="rname" id="rname" value = ' + rname  + ' />';
	$.dialog({
		title: '行政区名称',
		content: content,
		ok: function () {
			var rname = $('#rname').val();
			if(!rname) {
				alert('行政区名称不能为空');
				return false;
			} else {
				var url = '/' + _M + '/' + _C + '/edit';
				$.post(url, { id:id, rname:rname}, function(data){
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
			title: '行政区名称',
			content: '<input type="text" name="rname" id="rname" />',
			ok: function () {
				var rname = $('#rname').val();
				if(!rname) {
					alert('行政区名称不能为空');
					return false;
				} else {
					var url = '/' + _M + '/' + _C + '/add';
					$.post(url, {rname:rname}, function(data){
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