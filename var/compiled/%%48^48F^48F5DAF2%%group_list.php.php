<?php /* Smarty version 2.6.27, created on 2016-12-01 16:29:00
         compiled from admin/group_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
  <p>组管理</p>
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="add">添加</a></li>
  </ul>
</div>

<div class="tdare">
  <table width="400" cellspacing="0" class="dataTable">
    <!--<?php if ($this->_tpl_vars['groups']): ?>-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>组名称</td>
      <td class="handler">操作</td>
    </tr>
    <!--<?php endif; ?>-->
    <!--<?php $_from = $this->_tpl_vars['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group']):
?>-->
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['group']['gid']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['group']['g_name']; ?>
</td>
      <td>
      <span style="width: 100px">
      <a href="/admin/group/edit/id:<?php echo $this->_tpl_vars['group']['gid']; ?>
">编辑</a>　|　
      <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/group/drop/id:<?php echo $this->_tpl_vars['group']['gid']; ?>
');">删除</a>
      </span>
      </td>
    </tr>
    <!--<?php endforeach; else: ?>-->
    <tr class="no_data">
      <td colspan="3">暂无组</td>
    </tr>
    <!--<?php endif; unset($_from); ?>-->
  </table>
  <!--<?php if ($this->_tpl_vars['groups']): ?>-->
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="clear"></div>
  </div>
  <!--<?php endif; ?>-->
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>