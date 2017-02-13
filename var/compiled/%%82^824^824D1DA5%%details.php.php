<?php /* Smarty version 2.6.27, created on 2016-12-01 16:29:54
         compiled from admin/details.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'admin/details.php', 14, false),array('modifier', 'date_format', 'admin/details.php', 37, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>用户奖励明细</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
            <div class="left">
                用户名：
                <input class="queryInput" type="text" name="user_name" value="<?php echo ((is_array($_tmp=$_REQUEST['user_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/details/list">撤销检索</a>

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
        <?php if ($this->_tpl_vars['detailsList']): ?>
        <tr class="tatr1">
            <td width="20%">用户名</td>
            <td width="20%">获奖时间</td>
            <td width="20%">获奖类型</td>
            <td width="20%">获奖金额</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_tpl_vars['detailsList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php echo $this->_tpl_vars['item']['user_name']; ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
            <td>
            <?php if ($this->_tpl_vars['item']['task_type'] == 1): ?>天天向上
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 2): ?>十全大补
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 3): ?>街友最划算
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 4): ?>店员最划算
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 6): ?>营业员推荐返利
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 7): ?>星期六活动推荐返利
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 8): ?>上传商品返利
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 9): ?>推荐返利
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 10): ?>返利分成
            <?php elseif ($this->_tpl_vars['item']['task_type'] == 11): ?>游惠返利
            <?php endif; ?>
            </td>
            <td><?php echo $this->_tpl_vars['item']['award']; ?>
元</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="6">暂无记录</td>
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