<?php /* Smarty version 2.6.27, created on 2016-12-01 16:29:25
         compiled from admin/purview_allot.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<div id="rightTop">
  <p>权限分配</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/purview/list">组列表</a></li>
    <li><span>分配</span></li>
    <li><span style="color:red">注意：欢迎页面  必须勾选</span></li>
  </ul>
</div>

<div class="tdare">
<form method="post" id="purview_form" onSubmit="return check_select()">
  <input type="hidden" name="gid" value="<?php echo $this->_tpl_vars['gid']; ?>
" />
  <input type="hidden" name="str" id="str" value="" />
  <table width="100%" cellspacing="0" class="dataTable">
  	<!--<?php $_from = $this->_tpl_vars['moduleAll']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>-->
    <tr class="tatr2">
      <td width="200" class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['mid']; ?>
" id="<?php echo $this->_tpl_vars['item']['mid']; ?>
" onClick="checkChild(this, <?php echo $this->_tpl_vars['item']['mid']; ?>
)" mark="parent" /> <?php echo $this->_tpl_vars['item']['m_name']; ?>
</td>
      <td>      
      <!--<?php if ($this->_tpl_vars['item']['children']): ?>-->
      	<!--<?php $_from = $this->_tpl_vars['item']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item_child']):
?>-->
        		<input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item_child']['mid']; ?>
" name="ch_<?php echo $this->_tpl_vars['item_child']['pid']; ?>
" id="ch_<?php echo $this->_tpl_vars['item_child']['mid']; ?>
" mark="child" /> <?php echo $this->_tpl_vars['item_child']['m_name']; ?>

        <!--<?php endforeach; endif; unset($_from); ?>-->
      <!--<?php endif; ?>-->
      </td>
    </tr>
	<!--<?php endforeach; endif; unset($_from); ?>-->
    <tr><td class="firstCell" colspan="2"><input type="checkbox" class="checkitem" id="all" onClick="checkAll(this)" /> 全选</td></tr>
    <tr>
    <td class="firstCell" colspan="2">
      <input class="formbtn" type="submit" name="Submit" value="提交" />
      <input class="formbtn" type="reset" name="Reset" value="重置" />        </td>
    </tr>    
  </table>
</form>
</div>
<script>
function check_select(){
	var str = '';
	$("input[mark=child][type=checkbox]:checked").each(function(){
		str += $(this).val() + ',';
	});
	if(str == ''){
		alert('你还没有选择任何模块');
		return false;
	}else{		
		$("#str").val(str);
		return true;
	}
}
function checkChild(obj, mid){
	$("input[name=ch_" + mid +"][type=checkbox]").each(function(){
		if(obj.checked){
			$(this).attr('checked', true);
		}else{
			if($(this).attr('checked')){
				$(this).attr('checked', false);
			}else{
				$(this).attr('checked', true);
			}
		}
	});
	
	if($("input[name=ch_" + mid +"][type=checkbox]:checked").length > 0){
		if(!obj.checked){
			obj.checked = true;
		}
	}
}

function checkAll(obj){
	$("table.dataTable tr.tatr2 input[type=checkbox]").each(function(){
		if(obj.checked){
			$(this).attr('checked', true);
		}else{
			if($(this).attr('checked')){
				$(this).attr('checked', false);
			}else{
				$(this).attr('checked', true);
			}		
		}
	});	
}

$(function(){
	var str_sl = '<?php echo $this->_tpl_vars['checkedStr']; ?>
';
	var params = str_sl.split(',');
	if(params.length > 0){
		$("input[mark=child][type=checkbox]").each(function(){			
			for(j=0; j<params.length; j++){
				if($(this).val() == params[j]){
					$(this).attr('checked', true);
				}
			}
		});	
	}
});
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>