<?php /* Smarty version 2.6.27, created on 2016-02-03 10:12:51
         compiled from admin/good_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/good_list.php', 93, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<style>
<!--
.btndays {background: url(/css/admin/images/btn1.gif); display: block; width: 69px; height: 20px; line-height: 20px; color: #fff; text-align: center; text-decoration: none}
-->
</style>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav">
    <li><span>商品列表</span></li>
    <li><a class="btn1" href="/admin/good/add">新建商品</a></li>
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
            用户名：
            <input class="queryInput" type="text" name="uname" value="<?php echo $this->_tpl_vars['request']['uname']; ?>
" />
            商品标题：
            <input class="queryInput" type="text" name="title" value="<?php echo $this->_tpl_vars['request']['title']; ?>
" />
            <input class="querySelect" type="radio" name="isd" value="1" <?php if ($this->_tpl_vars['request']['isd'] == 1): ?>checked="checked"<?php endif; ?> /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/good/list">撤销检索</a>
    </form>
  </div>
<div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="mrightTop">
  <div class="fontl">
  		<form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
       		<span style="float:left">选择日期：</span>
       		<input class="queryInput" style="float:left;width:75px;" id="days" type="text" name="days" onFocus="WdatePicker({isShowClear:false,readOnly:false,maxDate:'now()'})"  value="<?php echo $this->_tpl_vars['request']['days']; ?>
"  />
            <span style="float:left;"><input type="checkbox" name="state" value="1" <?php if ($this->_tpl_vars['request']['state'] == 1): ?>checked="checked"<?php endif; ?> /></span>
           <span style="float:left"> <input type="submit" class="formbtn2" value="日期跳转" />&nbsp;&nbsp;&nbsp;&nbsp;</span>
           
            <?php $_from = $this->_tpl_vars['7days']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<?php if ($this->_tpl_vars['request']['days'] == $this->_tpl_vars['item']): ?>
            		<span class="querySelect" style="float:left"><?php echo $this->_tpl_vars['item']; ?>
</span>
            	<?php else: ?>
            		<a class="querySelect btndays" href="/admin/good/list/days:<?php echo $this->_tpl_vars['item']; ?>
" style="float:left"><?php echo $this->_tpl_vars['item']; ?>
</a>
            	<?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
            
      </div>
      </form>
  </div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
  <input type="hidden" name="days" id="days" value="<?php echo $this->_tpl_vars['auditDay']; ?>
" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="20%">商品标题 [<span style="color:red">原价</span> / <span style="color:green">折扣价</span>]</td>
      <td>店铺名称</td>
	  <td>用户名</td>
	  <td>录入时间</td>
	  <td width="100">状态</td>
	  <td class="table-center">喜欢 / 收藏</td>
      <td class="table-center">点击数</td>
      <?php if ($this->_tpl_vars['request']['isd'] == 0): ?>
      <td>操作</td>
      <?php endif; ?>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['good_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['good_name']; ?>
 [<span style="color:red"><?php echo $this->_tpl_vars['item']['org_price']; ?>
</span> / <span style="color:green"><?php echo $this->_tpl_vars['item']['dis_price']; ?>
</span>]</td>
      <td><a href="javascript:jumpToGoodShopList(<?php echo $this->_tpl_vars['item']['shop_id']; ?>
, <?php echo $this->_tpl_vars['item']['region_id']; ?>
, <?php echo $this->_tpl_vars['item']['circle_id']; ?>
)"><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</a></td>
      <td><?php echo $this->_tpl_vars['item']['user_name']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>
      		<?php if ($this->_tpl_vars['item']['good_status'] == '-1'): ?>审核不通过
      		<?php elseif ($this->_tpl_vars['item']['good_status'] == '0'): ?>未审核
			<?php elseif ($this->_tpl_vars['item']['good_status'] == '1'): ?>已审核
            <?php endif; ?>
      </td>
      <td class="table-center"><?php echo $this->_tpl_vars['item']['concerned_number']; ?>
 / <?php echo $this->_tpl_vars['item']['favorite_number']; ?>
 </td>
      <td class="table-center"><?php echo $this->_tpl_vars['item']['clicks']; ?>
</td>
      <?php if ($this->_tpl_vars['request']['isd'] == 0): ?>
      <td>
      		<a href="/admin/good/edit/gid:<?php echo $this->_tpl_vars['item']['good_id']; ?>
">编辑</a> | 
            <a href="javascript:drop_confirm('del', <?php echo $this->_tpl_vars['item']['good_id']; ?>
, '<?php echo $this->_tpl_vars['item']['good_name']; ?>
')">删除</a> |
            <?php if ($this->_tpl_vars['item']['good_status'] != '-1'): ?>
            <a href="/admin/good/recommend/id:<?php echo $this->_tpl_vars['item']['good_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">推荐</a> | 
            <?php endif; ?>
            <?php if ($this->_tpl_vars['item']['good_status'] == 0): ?><a href="/admin/good/audit/gid:<?php echo $this->_tpl_vars['item']['good_id']; ?>
/audit_day:<?php if ($this->_tpl_vars['auditDay']): ?><?php echo $this->_tpl_vars['auditDay']; ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d') : smarty_modifier_date_format($_tmp, '%Y-%m-%d')); ?>
<?php endif; ?>/page:<?php echo $this->_tpl_vars['page']; ?>
">审核</a> |<?php endif; ?>
            <a href="javascript:jumpToLog('good', <?php echo $this->_tpl_vars['item']['good_id']; ?>
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
    	<?php if ($this->_tpl_vars['request']['isd'] == 1): ?>
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        <?php else: ?>
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        <?php endif; ?>
                
        <input class="formbtn" type="button" value="批量审核" onClick="dropConfirm('<?php echo $this->_tpl_vars['auditDay']; ?>
')" />
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
function drop_confirm(act, id, gname, days) {
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
		
		case 'batch-audit' :
			$.post('/admin/good/check-audit', {audit_day: days}, function(data){
				if (data == 'audit') {
					$.dialog.alert('之前还有商品没有被审核！');
					return false;
				}
			});
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
			id = items;				
		break;
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/id:' + id + '/gname:' + gname + '/days:' + days + '/page:' +  $('#page').val();	
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
					var url = '/' + _M + '/' + _C + '/batch-upload';
					location.href  = url + '/id:' + items + '/days:' + days + '/page:' +  $('#page').val();				
				}
			},
			{
				value : '审核拒绝',
				callback:function(){
					var url = '/' + _M + '/' + _C + '/not-batch-audit';
					location.href  = url + '/id:' + items + '/days:' + days + '/page:' +  $('#page').val();
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