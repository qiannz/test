<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:00
         compiled from admin/warehousing/batch_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/warehousing/batch_list.php', 49, false),)), $this); ?>
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
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
            批次：
            <input class="queryInput" type="text" name="batch" value="<?php echo $this->_tpl_vars['request']['batch']; ?>
" />
            品牌：
            <input class="queryInput" type="text" name="bname" value="<?php echo $this->_tpl_vars['request']['bname']; ?>
" />
            入库时间：
            <input class="queryInput" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH'})"  value="<?php echo $this->_tpl_vars['request']['stime']; ?>
"  />　-　
            <input class="queryInput" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH',minDate:'#F{$dp.$D(\'stime\',{H:1})}'})"  value="<?php echo $this->_tpl_vars['request']['etime']; ?>
"  />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/batch/list">撤销检索</a>
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
      <td>批次</td>
      <td>店铺名称</td>
      <td>品牌</td>
	  <td>入库数量</td>
      <td>入库时间</td>
      <td>入库人</td>
	  <td>存放位置</td>
      <td>状态</td>
      <td>审核人</td>
	  <td>审核时间</td>
      <td>操作</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['good_batch']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['brand_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['quantity']; ?>
</td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['creator_user_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['location']; ?>
</td>
	  <td><?php if ($this->_tpl_vars['item']['status']): ?><?php else: ?>未审核<?php endif; ?></td>
      <td><?php if ($this->_tpl_vars['item']['inspector']): ?><?php echo $this->_tpl_vars['item']['inspector']; ?>
<?php endif; ?></td>    
      <td><?php if ($this->_tpl_vars['item']['checked']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['checked'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>
      		<?php if ($this->_tpl_vars['item']['status'] == 1): ?>
            
            <?php else: ?>
            <a href="javascript:audit(<?php echo $this->_tpl_vars['item']['batch_id']; ?>
)">审核</a>
            <?php endif; ?>
      </td>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="11">暂无记录</td>
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

<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript">
function audit(id) {
	alert(1);
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>