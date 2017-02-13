<?php /* Smarty version 2.6.27, created on 2016-12-01 16:27:36
         compiled from admin/manager_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'admin/manager_list.php', 15, false),array('modifier', 'date_format', 'admin/manager_list.php', 44, false),array('insert', 'groupName', 'admin/manager_list.php', 50, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<div id="rightTop">
  <p>管理员管理</p>
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="add">添加</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post">
       <div class="left">
          <select class="querySelect" name="field_name"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['query_fields'],'selected' => $_REQUEST['field_name']), $this);?>
</select>
          <input class="queryInput" type="text" name="field_value" value="<?php echo $_REQUEST['field_value']; ?>
" />
          排序:
          <select class="querySelect" name="sort"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['sort_options'],'selected' => $_REQUEST['sort']), $this);?>
</select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/manager/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <!--<?php if ($this->_tpl_vars['users']): ?>-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>会员名</td>
      <td>最后登录时间</td>
      <td>最后登录IP</td>
      <td>用户组</td>
      <td>组管理员</td>
      <td>状态</td>
      <td>操作</td>
    </tr>
    <!--<?php endif; ?>-->
    <!--<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>-->
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['user']['a_id']; ?>
" <?php if ($this->_tpl_vars['user']['role_id'] == 1): ?>disabled="disabled"<?php endif; ?> /></td>
      <td><?php echo $this->_tpl_vars['user']['userid']; ?>
</td>
      <td><!--<?php if ($this->_tpl_vars['user']['logintime']): ?>--><?php echo ((is_array($_tmp=$this->_tpl_vars['user']['logintime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<!--<?php endif; ?>--></td>
      <td><?php echo $this->_tpl_vars['user']['loginip']; ?>
</td>
      <td>
      <?php if ($this->_tpl_vars['user']['role_id'] == 1): ?>
      	创始人
      <?php else: ?>
      	<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'groupName', 'gid' => $this->_tpl_vars['user']['gid'])), $this); ?>

      <?php endif; ?>
      </td>
      <td><?php if ($this->_tpl_vars['user']['role_id'] == 2): ?> <?php if ($this->_tpl_vars['user']['group_admin'] == 0): ?>否<?php elseif ($this->_tpl_vars['user']['group_admin'] == 1): ?>是<?php endif; ?> <?php endif; ?></td>
      <td><?php if ($this->_tpl_vars['user']['is_disabled'] == 0): ?>可用<?php else: ?>禁用<?php endif; ?></td>
      <td class="handler">
      <span style="width: 100px">
      	 <?php if ($this->_tpl_vars['user']['role_id'] == 1): ?>
         	 <a href="/admin/manager/edit/id:<?php echo $this->_tpl_vars['user']['id']; ?>
">编辑</a>
         <?php elseif ($this->_tpl_vars['user']['role_id'] == 2): ?>
             <a href="/admin/manager/edit/id:<?php echo $this->_tpl_vars['user']['id']; ?>
">编辑</a> |
             <a href="/admin/manager/disabled/id:<?php echo $this->_tpl_vars['user']['id']; ?>
"><?php if ($this->_tpl_vars['user']['is_disabled'] == 0): ?>禁用<?php else: ?>启用<?php endif; ?></a>
             <!--<a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/manager/drop/id:<?php echo $this->_tpl_vars['user']['id']; ?>
');">删除</a>-->
         <?php endif; ?>
      </span>
      </td>
    </tr>
    <!--<?php endforeach; else: ?>-->
    <tr class="no_data">
      <td colspan="8">暂无管理员</td>
    </tr>
    <!--<?php endif; unset($_from); ?>-->
  </table>
  <!--<?php if ($this->_tpl_vars['users']): ?>-->
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="clear"></div>
  </div>
  <!--<?php endif; ?>-->
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>