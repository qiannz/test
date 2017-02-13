<?php /* Smarty version 2.6.27, created on 2016-02-15 15:11:30
         compiled from admin/discount_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/discount_list.php', 61, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><span>折扣管理</span></li>
    <li><a class="btn1" href="/admin/discount/add">新建折扣</a></li>
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
            用户名：
            <input class="queryInput" type="text" name="uname" value="<?php echo $this->_tpl_vars['request']['uname']; ?>
" />
            折扣标题：
            <input class="queryInput" type="text" name="title" value="<?php echo $this->_tpl_vars['request']['title']; ?>
" />
            <input class="querySelect" type="checkbox" name="isd" value="1" <?php if ($this->_tpl_vars['request']['isd'] == 1): ?>checked="checked"<?php endif; ?> /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/discount/list">撤销检索</a>
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
      <td>ID</td>
      <td width="15%">折扣标题</td>
      <td>开始时间</td>
      <td>结束时间</td>
	  <td>活动地点</td>
	  <td>折扣力度</td>
	  <td>促销信息</td>
	  <td>创建时间</td>
	  <td width="100">状态</td>
	  <td class="table-center">收藏数/点击数</td>
      <?php if ($this->_tpl_vars['request']['isd'] == 0): ?>
      <td width="250">操作</td>
      <?php endif; ?>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['discount_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['discount_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['title']; ?>
</td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%I') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%I')); ?>
</td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['etime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%I') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%I')); ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['address']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['discount_start'] && $this->_tpl_vars['item']['discount_end']): ?> <?php echo $this->_tpl_vars['item']['discount_start']; ?>
 - <?php echo $this->_tpl_vars['item']['discount_end']; ?>
 折 <?php endif; ?></td>
      <td><?php echo $this->_tpl_vars['item']['promotion']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>
      		<?php if ($this->_tpl_vars['item']['discount_status'] == '-1'): ?>审核不通过
      		<?php elseif ($this->_tpl_vars['item']['discount_status'] == '0'): ?>未审核
			<?php elseif ($this->_tpl_vars['item']['discount_status'] == '1'): ?>已审核
            <?php endif; ?>
      </td>
      <td class="table-center"><?php echo $this->_tpl_vars['item']['collection_number']; ?>
/<?php echo $this->_tpl_vars['item']['view_quantity']; ?>
 </td>
      <?php if ($this->_tpl_vars['request']['isd'] == 0): ?>
      <td width="250">
      		<a href="/admin/discount/edit/did:<?php echo $this->_tpl_vars['item']['discount_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a> | 
      		<a href="javascript:drop_confirm('del', <?php echo $this->_tpl_vars['item']['discount_id']; ?>
)">删除</a> |
            <?php if ($this->_tpl_vars['item']['discount_status'] == 0): ?><a href="/admin/discount/audit/did:<?php echo $this->_tpl_vars['item']['discount_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">审核</a> |<?php endif; ?>
            <a href="/admin/discount/recommend/did:<?php echo $this->_tpl_vars['item']['discount_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">推荐</a> |
            <a href="/admin/discount/consultation/did:<?php echo $this->_tpl_vars['item']['discount_id']; ?>
">咨询</a> |
            <a href="/admin/discount/group-chat/did:<?php echo $this->_tpl_vars['item']['discount_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
/type:discount">群聊</a> |
            <a href="javascript:jumpToLog('discount', '<?php echo $this->_tpl_vars['item']['discount_id']; ?>
')">记录</a>
      </td>
      <?php endif; ?>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="<?php if ($this->_tpl_vars['request']['isd'] == 0): ?>12<?php else: ?>11<?php endif; ?>">暂无数据</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <div id="dataFuncs">
    <div class="left paddingT15"> &nbsp;&nbsp;
    	<?php if ($this->_tpl_vars['request']['isd'] == 1): ?>
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        <?php else: ?>
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        <?php endif; ?>
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
function drop_confirm(act, id) {
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
			location.href  = url + '/id:' + id + '/page:' +  $('#page').val();	
		},
		cancel: true
	});
}

function jumpToGoodShopList(sid, rid, cid)
{
	window.parent.pickTab('good');
	window.parent.openItem('good_shop_list');
	location.href="/admin/goodshop/list/shop_id:" + sid + "/region_id:" + rid + "/circle_id:" + cid;
}

function dropConfirm(days) {
	var content;
/*	$.post('/admin/good/check-audit', {audit_day: days}, function(data){
		if (data == 'audit') {
			$.dialog.alert('之前还有商品没有被审核！');
			return false;
		}
	});*/
	content = '确认审核通过它们吗？'; 
	if($('.checkitem:checked').length == 0){
		alert('请选审核对象');
		return false;
	}
   
	var items = '';
	$('.checkitem:checked:enabled').each(function(){
		items += this.value + ',';
	});
	items = items.substr(0, (items.length - 1));	
	
	$.dialog({
		title:'警告',
		content: content,
		button : [
			{
				value : '审核通过',
				callback:function(){
					var url = '/' + _M + '/' + _C + '/do-audit';
					location.href  = url + '/did:' + items + '/days:' + days + '/audit_type:1/page:' +  $('#page').val();				
				}
			},
			{
				value : '审核拒绝',
				callback:function(){
					var url = '/' + _M + '/' + _C + '/do-audit';
					location.href  = url + '/did:' + items + '/days:' + days + '/audit_type:2/page:' +  $('#page').val();
				}
			},
			{
				value: '关闭'
			}
		]
	});	
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>