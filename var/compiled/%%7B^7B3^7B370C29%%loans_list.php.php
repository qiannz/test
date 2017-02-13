<?php /* Smarty version 2.6.27, created on 2016-02-04 10:17:09
         compiled from admin/loans_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/loans_list.php', 57, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>放款记录</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
            <div class="left">
                开始日期：
                <input class="queryInput" type="text" style="width:140px;" name="start_time" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="<?php echo $this->_tpl_vars['request']['start_time']; ?>
" />
                &nbsp;
                结束日期：
                <input class="queryInput" type="text" style="width:140px;" name="end_time" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="<?php echo $this->_tpl_vars['request']['end_time']; ?>
" />
                用户名：
                <input class="queryInput" type="text" name="user_name" value="<?php echo $this->_tpl_vars['request']['user_name']; ?>
" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/loans/list">撤销检索</a>

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
            <span>本时间段内共有<b style="color:red"> <?php echo $this->_tpl_vars['statistics']['num']; ?>
 </b>个用户提现申请处理完毕，总金额<b style="color:red"> <?php if ($this->_tpl_vars['statistics']['total_amount']): ?><?php echo $this->_tpl_vars['statistics']['total_amount']; ?>
<?php else: ?>0<?php endif; ?> </b>元</span>
        </div>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td width="8%">用户名</td>
            <td width="8%">用户身份</td>
            <td width="14%">申请日期</td>
            <td width="7%">提现金额</td>
            <td width="15%">（支付宝/银行）账号</td>
            <td width="8%">真实姓名</td>
            <td width="8%">放款人</td>
            <td width="14%">放款日期</td>
            <td width="8%">放款状态</td>
            <td width="10%">备注</td>
        </tr>

        <?php $_from = $this->_tpl_vars['loanslist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
        <tr class="tatr2">
            <td><?php echo $this->_tpl_vars['item']['user_name']; ?>
</td>
            <input type="hidden" value="<?php echo $this->_tpl_vars['item']['user_id']; ?>
"/>
            <td><?php if ($this->_tpl_vars['item']['user_type'] == 1): ?>普通用户 <?php elseif ($this->_tpl_vars['item']['user_type'] == 2): ?>认证商户 <?php elseif ($this->_tpl_vars['item']['user_type'] == 3): ?>营业员<?php endif; ?></td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['app_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['amount']; ?>
</td>
            <td><?php if ($this->_tpl_vars['item']['paypal_account']): ?><?php echo $this->_tpl_vars['item']['paypal_account']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['bank_name']; ?>
（<?php echo $this->_tpl_vars['item']['bank_number']; ?>
）<?php endif; ?></td>
            <td><?php echo $this->_tpl_vars['item']['paypal_name']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['admin_name']; ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['loans_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
            <td><?php if ($this->_tpl_vars['item']['operat_result'] == -1): ?>失败<?php else: ?>成功<?php endif; ?></td>
            <td><b style="color:red"><?php echo $this->_tpl_vars['item']['reason_of_failure']; ?>
</b></td>
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