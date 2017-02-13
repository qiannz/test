<?php /* Smarty version 2.6.27, created on 2016-02-05 17:20:06
         compiled from admin/sort_add.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
	$('#sort_form').validate({		
		errorPlacement: function(error, element){
		$(element).next('.field_notice').hide();
		$(element).after(error);
		},
		success:function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup:false,
			rules:{
			sort_id : {
				required:true,
				min : 1
			},
			sort_detail_name : {
				required:true,
                remote:{
                    url :'/admin/sort/check-sort',
                    type:'post',
                    data:{
                    	sort_detail_name : function(){
                            return $('#sort_detail_name').val();
                        },
                        id : '<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
',
                        sort_id :function(){
                            return $('#sort_id').val();
                        }
                    }
                }
			},
			sort_detail_mark : {
				required:true,
                remote:{
                    url :'/admin/sort/check-mark',
                    type:'post',
                    data:{
                    	sort_detail_name : function(){
                            return $('#sort_detail_mark').val();
                        },
                        id : '<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
'
                    }
                }
			}
		},
		messages : {
			sort_id : {
				required : '请选择类别名称',
				'min' : '请选择类别名称'
			},
			sort_detail_name : {
				required : '分类名称不能为空',
				remote : '当前类别下分类名称重复，请换一个'
			},
			sort_detail_mark : {
				required : '分类标识不能为空',
				remote : '分类标识重复，请换一个'
			}
		}
	});
});
</script>
<div id="rightTop">
  <p>全站分类</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/sort/list">分类管理</a></li>    
    <li>
        <?php if ($this->_tpl_vars['sort']['id']): ?>
        <a class="btn1" href="/admin/sort/sort-add">分类新增</a>
        <?php else: ?>
        <span>分类新增</span>
        <?php endif; ?>        
    </li>

    <li><a class="btn1" href="/admin/sort/category-list">类别管理</a></li>
    <li><a class="btn1" href="/admin/sort/category-add">类别添加</a></li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data" id="sort_form">
    <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
" />
    <input type="hidden" name="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> 类别名称:</th>
        <td class="paddingT15 wordSpacing5">
          <select name="sort_id" id="sort_id">
          <option value="0">全部</option>
          <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['category']):
?>
            <option value="<?php echo $this->_tpl_vars['category']['sort_id']; ?>
"<?php if ($this->_tpl_vars['sort']['sort_id'] == $this->_tpl_vars['category']['sort_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['category']['sort_name']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
          </select>
          <label class="field_notice"></label>
          </td>
      </tr>     
      <tr>
        <th class="paddingT15"> 分类名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="sort_detail_name" type="text" name="sort_detail_name" value="<?php echo $this->_tpl_vars['sort']['sort_detail_name']; ?>
" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th class="paddingT15"> 分类标记:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="sort_detail_mark" type="text" name="sort_detail_mark" value="<?php echo $this->_tpl_vars['sort']['sort_detail_mark']; ?>
" style="width:200px" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th></th>
        <td class="ptb20">
          <input class="formbtn" type="submit" name="Submit" value="提交" />
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