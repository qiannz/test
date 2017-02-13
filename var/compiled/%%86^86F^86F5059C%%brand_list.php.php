<?php /* Smarty version 2.6.27, created on 2016-02-19 09:50:31
         compiled from admin/brand_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>品牌管理</p>
  <ul class="subnav">
    <li><span>品牌管理</span></li>
    <li><a class="btn1" href="/admin/brand/add">新增品牌</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
   	分类：
   				    <select name="store_id" id="store_id">
		            	<option value="">全部</option>
		                <?php $_from = $this->_tpl_vars['storeArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
		                	<option value="<?php echo $this->_tpl_vars['key']; ?>
"  <?php if ($_REQUEST['store_id'] == $this->_tpl_vars['key']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']; ?>
</option>
		                <?php endforeach; endif; unset($_from); ?>
		            </select>&nbsp;&nbsp;
   	   
           品牌名：
           <input class="queryInput" type="text" name="brand_name" value="<?php echo $_REQUEST['brand_name']; ?>
" />
           前台展示：
           <input type="checkbox" name="is_show" value="1" <?php if ($_REQUEST['is_show'] == 1): ?> checked <?php endif; ?> />&nbsp;&nbsp;
           是否启用：
           <input type="checkbox" name="is_enable" value="1" <?php if ($_REQUEST['is_enable'] == 1): ?> checked <?php endif; ?> />&nbsp;&nbsp;
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/brand/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_tpl_vars['brands']): ?>
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>分类</td>
      <td>中文名称</td>
      <td>英文名称</td>
      <td>首字母</td>
      <td>前台展示</td>
      <td>是否启用</td>
      <td>排序</td>
      <td>操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['brand_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['store_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['brand_name_zh']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['brand_name_en']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['firs_word']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['is_show'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
      <td><?php if ($this->_tpl_vars['item']['is_enable'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['brand_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
      <td>      	
      	<a href="/admin/brand/edit/id:<?php echo $this->_tpl_vars['item']['brand_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a> | 
        <a href="/admin/brand/recommend/id:<?php echo $this->_tpl_vars['item']['brand_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">推荐</a> | 
        <a href="/admin/brand/color-size/bid:<?php echo $this->_tpl_vars['item']['brand_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">颜色尺码</a> | 
      	<a href="/admin/brand/del/id:<?php echo $this->_tpl_vars['item']['brand_id']; ?>
">删除</a>
      </td>  
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="6">暂无品牌记录</td>
    </tr>
  <?php endif; unset($_from); ?>
  </table>
  <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<script type="text/javascript">
$(".dataTable tr.tatr2").mouseover(function(){  
	$(this).addClass("over");
})

$(".dataTable tr.tatr2").mouseout(function(){
	$(this).removeClass("over");
})
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>