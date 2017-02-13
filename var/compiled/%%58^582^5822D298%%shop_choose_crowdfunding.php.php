<?php /* Smarty version 2.6.27, created on 2016-02-18 17:46:15
         compiled from admin/shop_choose_crowdfunding.php */ ?>
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
	$.post('/admin/commodity/check-user-shop', {user_name:user_name}, function(data){
		if (data == 'ok') {
			$('#ts').html('此用户不是店铺营业员');
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
    <li><a class="btn4" href="/admin/crowdfunding/list">一元众筹</a></li>
    <li><span>店铺选择</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form">
      <div class="left">
            请输入用户名：
            <input class="queryInput" type="text" name="uname" id="uname" value="<?php echo $this->_tpl_vars['user_name']; ?>
" />
            <input type="button" class="formbtn"  value="查询" onClick="checkUser()"/>
      </div>
    
      <a class="left formbtn1" href="/admin/crowdfunding/user-shop">撤销检索</a> &nbsp;&nbsp;&nbsp;
      <label class="field_notice" >请填写用户名选择店铺</label>
      <label class="field_notice" style="color:red" id="ts"></label>
    </form>
  </div>
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
      <td><a href="/admin/crowdfunding/add-edit/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/uname:<?php echo $this->_tpl_vars['user_name']; ?>
">添加一元众筹</a></td>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="2">暂无数据</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>