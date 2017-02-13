<?php /* Smarty version 2.6.27, created on 2016-12-01 16:29:59
         compiled from admin/recommend_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/recommend_list.php', 64, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>推荐管理</p>
  <ul class="subnav">
    <li><span>推荐列表</span></li>
    <li><a class="btn1" href="/admin/recommend/add">新增推荐</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
           标题：
           <input class="queryInput" type="text" name="title" value="<?php echo $_REQUEST['title']; ?>
" style="width:160px;" />
           推荐位：
			<select name="pos_id">
            	<option value="">所有</option>
            	<?php $_from = $this->_tpl_vars['position']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            		<optgroup label="<?php echo $this->_tpl_vars['item']['pos_name']; ?>
"/>
                    <?php $_from = $this->_tpl_vars['item']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
            		<option value="<?php echo $this->_tpl_vars['sitem']['pos_id']; ?>
" <?php if ($_REQUEST['pos_id'] == $this->_tpl_vars['sitem']['pos_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['sitem']['pos_name']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
            	<?php endforeach; endif; unset($_from); ?>
        </select> 
          <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/recommend/list">撤销检索</a>
      
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
    <?php if ($this->_tpl_vars['recommend']): ?>
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>标题</td>
      <td>简介</td>
      <td>推荐位</td>
      <td>推荐位标识</td>
      <td>链接地址</td>
      <td>排序</td>
      <td>创建时间</td>
      <td>修改时间</td>
      <td>操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['recommend']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['recommend_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['title']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['summary']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['pos_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['identifier']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['www_url']; ?>
</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['recommend_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['updated'] != 0): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['updated'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>      	
      	<a href="/admin/recommend/edit/id:<?php echo $this->_tpl_vars['item']['recommend_id']; ?>
">编辑</a> | 
      	<a href="javascript:drop_confim(<?php echo $this->_tpl_vars['item']['recommend_id']; ?>
)">删除</a>
      </td>  
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="6">暂无推荐记录</td>
    </tr>
  <?php endif; unset($_from); ?>
  </table>
  <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<script type="text/javascript">
function drop_confim(rid) {
	$.dialog({
		title: '提示',
		content: '确定删除？' ,
		okValue: '确定',
		ok: function () {
			location.href = '/admin/recommend/del/id:' + rid;
		},
		cancelValue: '取消',
		cancel : true
	});	
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>