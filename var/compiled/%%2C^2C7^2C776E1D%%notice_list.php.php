<?php /* Smarty version 2.6.27, created on 2016-02-03 10:18:57
         compiled from admin/notice_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/notice_list.php', 47, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><span>公告管理</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
           分类：<select name="type">
           			<option value="">全部</option>
                    <option value="voucher" <?php if ($this->_tpl_vars['request']['type'] == 'voucher'): ?>selected="selected"<?php endif; ?>>现金券</option>
                    <option value="buygood" <?php if ($this->_tpl_vars['request']['type'] == 'buygood'): ?>selected="selected"<?php endif; ?>>团购商品</option>
		        </select>
           		<input class="queryInput" type="text" name="title" value="<?php echo $this->_tpl_vars['request']['title']; ?>
" />
           用户名：
           <input class="queryInput" type="text" name="user_name" value="<?php echo $this->_tpl_vars['request']['user_name']; ?>
" />
           
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/notice/list">撤销检索</a>
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
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="200">用户名</td>
      <td width="300">标题</td>
      <td>公告</td>
      <td width="300">创建时间</td>
      <td width="200">操作</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['notice_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['user_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['title']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['content']; ?>
</td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
      <td>      	
      	<a href="javascript:drop_confirm('确定删除', '/admin/notice/del-all/id:<?php echo $this->_tpl_vars['item']['notice_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
')">删除</a>
      </td>  
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="5">暂无记录</td>
    </tr>
  <?php endif; unset($_from); ?>
  </table>
<div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
        <input class="formbtn batchButton" type="button" value="删除" uri="del-all" presubmit="confirm('你确定要删除？');" />                
    </div>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="clear"></div>
  </div>  
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>