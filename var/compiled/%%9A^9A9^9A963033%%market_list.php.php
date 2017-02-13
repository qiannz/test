<?php /* Smarty version 2.6.27, created on 2016-02-02 09:22:00
         compiled from admin/market_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><span>商场管理</span></li>
    <li><a class="btn1" href="/admin/market/add-edit">新建商场</a></li>
    <?php if ($this->_tpl_vars['_ad_city'] == 'sh'): ?>
    <li><input type="button" class="formbtn1" value="同步商场" onClick="marketSync()" /></li>
    <?php endif; ?>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
          商场名称：
           <input class="queryInput" type="text" name="market" value="<?php echo $this->_tpl_vars['request']['market']; ?>
" />
          APP推荐：
           <input class="querySelect" type="radio" name="ist" value="1" <?php if ($this->_tpl_vars['request']['ist'] == 1): ?>checked="checked"<?php endif; ?> />
          <input type="submit" class="formbtn" value="查询" />
      </div>      
      <a class="left formbtn1" href="/admin/market/list">撤销检索</a>
      
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
    <?php if ($this->_tpl_vars['data']): ?>
    <tr class="tatr1">
      <td>商场名称</td>
      <td>商场地址</td>
      <td>经度</td>
      <td>纬度</td>
      <td>区域</td>
      <td>APP推荐</td>
      <td>排序</td>
      <td></td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['market_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['market_address']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['lng']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['lat']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['region_name']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['is_show'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['market_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
      <td>
      <a href="/admin/market/add-edit/id:<?php echo $this->_tpl_vars['item']['market_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a> | 
       <a href="/admin/market/recommend/id:<?php echo $this->_tpl_vars['item']['market_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">推荐</a> |   
       <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/market/delete/id:<?php echo $this->_tpl_vars['item']['market_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
');">删除</a>
        
       </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="8">暂无活动记录</td>
    </tr>
  <?php endif; unset($_from); ?>
  </table>
  <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<script type="text/javascript">
function marketSync() {
	$('.formbtn1').attr('disabled',true).attr('value', '同步中...');
	$.post('/admin/market/market-sync', {}, function(json){
		var obj = eval ("(" + json + ")" );
		if(obj.res == 100) {
			$('.formbtn1').attr('disabled',false).attr('value', '同步商场');
			$.dialog.alert('同步成功');
		} else {
			$.dialog.alert('同步失败');
		}
	});
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>