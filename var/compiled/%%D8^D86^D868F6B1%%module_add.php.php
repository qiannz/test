<?php /* Smarty version 2.6.27, created on 2016-12-01 16:23:53
         compiled from admin/module_add.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#module_from').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            m_name : {
                required : true,
                remote   : {
                url :'/admin/module/check-mod',
                type:'post',
                data:{
                    m_name : function(){
                        return $('#m_name').val();
                    },
                    pid : function() {
                        return $('#pid').val();
                    },
                    mid : '<?php echo $this->_tpl_vars['moduleRow']['mid']; ?>
'
                  }
                }
            },
            sequence : {
                number   : true
            }
        },
        messages : {
            m_name : {
                required : '模块名称不能为空',
				remote   : '该分类模块名称已经存在了，请您换一个'
            },
            sequence  : {
                number   : '此项必须为数字'
            }
        }
    });
});
</script>
<div id="rightTop">
    <p>模块管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/module/list">管理</a></li>
        <li><!-- <?php if ($this->_tpl_vars['moduleRow']['mid']): ?> --><a class="btn1" href="/admin/module/add">新增</a><!-- <?php else: ?> --><span>新增</span><!-- <?php endif; ?> --></li>
    </ul>
</div>

<div class="info">
    <form method="post" id="module_from">
    	<input type="hidden" name="mid" value="<?php echo $this->_tpl_vars['moduleRow']['mid']; ?>
" />
    	<input type="hidden" name="cont" value="<?php echo $this->_tpl_vars['cont']; ?>
" />
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    模块名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="m_name" type="text" name="m_name" value="<?php echo $this->_tpl_vars['moduleRow']['m_name']; ?>
" />
                    <label class="field_notice">模块分类名称</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="pid">所属分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="pid" name="pid">
                        <option value="">请选择...</option>
                        <?php $_from = $this->_tpl_vars['moduleArr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                        <option value="<?php echo $this->_tpl_vars['item']['mid']; ?>
"<?php if ($this->_tpl_vars['item']['mid'] == $this->_tpl_vars['pid']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['m_name']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">模块路径:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="m_path" type="text" name="m_path" value="<?php echo $this->_tpl_vars['moduleRow']['m_path']; ?>
" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">模块标记:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="mark" type="text" name="mark" value="<?php echo $this->_tpl_vars['moduleRow']['mark']; ?>
" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sequence" type="text" name="sequence" value="<?php if ($this->_tpl_vars['moduleRow']['sequence']): ?><?php echo $this->_tpl_vars['moduleRow']['sequence']; ?>
<?php else: ?>99<?php endif; ?>" />
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="formbtn" type="submit" name="Submit" value="提交" />
                    <input class="formbtn" type="reset" name="Submit2" value="重置" />
                </td>
            </tr>
        </table>
    </form>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>