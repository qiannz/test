<?php /* Smarty version 2.6.27, created on 2016-01-29 15:38:41
         compiled from admin/wbdiscount_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/wbdiscount_list.php', 38, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>微商管理</p>
  <ul class="subnav">
    <li><span>折扣管理</span></li>
    <li><a class="btn1" href="/admin/wbdiscount/add">设定折扣</a></li>
  </ul>
</div>
<div class="mrightTop">
<div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>ID</td>
      <td width="15%">起始价格</td>
      <td>结束价格</td>
      <td>折扣</td>
	  <td>创建时间</td>
      <td width="250">操作</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['min_price']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['max_price']; ?>
</td>
      <td>
      		<?php echo $this->_tpl_vars['item']['discount']/10; ?>

      </td>
      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td width="250">
      		<a href="javascript:drop_confirm('del', '<?php echo $this->_tpl_vars['item']['id']; ?>
')">删除</a> |
      		<a href="javascript:jumpToLog('wbmember', '<?php echo $this->_tpl_vars['item']['id']; ?>
')">记录</a>
      </td>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="7">暂无数据</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <div id="dataFuncs">
    <div class="left paddingT15"> &nbsp;&nbsp;
    	<?php if ($this->_tpl_vars['request']['isd'] == 1): ?>
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        <?php else: ?>
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        <?php endif; ?>
    </div>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function drop_confirm(act, id) {
	var content;
	switch(act) {
		case 'del': content = '确认删除？'; break;
		case 'del-all': 
            if($('.checkitem:checked').length == 0){
                alert('请选择删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;	
			content = '确认删除他们？'; 
			break;
		case 'un-del': content = '确认取消删除它们吗？'; 
            if($('.checkitem:checked').length == 0){
                alert('请选择恢复删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;				
			break;
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/id:' + id + '/page:' +  $('#page').val();	
		},
		cancel: true
	});
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>