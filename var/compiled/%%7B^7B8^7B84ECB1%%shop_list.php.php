<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:05
         compiled from admin/shop_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/shop_list.php', 69, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><span>店铺列表</span></li>
    <li><a class="btn1" href="/admin/shop/add">新建店铺</a></li>
    <!--<li><a class="btn1" href="/admin/shop/merge">合并店铺</a></li>-->
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
			审核状态：
			<select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['st'] == 1): ?>selected="selected"<?php endif; ?>>未审核</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['st'] == 2): ?>selected="selected"<?php endif; ?>>已审核</option>
                <option value="3" <?php if ($this->_tpl_vars['request']['st'] == 3): ?>selected="selected"<?php endif; ?>>审核不通过</option>
            </select>
			时间：
			<select name="tt" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['tt'] == 1): ?>selected="selected"<?php endif; ?>>1周内</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['tt'] == 2): ?>selected="selected"<?php endif; ?>>1个月内</option>
                <option value="3" <?php if ($this->_tpl_vars['request']['tt'] == 3): ?>selected="selected"<?php endif; ?>>3个月内</option>
                <option value="4" <?php if ($this->_tpl_vars['request']['tt'] == 4): ?>selected="selected"<?php endif; ?>>1年内</option>
            </select>
<!--            用户名：
            <input class="queryInput" type="text" name="uname" value="<?php echo $this->_tpl_vars['request']['uname']; ?>
" />-->
            店铺名称：
            <input class="queryInput" type="text" name="title" value="<?php echo $this->_tpl_vars['request']['title']; ?>
" />
            <!--<input class="querySelect" type="radio" name="isd" value="1" <?php if ($this->_tpl_vars['request']['isd'] == 1): ?>checked="checked"<?php endif; ?> /> 删除-->　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/shop/list">撤销检索</a>
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
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="50">店铺ID</td>
      <td>店铺名称</td>
      <td>地址</td>
      <td>经纬度</td>
      <td>联系电话</td>
	  <td>录入时间</td>
	  <td>状态</td>
	  <td class="table-center" title="已审核的商品/未审核的商品">商品数量</td>
      <?php if ($this->_tpl_vars['request']['isd'] == 0): ?>
      <td>操作</td>
      <?php endif; ?>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['shop_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['shop_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['shop_address']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['lng'] > 0 && $this->_tpl_vars['item']['lat'] > 0): ?><?php echo $this->_tpl_vars['item']['lng']; ?>
， <?php echo $this->_tpl_vars['item']['lat']; ?>
<?php endif; ?></td>
      <td><?php echo $this->_tpl_vars['item']['phone']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>
      		<?php if ($this->_tpl_vars['item']['shop_status'] == '-1'): ?>审核不通过
      		<?php elseif ($this->_tpl_vars['item']['shop_status'] == '0'): ?>未审核
			<?php elseif ($this->_tpl_vars['item']['shop_status'] == '1'): ?>已审核
            <?php endif; ?>
      </td>
      <td class="table-center"><?php echo $this->_tpl_vars['item']['through']; ?>
 / <?php echo $this->_tpl_vars['item']['total']; ?>
 </td>
      <?php if ($this->_tpl_vars['request']['isd'] == 0): ?>
      <td>
      		<a href="/admin/shop/edit/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a> | 
            <!--<a href="javascript:drop_confirm('del', <?php echo $this->_tpl_vars['item']['shop_id']; ?>
, '<?php echo $this->_tpl_vars['item']['shop_name']; ?>
')">删除</a> |--> 
            <?php if ($this->_tpl_vars['item']['shop_status'] == 0): ?><a href="/admin/shop/audit/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">审核</a> |<?php endif; ?>
            <a href="/admin/shop/staff-management/sid:<?php echo $this->_tpl_vars['item']['shop_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">店员管理</a> |
            <a href="javascript:jumpToLog('shop', <?php echo $this->_tpl_vars['item']['shop_id']; ?>
)">记录</a>
      </td>
      <?php endif; ?>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="<?php if ($this->_tpl_vars['request']['isd'] == 0): ?>9<?php else: ?>8<?php endif; ?>">暂无数据</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <div id="dataFuncs">
    <div class="left paddingT15"> &nbsp;&nbsp;
    	<!--<?php if ($this->_tpl_vars['request']['isd'] == 1): ?>
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        <?php else: ?>
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        <input class="formbtn2" type="button" value="加入合并序列" onClick="addMerge()" />
        <input class="formbtn2" type="button" value="清空合并序列" onClick="moveMerge()" />
        <?php endif; ?>-->
    </div>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function drop_confirm(act, id, sname) {
	var content;
	switch(act) {
		case 'del': content = '确认删除？'; break;
		case 'del-all': 
            if($('.checkitem:checked').length == 0){
                alert('请选择删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;	
			content = '确认删除他们？'; 
			break;
		case 'recommend': content = '确认推荐？'; break;
		case 'un-del': content = '确认取消删除它们吗？'; 
            if($('.checkitem:checked').length == 0){
                alert('请选择恢复删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;				
		break;
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/id:' + id + '/sname:' + sname +　'/page:' +  $('#page').val();	
		},
		cancel: true
	});
}

function addMerge() {
	if($('.checkitem:checked').length == 0){
		alert('请选择合并对象');
		return false;
	}
   
	var items = '';
	$('.checkitem:checked:enabled').each(function(){
		items += this.value + ',';
	});
	items = items.substr(0, (items.length - 1));
	$.post('/admin/shop/set-shop-seq', {sid:items}, function(data){
		if(data == 'ok'){
			$.dialog({
				title : '结果',
				content : '加入合并序列成功!', 
				ok : function () {location.href = '/admin/shop/list/page:' + $('#page').val();},
				cancel : false
			});
		}
	});
}

function moveMerge() {
	$.post('/admin/shop/uset-shop-seq', {}, function(data){
		if(data == 'ok'){
			$.dialog({
				title : '结果',
				content : '清空序列成功!', 
				ok : function () {location.href = '/admin/shop/list/page:' + $('#page').val();},
				cancel : false
			});
		}
	});
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>