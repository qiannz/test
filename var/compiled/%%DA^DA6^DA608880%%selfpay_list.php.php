<?php /* Smarty version 2.6.27, created on 2016-02-18 11:32:23
         compiled from admin/selfpay_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/selfpay_list.php', 98, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><span>自定义买单列表</span></li>
    <li><a class="btn4" href="/admin/ticket/user-shop/type:s">新增自定义买单券</a></li>
  </ul>
</div>
<div class="mrightTop" style="min-width:1428px;">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
			申领状态：
			<select name="app" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['app'] == 1): ?>selected="selected"<?php endif; ?>>未开始</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['app'] == 2): ?>selected="selected"<?php endif; ?>>申领中</option>
                <option value="3" <?php if ($this->_tpl_vars['request']['app'] == 3): ?>selected="selected"<?php endif; ?>>已过期</option>
            </select>
            审核状态：
            <select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['st'] == 1): ?>selected="selected"<?php endif; ?>>未审核</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['st'] == 2): ?>selected="selected"<?php endif; ?>>已审核</option>
                <option value="3" <?php if ($this->_tpl_vars['request']['st'] == 3): ?>selected="selected"<?php endif; ?>>审核不通过</option>
            </select>
            上下架：
             <select name="isa" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['isa'] == 1): ?>selected="selected"<?php endif; ?>>上架</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['isa'] == 2): ?>selected="selected"<?php endif; ?>>下架</option>
            </select>
            显示状态：
            <select name="iss" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['isa'] == 1): ?>selected="selected"<?php endif; ?>>显示</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['isa'] == 2): ?>selected="selected"<?php endif; ?>>不显示</option>
            </select>
            券名称：
            <input class="queryInput" type="text" name="title" value="<?php echo $this->_tpl_vars['request']['title']; ?>
" />
            活动名称：
            <input class="queryInput" type="text" name="act_name" value="<?php echo $this->_tpl_vars['request']['act_name']; ?>
" />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/ticket/selfpay-list">撤销检索</a>
    </form>
  </div>
<div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="tdare" style="min-width:1428px;">
  <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="280">ID</td>
      <td>券名称</td>
      <td>活动名称</td>
      <td>所属店铺</td>
      <td width="80">返利百分比</td>
      <td width="80">申领状态</td>
	  <td width="80">审核状态</td>
      <td width="80">上下架</td>
      <td width="80">显示状态</td>
      <td width="120">排序</td>
	  <td>录入时间</td>
      <td>操作</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['ticket_id']; ?>
<?php if ($this->_tpl_vars['item']['ticket_uuid']): ?><br /><?php echo $this->_tpl_vars['item']['ticket_uuid']; ?>
<?php endif; ?></td>
      <td><?php echo $this->_tpl_vars['item']['ticket_title']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['activity_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['rebates'] > 0): ?><?php echo $this->_tpl_vars['item']['rebates']; ?>
 / 100<?php endif; ?></td>
      <td>
      		<?php if ($this->_tpl_vars['item']['apply_status'] == '-1'): ?>已过期
      		<?php elseif ($this->_tpl_vars['item']['apply_status'] == '0'): ?>未开始
			<?php elseif ($this->_tpl_vars['item']['apply_status'] == '1'): ?>申领中
            <?php endif; ?>
      </td>
      <td>
      		<?php if ($this->_tpl_vars['item']['ticket_status'] == '-1'): ?>审核不通过
      		<?php elseif ($this->_tpl_vars['item']['ticket_status'] == '0'): ?>未审核
			<?php elseif ($this->_tpl_vars['item']['ticket_status'] == '1'): ?>已审核
            <?php endif; ?>
      </td>
      <td>
      		<?php if ($this->_tpl_vars['item']['is_auth'] == '0'): ?>下架
      		<?php elseif ($this->_tpl_vars['item']['is_auth'] == '1'): ?>上架
            <?php endif; ?>
      </td>
      <td>
      		<?php if ($this->_tpl_vars['item']['is_show'] == '0'): ?>不显示
      		<?php elseif ($this->_tpl_vars['item']['is_show'] == '1'): ?>显示
            <?php endif; ?>
      </td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['ticket_id']; ?>
" datatype="pint" maxvalue="999999" class="editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>
      		<a href="/admin/ticket/add-selfpay/tid:<?php echo $this->_tpl_vars['item']['ticket_id']; ?>
/uname:<?php echo $this->_tpl_vars['item']['user_name']; ?>
/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a> | 
            <?php if ($this->_tpl_vars['item']['ticket_status'] == '1' && $this->_tpl_vars['item']['apply_status'] != '-1'): ?>
            <a href="/admin/ticket/recommend/id:<?php echo $this->_tpl_vars['item']['ticket_id']; ?>
/title:<?php echo $this->_tpl_vars['item']['ticket_title']; ?>
/type:<?php echo $this->_tpl_vars['item']['ticket_type']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">推荐</a> | 
            <?php endif; ?>
            <?php if ($this->_tpl_vars['item']['ticket_status'] == 0 && $this->_tpl_vars['item']['apply_status'] != '-1'): ?>
            <a href="/admin/ticket/audit/tid:<?php echo $this->_tpl_vars['item']['ticket_id']; ?>
/type:<?php echo $this->_tpl_vars['item']['ticket_type']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">审核</a> |<?php endif; ?>
            <a href="javascript:jumpToLog('ticket', <?php echo $this->_tpl_vars['item']['ticket_id']; ?>
)">记录</a>
      </td>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="12">暂无数据</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <div id="dataFuncs">
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