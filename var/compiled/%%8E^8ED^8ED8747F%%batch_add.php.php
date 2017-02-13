<?php /* Smarty version 2.6.27, created on 2016-02-18 17:47:53
         compiled from admin/warehousing/batch_add.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/batch/list">入库记录</a></li>
    <li><a class="btn3" href="/admin/batch/choose-shop/sname:<?php echo $this->_tpl_vars['shop_name']; ?>
">店铺选择</a></li>
    <li><span>新增入库</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="<?php echo $this->_tpl_vars['shop_id']; ?>
" />
<input type="hidden" name="sname" id="sname" value="<?php echo $this->_tpl_vars['shop_name']; ?>
" />
<table class="infoTable">
    <tr>
        <th class="paddingT15">当前批次:</th>
        <td class="paddingT15 wordSpacing5"><input type="text" name="good_batch" value="<?php echo $this->_tpl_vars['good_batch']; ?>
" readonly="readonly" /></td>
    </tr>
    <tr>
        <th class="paddingT15">所在店铺:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_tpl_vars['shop_name']; ?>
</td>
    </tr>  
    <tr>
        <th class="paddingT15">销售时间:</th>
        <td class="paddingT15 wordSpacing5">
            <input class="infoTableFile2" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss'})"  value=""  />　-　
            <input class="infoTableFile2" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})"  value=""  />
            <label class="field_notice"></label>
        </td>    	
    </tr>
    <tr>
        <th class="paddingT15">选择文件:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="file"  name="uploadFile" id="uploadFile" /> 
            <a href="/data/2016.xlsx">下载模板</a>
            <label class="field_notice"></label>
        </td>    	
    </tr>    
    
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
        </td>
    </tr>    
</table>
</form>
</div>


<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript">
var validArray = ['stime', 'etime', 'uploadFile'];
$(function(){	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}	
});

$('.formbtn1').attr("disabled", false);
function checkSubmit()
{
	var len = 0;
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}	
	
	if(len == validArray.length) {
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
	
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'stime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入销售时间';	
				}
			break;
		case 'etime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入销售时间';	
				}
			break;	
		case 'uploadFile':
				if($('#' + id).val().length == 0) {
					_msg = '请选择入库文件';	
				}
			break;
	}
	if(_msg == '') {
		$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
	} else {
		$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
		return false;
	}
	return true;	
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>