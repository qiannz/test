<?php /* Smarty version 2.6.27, created on 2016-02-01 10:39:52
         compiled from admin/privateletter_add.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_radios', 'admin/privateletter_add.php', 105, false),array('modifier', 'date_format', 'admin/privateletter_add.php', 121, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
var moduleLocationListJson = <?php echo $this->_tpl_vars['moduleLocationListJson']; ?>
;
<?php if ($this->_tpl_vars['recommend']['pmark']): ?>
$("#pmark").val('<?php echo $this->_tpl_vars['recommend']['pmark']; ?>
');
$("#cmark").empty().append("<option value=''>请选择</option>");
	$.each(moduleLocationListJson, function(k, v){
		if('<?php echo $this->_tpl_vars['recommend']['pmark']; ?>
' == k) {
			$.each(v.child, function(ck, cv){
				$("#cmark").append("<option value='"+cv.mark+"'>"+cv.name+"</option>");
			});
		}
	});
<?php endif; ?>

<?php if ($this->_tpl_vars['recommend']['cmark']): ?>
$("#cmark").val('<?php echo $this->_tpl_vars['recommend']['cmark']; ?>
');
<?php endif; ?>

$(function(){
	$("#pmark").change(function(){
		if(!$(this).val()) {
			$("#cmark").empty().append("<option value=''>请选择</option>");
		} else {
			var _value = $(this).val();
			$("#cmark").empty().append("<option value=''>请选择</option>");
			$.each(moduleLocationListJson, function(k, v){
				if(_value == k) {
					$.each(v.child, function(ck, cv){
						$("#cmark").append("<option value='"+cv.mark+"'>"+cv.name+"</option>");
					});
				}
			});
		}
	});
    $('#notice_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : { 
            user_name : {
                required : check_user_name  
            }, 
			content : {
				required : true,
				byteRange: [10,200,'gbk']
			}
        },
        messages : {
            user_name :{
                required     : '指定会员发送，会员名不能为空且一行一个会员名'
            },
			content :{
                required     : '请填写通知内容',
				byteRange	 : '通知内容的长度应在10-200个字符之间'
            }
        }
    });
    function check_user_name()
    {
        var rs = $(":input[name='send_type']:checked").val();
        
        return rs == 1 ? true : false; 
    }
    $("input[name='send_type']").click(function(){
        var rs = $(this).val();
        switch(rs)
        {
            case '1':
                $('#user_list').show();
                $('#sgrade_list').hide();
                $('#push_time').hide();
                break;
            case '2':
            	$('#push_time').show();
                $('#user_list').hide();
                $('#sgrade_list').hide();
                break;
        }
    });

});

</script>
<div id="rightTop">
  <p>私信管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/privateletter/list">管理</a></li>
    <li><span>添加</span></li>
    <li><a class="btn1" href="/admin/privateletter/system">待发送通知</a></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="notice_form">
<table class="infoTable">
    <tr>
        <th class="paddingT15">发送类型:</td>
        <td class="paddingT15 wordSpacing5">
           <?php echo smarty_function_html_radios(array('options' => $this->_tpl_vars['send_type'],'name' => 'send_type','checked' => 1,'autocomplete' => 'off'), $this);?>

        </td>
    </tr>
    <tr id="user_list">
        <th class="paddingT15">会员列表:</th>
        <td class="paddingT15 wordSpacing5"><textarea name="user_name" style="height:100px;" id="user_name"></textarea><span class="field_notice">每行填写一个会员名<span></td>
    </tr>
    <tr id="msg">
        <th class="paddingT15">通知内容:</td>
        <td class="paddingT15 wordSpacing5"><textarea name="content" style="width:400px; height:300px;" class="limitedContent"></textarea>
        <span class="field_notice">200字以内</span>
        </td>
    </tr>
    <tr id="push_time" style="display:none;">
    	<th class="paddingT15">发送时间:</th>
    	<td class="paddingT15 wordSpacing5">
    		<input class="infoTableFile2" style="width:140px;" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php if ($this->_tpl_vars['row']['stime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%I:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%I:%S')); ?>
<?php endif; ?>" placeholder="发送时间"/>
    	</td>
    	<span class="field_notice"></span>
    </tr>
    <tr id="is_push">
    	<th class="paddingT15">友盟推送:</th>
    	<td class="paddingT15 wordSpacing5">
    		<input type="radio" name="is_push" value="1"/>是
    		<input type="radio" name="is_push" value="0" checked="checked"/>否
    		<span class="field_notice"></span>
        </td>
    </tr>
	<tr class="link_target">
		<th class="paddingT15">链接目标:</th>
		<td class="paddingT15 wordSpacing5">
		<select name="pmark" id="pmark">
        	<option value="">请选择</option>
        	<?php $_from = $this->_tpl_vars['moduleLocationList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            <option value="<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
            <?php endforeach; endif; unset($_from); ?>
        </select>
        <select name="cmark" id="cmark"><option value="">请选择</option></select>
		</td>
	</tr>
    <tr class="link_target">
		<th class="paddingT15">目标ID:</th>
		<td class="paddingT15 wordSpacing5">
		<input class="infoTableInput" id="come_from_id" type="text" name="come_from_id" value="<?php echo $this->_tpl_vars['recommend']['come_from_id']; ?>
" />
		</td>
	</tr>
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="确定" />
          <input class="formbtn" type="reset" name="Submit2" value="重置" /></td>
    </tr>
</table>
</form>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>