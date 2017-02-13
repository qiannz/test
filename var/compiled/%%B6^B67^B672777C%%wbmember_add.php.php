<?php /* Smarty version 2.6.27, created on 2016-01-29 15:25:10
         compiled from admin/wbmember_add.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/wbmember_add.php', 91, false),)), $this); ?>
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
    <li><a class="btn4" href="/admin/wbmember/list/page:<?php echo $this->_tpl_vars['page']; ?>
">会员管理</a></li>
    <?php endif; ?>
    <li><span><?php if ($this->_tpl_vars['row']['user_id']): ?>编辑会员<?php else: ?>新建会员<?php endif; ?></span></li>
  </ul>
</div>
<div class="info">
<form method="POST" enctype="multipart/form-data" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form">
	<input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
	<input type="hidden" name="uid" id="uid" value="<?php echo $this->_tpl_vars['row']['user_id']; ?>
" />
	<table class="infoTable">
		      <tr>
		        <th class="paddingT15">姓名:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" type="text" name=realname id="realname" value="<?php echo $this->_tpl_vars['row']['realname']; ?>
" style="width:140px;" />
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">手机号:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="mobile" id="mobile" value="<?php echo $this->_tpl_vars['row']['mobile']; ?>
" style="width:140px;"/>
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">会员类型:</th>
		        <td class="paddingT15 wordSpacing5">
					<select name="user_type" id="user_type">
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
		        <th class="paddingT15">申请说明:</th>
		        <td class="paddingT15 wordSpacing5">
		          <textarea class="infoTableFile2" style="width:200px; height:100px;" name="apply_reason" id="apply_reason" value="<?php echo $this->_tpl_vars['row']['apply_reason']; ?>
"></textarea>
				  <label class="field_notice">最多50个字符，汉字算一个字符</label>
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
<?php if ($this->_tpl_vars['from'] == 'add'): ?>
<div class="tdare">
	<h1>最新的会员列表</h1>
	<table width="100%" cellspacing="0" class="dataTable">
	    <tr class="tatr1">
	      <td>ID</td>
	      <td width="15%">手机号</td>
	      <td>姓名</td>
	      <td>申请类型</td>
		  <td>申请说明</td>
		  <td>申请时间</td>
		  <td>审核状态</td>
	    </tr>
	    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
	    <tr class="tatr2">
	      <td><?php echo $this->_tpl_vars['item']['user_id']; ?>
</td>
	      <td><?php echo $this->_tpl_vars['item']['mobile']; ?>
</td>
	      <td><?php echo $this->_tpl_vars['item']['realname']; ?>
</td>
	      <td>
	      		<?php if ($this->_tpl_vars['item']['user_type'] == '1'): ?>微商
	      		<?php elseif ($this->_tpl_vars['item']['user_type'] == '2'): ?>代购
	      		<?php elseif ($this->_tpl_vars['item']['user_type'] == '3'): ?>切货
	      		<?php elseif ($this->_tpl_vars['item']['user_type'] == '4'): ?>游客VIP
	      		<?php endif; ?>
	      </td>
	      <td><?php echo $this->_tpl_vars['item']['apply_reason']; ?>
</td>
	      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
	      <td>
	      		<?php if ($this->_tpl_vars['item']['user_status'] == '-1'): ?>审核拒绝
	      		<?php elseif ($this->_tpl_vars['item']['user_status'] == '0'): ?>未审核
				<?php elseif ($this->_tpl_vars['item']['user_status'] == '1'): ?>审核通过
	            <?php endif; ?>
	      </td>
	    </tr>
	   <?php endforeach; else: ?>
	    <tr class="no_data">
	      <td colspan="9">暂无数据</td>
	    </tr>
	    <?php endif; unset($_from); ?>
	</table>
</div>
<?php endif; ?>
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
			realname : {
				required : true
			},
			mobile : {
				required : true,
				isMobile : true,
				remote: {
                    type: "post",
                    url: "/admin/wbmember/mobile-is-exist",
                    data: {
                        mobile: function() {
                            return $("#mobile").val();
                        },
                        uid: function(){
							return $("#uid").val();
                        }
	                },
	                dataType: "html",
	                dataFilter: function(data, type) {
	                    if (data == 1)
	                        return true;
	                    else
	                        return false;
	                }
				}
			},
			user_type:{
				required : true
			}
        },
        messages : {
        	realname : {
				required : '请输入用户姓名'
			},
			mobile : {
				required : '请输入手机号码',
				remote   : '该手机号码已存在'
			},
			user_type : {
				required : '请选择用户类型'
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