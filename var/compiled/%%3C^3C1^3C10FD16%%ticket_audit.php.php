<?php /* Smarty version 2.6.27, created on 2016-02-22 16:32:31
         compiled from admin/ticket_audit.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#audit_form').validate({
        errorPlacement: function(error, element){
            $(element).parent().find('span').html(error);
        },
		submitHandler: function(form) {
			$(form).find(":submit").attr("disabled", true).attr("value","提交...");
			form.submit();
		},
        rules : { 
        	audit_type : {
                required : true  
            },
            reason2 : {
            	required : true
            }
            
        },
        messages : {
        	audit_type :{
                required : '请选择审核操作'
            },
            reason2 : {
            	required : '请填写不通过原因'
            }
        }
    });

	$('#sel').change(function(){
		var rs = $(this).val();
		if (rs == 4){
			$('#res').show();
		} else {
			$('#res').hide();
		}
	})
});

function couponJumpTo(type, page)
{
	var action, openItem;
	if(type == 'coupon') {
		action = 'coupon-list';
		openItem = 'ticket_list';
	} else if(type == 'voucher') {
		action = 'voucher-list';
		openItem = 'voucher_list';
	}else if(type == 'selfpay') {
		action = 'selfpay-list';
		openItem = 'selfpay_list';
	}
	window.parent.pickTab('ticket');
	window.parent.loadSubmenu();
	window.parent.openItem(openItem);
	location.href="/admin/ticket/" + action + "/page:" + page;
}
</script>

<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
  	<?php if ($this->_tpl_vars['mark'] == 'voucher'): ?>
    <li><a class="btn4" href="javascript:couponJumpTo('coupon', <?php echo $this->_tpl_vars['page']; ?>
)">优惠券列表</a></li>
    <li><a class="btn4" href="javascript:couponJumpTo('voucher', <?php echo $this->_tpl_vars['page']; ?>
)">代金券列表</a></li>
    <?php elseif ($this->_tpl_vars['mark'] == 'selfpay'): ?>
    <li><a class="btn4" href="javascript:couponJumpTo('selfpay', <?php echo $this->_tpl_vars['page']; ?>
)">自定义买单列表</a></li>
    <?php endif; ?>
    <li><span>审核券</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="audit_form">
    <input type="hidden" name="tid" id="tid" value="<?php echo $this->_tpl_vars['tid']; ?>
" >
    <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" >
    <input type="hidden" name="title" id="title" value="<?php echo $this->_tpl_vars['row']['ticket_title']; ?>
" >
    <input type="hidden" name="type" id="type" value="<?php echo $this->_tpl_vars['type']; ?>
" >
       <div class="left">
       	<input type="radio" name="audit_type" value="1"> 通过审核　
        <input type="radio" name="audit_type" value="2"> 审核不通过　
         不通过原因：
        <select class="querySelect" name="reason1" id="sel">
            <option value = "1" >虚假信息</option>
            <option value = "2" >恶意广告</option>
            <option value = "3" >敏感内容</option>
            <option value = "4" >其他原因</option>
        </select>&nbsp;
        <input id="res" class="queryInput" type="text" name="reason2" value="" style="display:none;" />
        <?php if ($this->_tpl_vars['mark'] == 'voucher' && $this->_tpl_vars['row']['ticket_class'] == 2): ?>
        返利金额：
        <input class="queryInput" type="text" name="rebates" value="<?php if ($this->_tpl_vars['row']['rebates'] > 0): ?><?php echo $this->_tpl_vars['row']['rebates']; ?>
<?php endif; ?>" />
        <?php elseif ($this->_tpl_vars['mark'] == 'selfpay' && $this->_tpl_vars['row']['ticket_class'] == 2): ?>
        返利百分比：
        <input class="queryInput" type="text" name="rebates" value="<?php if ($this->_tpl_vars['row']['rebates'] > 0): ?><?php echo $this->_tpl_vars['row']['rebates']; ?>
<?php endif; ?>" />  /  100 &nbsp;&nbsp;
        <?php endif; ?>
      	<input class="formbtn" type="submit" name="Submit" value="确定" />
        <span></span>
      </div>
    </form>
  </div>
</div>

<div class="info">
<table class="infoTable">
<iframe width=100% height=600 frameborder=0 scrolling=auto src="/home/ticket/show/tid/<?php echo $this->_tpl_vars['tid']; ?>
"></iframe>
</table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>