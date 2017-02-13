<?php /* Smarty version 2.6.27, created on 2016-02-15 15:11:32
         compiled from admin/privateletter_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'admin/privateletter_list.php', 17, false),array('modifier', 'date_format', 'admin/privateletter_list.php', 58, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/admin/inline_edit.js" ></script>
<div id="rightTop">
  <p>私信管理</p>
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="/admin/privateletter/add">添加</a></li>
    <li><a class="btn1" href="/admin/privateletter/system">待发送通知</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post">
       <div class="left">
   	接收者：
          <input class="queryInput" type="text" name="field_value" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['request']['field_value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /> 
          私信内容：
          <input class="queryInput" type="text" name="content" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['request']['content'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
          私信类型：
          <select class="querySelect" name="type">
          <?php $_from = $this->_tpl_vars['message_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
          <option value="<?php echo $this->_tpl_vars['key']; ?>
" <?php if ($this->_tpl_vars['key'] == $this->_tpl_vars['request']['type']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
          </select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
       <a class="left formbtn1" href="/admin/privateletter/list">撤销检索</a>
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
    <!--{if $messages}-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="150">接收者</td>
      <td width="150" >私信类型</td>
      <td width="150">opentype</td>
      <td width="100">from_id</td>
      <td width="150">发送者</td>
      <td width="100">友盟推送</td>
      <td>私信内容</td>
      <td>创建时间</td>
    </tr>
    <!--{/if}-->
    <?php $_from = $this->_tpl_vars['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['message']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['message']['id']; ?>
" /></td>
      <td>用户名：<?php echo $this->_tpl_vars['message']['to_user_name']; ?>
<br /> 用户ID：<?php echo $this->_tpl_vars['message']['user_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['message_type'][$this->_tpl_vars['message']['type']]; ?>
</td>
      <td><?php echo $this->_tpl_vars['message']['opentype']; ?>
</td>
      <td><?php if ($this->_tpl_vars['message']['from_id']): ?><?php echo $this->_tpl_vars['message']['from_id']; ?>
<?php endif; ?></td>
      <td><?php if ($this->_tpl_vars['message']['charter_user_id']): ?>用户名：<?php echo $this->_tpl_vars['message']['send_user_name']; ?>
<br />用户ID：<?php echo $this->_tpl_vars['message']['charter_user_id']; ?>
<?php endif; ?></td>
      <td><?php if ($this->_tpl_vars['message']['is_push'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
      <td><?php echo $this->_tpl_vars['message']['message']; ?>
</td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['message']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="9">暂无私信</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <?php if ($this->_tpl_vars['messages']): ?>
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
  <?php endif; ?>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>