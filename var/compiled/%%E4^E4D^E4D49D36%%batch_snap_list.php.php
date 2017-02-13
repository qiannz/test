<?php /* Smarty version 2.6.27, created on 2016-02-18 17:48:22
         compiled from admin/warehousing/batch_snap_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><span>入库记录</span></li>
    <li><a class="btn4" href="/admin/batch/choose-shop">新增入库</a></li>
    <li><span>入库校验</span></li>
  </ul>
</div>
<div class="tdare">
<table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
        <td>品类</td>
        <td>品名</td>
        <td>货号</td>
        <td>条形码</td>
        <td>质地</td>
        <td>适合人群</td>
        <td>年份</td>
        <td>季节</td>
        <td>颜色</td>
        <td>尺码</td>
        <td>数量</td>
        <td>原价</td>
        <td>现价</td>
        <td>卖点</td>
        <td>简介</td>
        <td>是否包邮</td>
        <td>详情</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
    	<?php $_from = $this->_tpl_vars['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
      	<td <?php if ($this->_tpl_vars['sitem']['mark'] == 1): ?>style="background-color: #FFB6C1"<?php endif; ?>><?php echo $this->_tpl_vars['sitem']['value']; ?>
</td>
        <?php endforeach; endif; unset($_from); ?>
    </tr>
    <?php endforeach; endif; unset($_from); ?>
</table>
</div>

<div class="info">
<form method="POST" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
<input type="hidden" name="sid" id="sid" value="<?php echo $this->_tpl_vars['data']['detail']['sid']; ?>
" />
<input type="hidden" name="sname" id="sname" value="<?php echo $this->_tpl_vars['data']['detail']['sname']; ?>
" />
<input type="hidden" name="batch" id="batch" value="<?php echo $this->_tpl_vars['data']['detail']['good_batch']; ?>
" />
<input type="hidden" name="stime" id="stime" value="<?php echo $this->_tpl_vars['data']['detail']['stime']; ?>
" />
<input type="hidden" name="etime" id="etime" value="<?php echo $this->_tpl_vars['data']['detail']['etime']; ?>
" />
<input type="hidden" name="action" id="action" value="on" />
<table class="infoTable">
	<tr>
        <th class="paddingT15">当前批次:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_tpl_vars['data']['detail']['good_batch']; ?>
</td>
    </tr>
    <tr>
        <th class="paddingT15">所在店铺:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_tpl_vars['data']['detail']['sname']; ?>
</td>
    </tr>  
    <tr>
        <th class="paddingT15">销售时间:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_tpl_vars['data']['detail']['stime']; ?>
 - <?php echo $this->_tpl_vars['data']['detail']['etime']; ?>
</td>    	
    </tr>
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <input type="submit" value="确认提交" name="Submit" class="formbtn1" <?php if ($this->_tpl_vars['data']['can_sub'] == 1): ?> disabled="disabled"<?php endif; ?> />
        </td>
    </tr>    
</table>
</form>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>