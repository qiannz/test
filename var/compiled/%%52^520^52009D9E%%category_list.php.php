<?php /* Smarty version 2.6.27, created on 2016-02-18 11:27:24
         compiled from admin/category_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
  <p>全站分类</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/sort/list">分类管理</a></li>
    <li><a class="btn1" href="/admin/sort/sort-add">分类新增</a></li>
    <li><span>类别管理</span></a></li>
    <li><a class="btn1" href="/admin/sort/category-add">类别添加</a></li>
  </ul>
</div>

<div class="tdare">
  <table width="600" cellspacing="0" class="dataTable">
    <?php if ($this->_tpl_vars['categories']): ?>
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>类别ID</td>
      <td>类别名称</td>
      <td>类别标记</td>
      <td class="handler">操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['category']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['category']['sort_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['category']['sort_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['category']['sort_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['category']['sort_unique']; ?>
</td>
      <td>
      <span style="width: 100px">
      <a href="/admin/sort/category-edit/id:<?php echo $this->_tpl_vars['category']['sort_id']; ?>
">编辑</a>
       | 
      <a href="javascript:drop_confirm('删除该类别的同时将删除所属它的所有分类，你确定要删除它吗？', '/admin/sort/category-del/id:<?php echo $this->_tpl_vars['category']['sort_id']; ?>
');">删除</a>
      </span>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="5">暂无分类</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <?php if ($this->_tpl_vars['categorys']): ?>
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="category-del" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>