<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:39
         compiled from admin/log_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'admin/log_list.php', 41, false),array('modifier', 'date_format', 'admin/log_list.php', 76, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript">
$(function(){
	$('#pmodule').change(function(){
		var _this = $('#cmodule');
		_this.empty();
		$.post('/admin/log/get-cmodule', {id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.m_name).val(s.mark));
			});
		});
	});
});
</script>
<div id="rightTop">
  <p>日志管理</p>
  <ul class="subnav">
    <li><span>日志列表</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
            一级模块：
			<select name="pmodule" id="pmodule"><?php echo $_REQUEST['pmodule']; ?>
###<?php echo $_REQUEST['cmodule']; ?>

            	<option value="">所有</option>
            	<?php $_from = $this->_tpl_vars['pmodule']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<option value="<?php echo $this->_tpl_vars['item']['mark']; ?>
" <?php if ($_REQUEST['pmodule'] == $this->_tpl_vars['item']['mark']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['m_name']; ?>
</option>
            	<?php endforeach; endif; unset($_from); ?>
        	</select>&nbsp;&nbsp;&nbsp;
            二级模块：
			<select name="cmodule" id="cmodule">
            	<option value="">所有</option>
				<?php $_from = $this->_tpl_vars['cmodule']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<option value="<?php echo $this->_tpl_vars['item']['mark']; ?>
" <?php if ($_REQUEST['cmodule'] == $this->_tpl_vars['item']['mark']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['m_name']; ?>
</option>
            	<?php endforeach; endif; unset($_from); ?>
        	</select> &nbsp;&nbsp;&nbsp;
        	操作动作：
			<select class="querySelect" name="activity"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['activity_fields'],'selected' => $_REQUEST['activity']), $this);?>
</select>  &nbsp;&nbsp;&nbsp;  
        	<input class="queryInput" style="width:160px;" type="text" name="field_value" value="<?php if ($this->_tpl_vars['name']): ?><?php echo $this->_tpl_vars['name']; ?>
<?php else: ?><?php echo $_REQUEST['field_value']; ?>
<?php endif; ?>" />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/log/list">撤销检索</a>
      
    </form>
  </div>
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
    <?php if ($this->_tpl_vars['logs']): ?>
    <tr class="tatr1">
      <td width="50">ID</td>
      <td width="50%">操作内容</td>
      <td>一级模块</td>
      <td>二级模块</td>
      <td>来源ID</td>
      <td>操作</td>
      <td>操作人</td>
      <td>创建时间</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['logs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['log_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['operat_info']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['pmodule']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['cmodule']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['from_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['activity_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['admin_user_name']; ?>
</td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="6">暂无日志记录</td>
    </tr>
  <?php endif; unset($_from); ?>
  </table>
  <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>