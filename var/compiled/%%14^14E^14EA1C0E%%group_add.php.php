<?php /* Smarty version 2.6.27, created on 2016-12-01 16:28:52
         compiled from admin/group_add.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#user_form').validate({
		
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
		submitHandler: function(form) {
			$(form).find(":submit").attr("disabled", true).attr("value","提交...");
			form.submit();
		},
        success:function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup:true,
        rules:{
            g_name : {
                required:true,
                remote:{
                    url :'/admin/group/check-group',
                    type:'post',
                    data:{
                        user_name : function(){
                            return $('#g_name').val();
                        },
                        id : '<?php echo $this->_tpl_vars['group']['gid']; ?>
'
                    }
                }
            }
        },
        messages : {
            g_name : {
                required : '组名称不能为空 ',
                remote : '该组名已经存在了，请您换一个'
            }
        }
    });
});
</script>
<div id="rightTop">
  <p>组管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/group/list">管理</a></li>
    <li>
      <!-- <?php if ($this->_tpl_vars['group']['gid']): ?> -->
      <a class="btn1" href="/admin/group/add">新增</a>
      <!-- <?php else: ?> -->
      <span>新增</span>
      <!-- <?php endif; ?> -->
    </li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data" id="user_form">
    <input type="hidden" name="gid" value="<?php echo $this->_tpl_vars['group']['gid']; ?>
" />
    <input type="hidden" name="name" value="<?php echo $this->_tpl_vars['group']['g_name']; ?>
" />
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> 组名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="g_name" type="text" name="g_name" value="<?php echo $this->_tpl_vars['group']['g_name']; ?>
" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交" />
          <input class="formbtn" type="reset" name="Reset" value="重置" />        </td>
      </tr>
    </table>
  </form>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>