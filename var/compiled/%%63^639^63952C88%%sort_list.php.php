<?php /* Smarty version 2.6.27, created on 2016-02-18 11:27:35
         compiled from admin/sort_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>全站分类</p>
  <ul class="subnav">
    <li><span>分类管理</span></li>
    <li><a class="btn1" href="/admin/sort/sort-add">分类新增</a></li>
    <li><a class="btn1" href="/admin/sort/category-list">类别管理</a></li>
    <li><a class="btn1" href="/admin/sort/category-add">类别添加</a></li>
  </ul>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{$page}" />
  <table width="800" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>分类名称</td>
      <td width="250">分类标识</td>
      <td>所属类别</td>
      <td>排序</td>
      <td class="handler">
      <select name="tid" id="tid" onChange="jumpTo()">
      <option value="0">全部</option>
      <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['category']):
?>
      	<option value="<?php echo $this->_tpl_vars['category']['sort_id']; ?>
"<?php if ($this->_tpl_vars['tid'] == $this->_tpl_vars['category']['sort_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['category']['sort_name']; ?>
</option>
      <?php endforeach; endif; unset($_from); ?>
      </select>
      <script language="javascript">
      	function jumpTo(){
			location.href = '/admin/sort/list/tid:' + $('#tid').val()+ '/page:1';
		}
      </script>
      </td>
    </tr>
    <?php $_from = $this->_tpl_vars['sorts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sort']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['sort']['sort_detail_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['sort']['sort_detail_mark']; ?>
</td>
      <td><?php echo $this->_tpl_vars['sort']['sort_name']; ?>
</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['sort']['sequence']; ?>
</span></td>
      <td>
      <span style="width: 100px">
      <a href="/admin/sort/sort-edit/id:<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a>
       | 
      <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/sort/sort-del/id:<?php echo $this->_tpl_vars['sort']['sort_detail_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
');">删除</a>
      </span>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="6">暂无分类</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <?php if ($this->_tpl_vars['sorts']): ?>
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="sort-del" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
  </div>
  <?php endif; ?>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>