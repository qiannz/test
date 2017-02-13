<?php /* Smarty version 2.6.27, created on 2016-02-18 17:45:37
         compiled from admin/shop_choose.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="/js/jquery.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript">

function checkUser()
{
	var user_name = $('#uname').val();
	if (user_name == '') {
		$('#ts').html('请输入用户名！');
    	return false;
	}
	$.post('/admin/ticket/check-user-shop', {user_name:user_name}, function(data){
		if (data == 'ok') {
			$('#ts').html('此用户不是商家认证用户，不能添加券');
			$("#table").remove();
			return false;
		}else {
			$("#form").submit();
		}
	});
	return true;
}


</script>

<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <?php if ($this->_tpl_vars['type'] == 'v'): ?>
    	<li><a class="btn4" href="/admin/ticket/voucher-list">现金券列表</a></li>
    <?php elseif ($this->_tpl_vars['type'] == 's'): ?>
    	<li><a class="btn4" href="/admin/ticket/selfpay-list">自定义买单列表</a></li>
    <?php else: ?>    
    	<li><a class="btn4" href="/admin/ticket/coupon-list">优惠券列表</a></li>
    <?php endif; ?>    
    <li><span>店铺选择</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form">
    <input type="hidden" name="type" value="<?php echo $this->_tpl_vars['type']; ?>
"  />
       <div class="left">
            请输入用户名：
            <input class="queryInput" type="text" name="uname" id="uname" value="<?php echo $this->_tpl_vars['user_name']; ?>
" />
            店铺名称：
            <input class="queryInput" type="text" name="sname" id="sname" value="<?php echo $this->_tpl_vars['shop_name']; ?>
" />
            <input type="button" class="formbtn"  value="查询" onClick="checkUser()"/>
      </div>
         <?php if ($this->_tpl_vars['type'] == 'v'): ?>
            <a class="left formbtn1" href="/admin/ticket/user-shop/type:v">撤销检索</a> &nbsp;&nbsp;&nbsp;
        <?php elseif ($this->_tpl_vars['type'] == 's'): ?>
        	<a class="left formbtn1" href="/admin/ticket/user-shop/type:s">撤销检索</a> &nbsp;&nbsp;&nbsp;
        <?php else: ?>    
            <a class="left formbtn1" href="/admin/ticket/user-shop/type:c">撤销检索</a> &nbsp;&nbsp;&nbsp;
        <?php endif; ?>  
      <label class="field_notice" >请填写用户名选择店铺</label>
      <label class="field_notice" style="color:red" id="ts"></label>
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
  <table width="100%" cellspacing="0" class="dataTable" id="table">
    <?php if ($this->_tpl_vars['data']): ?>
    <tr class="tatr1">
      <td>所属店铺</td>
      <td>操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</td>
      <td>
      	<?php if ($this->_tpl_vars['type'] == 'v'): ?>
      		<a href="/admin/ticket/add-voucher/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/uname:<?php echo $this->_tpl_vars['user_name']; ?>
">添加现金券</a> 
        <?php elseif ($this->_tpl_vars['type'] == 's'): ?>
        	<a href="/admin/ticket/add-selfpay/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/uname:<?php echo $this->_tpl_vars['user_name']; ?>
">添加自定义买单券</a>
        <?php else: ?>
        	<a href="/admin/ticket/add-coupon/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/uname:<?php echo $this->_tpl_vars['user_name']; ?>
">添加优惠券</a> 
        <?php endif; ?>    
      </td>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="2">暂无数据</td>
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