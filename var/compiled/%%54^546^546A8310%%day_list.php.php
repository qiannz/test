<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:11
         compiled from admin/day_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/day_list.php', 56, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>天天向上</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
            <div class="left">
                开始日期：
                <input class="queryInput" type="text" style="width:140px;" name="start_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="<?php echo $this->_tpl_vars['request']['start_time']; ?>
" />
                &nbsp;
                结束日期：
                <input class="queryInput" type="text" style="width:140px;" name="end_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="<?php echo $this->_tpl_vars['request']['end_time']; ?>
" />
                用户名：
                <input class="queryInput" type="text" name="user_name" value="<?php echo $this->_tpl_vars['request']['user_name']; ?>
" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/day/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="mrightTop">
	<div class="fontl">
        
        <div class="left">
            <span>本时间段内共有<b style="color:red"> <?php echo $this->_tpl_vars['statistics']['total']; ?>
 </b>个用户完成本任务，共奖励<b style="color:red"> <?php if ($this->_tpl_vars['statistics']['total_amount']): ?> <?php echo $this->_tpl_vars['statistics']['total_amount']; ?>
 <?php else: ?> 0 <?php endif; ?> </b>元</span>
        </div>    
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
    	<?php if ($this->_tpl_vars['dayList']): ?>
        <tr class="tatr1">
            <td width="16%">用户名</td>
            <td width="16%">日期</td>
            <td width="16%">有效上传商品</td>
            <td width="16%">获得奖励</td>
            <td width="16%">连续天数</td>
            <td width="16%">审核时间</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_tpl_vars['dayList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php echo $this->_tpl_vars['item']['user_name']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['day_date']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['effective_upload']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['award']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['consecutive_day']; ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
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