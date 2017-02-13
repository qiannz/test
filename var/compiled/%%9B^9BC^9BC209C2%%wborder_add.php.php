<?php /* Smarty version 2.6.27, created on 2016-02-02 15:49:09
         compiled from admin/wborder_add.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>微商系统</p>
  <ul class="subnav">
  	<?php if ($this->_tpl_vars['from'] != 'add'): ?>
    <li><a class="btn4" href="/admin/wborder/list/page:<?php echo $this->_tpl_vars['page']; ?>
">订单管理</a></li>
    <?php endif; ?>
    <li><span><?php if ($this->_tpl_vars['row']['user_id']): ?>编辑订单<?php else: ?>新建订单<?php endif; ?></span></li>
  </ul>
</div>
<div class="info">
<form method="POST" enctype="multipart/form-data" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form">
	<input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
	<input type="hidden" name="order_id" id="order_id" value="<?php echo $this->_tpl_vars['row']['order_id']; ?>
" />
	<table class="infoTable">
		      <tr>
		        <th class="paddingT15">手机号:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" type="text" name="mobile" id="mobile" autocomplete="off" value="<?php echo $this->_tpl_vars['row']['mobile']; ?>
" style="width:140px;" />
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">会员类型:</th>
		        <td class="paddingT15 wordSpacing5">
					<select name="user_type" id="user_type" autocomplete="off">
		            	<option value="">请选择会员类型</option>
		            	<option value="1" <?php if ($this->_tpl_vars['row']['user_type'] == 1): ?>selected="selected"<?php endif; ?>>微商</option>
		                <option value="2" <?php if ($this->_tpl_vars['row']['user_type'] == 2): ?>selected="selected"<?php endif; ?>>代购</option>
		                <option value="3" <?php if ($this->_tpl_vars['row']['user_type'] == 3): ?>selected="selected"<?php endif; ?>>切货</option>
		                <option value="4" <?php if ($this->_tpl_vars['row']['user_type'] == 4): ?>selected="selected"<?php endif; ?>>游客VIP</option>
		            </select>	
		            <label class="field_notice"></label>
		        </td>
		      </tr> 
			  <tr>
		        <th class="paddingT15">姓名:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="realname" id="realname" autocomplete="off" value="<?php echo $this->_tpl_vars['row']['realname']; ?>
" style="width:140px;"/>
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">总金额:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="total_price" id="total_price" autocomplete="off" value="<?php echo $this->_tpl_vars['row']['total_price']; ?>
" style="width:140px;"/>
				  <label class="field_notice"></label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">折扣:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="discount" id="discount" autocomplete="off" value="<?php if ($this->_tpl_vars['row']['discount'] != 0): ?><?php echo $this->_tpl_vars['row']['discount']; ?>
<?php endif; ?>" style="width:140px;" readonly="readonly"/>
				  <label class="field_notice"></label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">实付金额:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="pay_price" id="pay_price" autocomplete="off" value="<?php echo $this->_tpl_vars['row']['pay_price']; ?>
" style="width:140px;" readonly="readonly"/>
				  <label class="field_notice"></label>
		        </td>
		      </tr>
		    <tr>
		        <th class="paddingT15"> </th>
		        <td class="ptb20">
		          <input class="formbtn" type="submit" name="Submit" value="确定" />
		          <input class="formbtn" type="reset" name="Submit2" value="重置" />
		        </td>
		    </tr>
	</table>
</form>
</div>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript">
$(function(){
	//手机号改变
	$("#mobile").keyup(function(){
		var mobile = $.trim($(this).val());
		if( /^1[2-9][0-9]{9}$/.test(mobile) ){
			$.ajax({
				url:"/admin/wborder/get-member",
				type:"POST",
				dataType:"json",
				data:{mobile:mobile},
				success:function(data){
					if( data ){
						$("#user_type option[value='"+data.user_type+"']").attr("selected","selected");
						$("#realname").val(data.realname);
					}else{
						$("#user_type option:eq(0)").attr("selected","selected");
						$("#realname").val('');
					}
				},
				error:function(){
				}
			});	
		}
	});

	//总金额改变
	var total_price = 0;
	var discount = 0;
	$("#total_price").keyup(function(){
		var total_price = $(this).val();
		$.ajax({
			url:"/admin/wborder/get-suit-discount",
			type:"POST",
			dataType:"json",
			data:{total_price:total_price},
			success:function(data){
				if( 'discount' in data ){
					discount = data.discount;
					$("#discount").val(  discount );
					$("#pay_price").val( total_price*discount/100 );
				}else{
					$("#discount").val( '' );
					$("#pay_price").val( total_price );
				}
			},
			error:function(){
			}
		});
	});
	
	// 手机号码验证
	jQuery.validator.addMethod("isMobile", function(value, element) {
	    var length = value.length;
	    var mobile = /^1[2-9][0-9]{9}$/;
	    return this.optional(element) || (length == 11 && mobile.test(value));
	}, "请输入正确手机号码");
	$('#form').validate({
        errorPlacement: function(error, element){
			$(element).next('.field_notice').hide();
			$(element).after(error); 
        },
		success : function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup : false, 
        rules : { 
        	mobile : {
				required : true,
				isMobile : true
			},
			user_type:{ 
				required : true
			},
			realname : {
				required : true
			},
			total_price:{
				required : true,
				number : true
			},
			discount:{
				digits   : true,
				range    : [0,99]
			}
        },
        messages : {
        	mobile : {
				required : '请输入手机号码'
			},
			user_type : {
				required : '请选择用户类型'
			},
        	realname : {
				required : '请输入用户姓名'
			},
			total_price : {
				required : '请输入总金额',
				number   : '请输入合法的数字'
			},
			discount : {
				digits   : '请输入整数',
				range    : '输入的数字必须范围在0-99之间'
			},
			pay_price:{
				required : '请输入支付价格',
				number : '请输入合法的数字'
			}
        }
    });
});
function checkSubmit()
{
	if($("#form").valid())
	{
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>