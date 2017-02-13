<?php /* Smarty version 2.6.27, created on 2016-12-01 16:29:22
         compiled from admin/purview_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
  <p>权限分配</p>
  <ul class="subnav">
    <li><span>组列表</span></li>
  </ul>
</div>

<div class="tdare">
  <table width="400" cellspacing="0" class="dataTable">
    <!--<?php if ($this->_tpl_vars['groups']): ?>-->
    <tr class="tatr1">
      <td class="handler">组名称</td>
      <td class="handler">操作</td>
    </tr>
    <!--<?php endif; ?>-->
    <!--<?php $_from = $this->_tpl_vars['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group']):
?>-->
    <tr class="tatr2">
      <td class="handler"><?php echo $this->_tpl_vars['group']['g_name']; ?>
</td>
      <td class="handler"><a href="/admin/purview/allot/gid:<?php echo $this->_tpl_vars['group']['gid']; ?>
">分配</a></td>
    </tr>
    <!--<?php endforeach; else: ?>-->
    <tr class="no_data">
      <td colspan="3">暂无组</td>
    </tr>
    <!--<?php endif; unset($_from); ?>-->
  </table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>