<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:03
         compiled from admin/user_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/user_list.php', 75, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><span>用户列表</span></li>
    <!--<li><a class="btn4" href="/admin/user/star-edit">幸运星增/减</a></li>-->
  </ul>
</div>

<div class="mrightTop" style="min-width:1428px;">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
          	类别：	
          	<select class="querySelect" name="user_type">
          		<option value = "">全部</option>
            	<option value = "1" <?php if ($this->_tpl_vars['request']['user_type'] == '1'): ?>selected="selected"<?php endif; ?>>普通用户</option>
				<option value = "2" <?php if ($this->_tpl_vars['request']['user_type'] == '2'): ?>selected="selected"<?php endif; ?>>认证商户</option>
                <option value = "3" <?php if ($this->_tpl_vars['request']['user_type'] == '3'): ?>selected="selected"<?php endif; ?>>营业员</option>
            </select>
            状态：	
          	<select class="querySelect" name="user_status">
          		<option value = "">全部</option>
            	<option value = "1" <?php if ($this->_tpl_vars['request']['user_status'] == '1'): ?>selected="selected"<?php endif; ?>>正常</option>
				<option value = "2" <?php if ($this->_tpl_vars['request']['user_status'] == '2'): ?>selected="selected"<?php endif; ?>>黑名单</option>
                <option value = "3" <?php if ($this->_tpl_vars['request']['user_status'] == '3'): ?>selected="selected"<?php endif; ?>>禁言</option>
            </select>
            用户名：
           <input class="queryInput" type="text" name="user_name" value="<?php echo $this->_tpl_vars['request']['user_name']; ?>
" />　
           <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/user/list">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="tdare" style="min-width:1428px;">
  <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
  <table width="100%" cellspacing="0" class="dataTable">
    <!--<?php if ($this->_tpl_vars['users']): ?>-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="100">User_Id</td>
      <td width="250">用户名</td>
      <td class="table-center" width="150">类别</td>
      <td class="table-center" width="150">身份</td>
      <!--<td class="table-center" width="150">名下店铺</td>-->
      <td>活跃时间</td>
 <!--     <td>上传商品</td>
      <td>积分</td>
      <td>等级</td>
      <td>收藏 / 喜欢</td>
      <td>幸运星</td>  -->
      <td>状态</td>
      <td width="400">操作</td>
    </tr>
    <!--<?php endif; ?>-->
    <!--<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>-->
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['item']['user_id']; ?>
" /></td>
      <td><?php echo $this->_tpl_vars['item']['user_id']; ?>
</td>
      <td>
          <a href="javascript:jumpToList('usergood', <?php echo $this->_tpl_vars['item']['user_id']; ?>
, '<?php echo $this->_tpl_vars['item']['user_name']; ?>
')"><?php echo $this->_tpl_vars['item']['user_name']; ?>
</a>
          <?php if ($this->_tpl_vars['item']['gag_status'] == '1'): ?><span style="color: #F00; font-weight:bold;" title="禁言用户，到期时间：<?php echo $this->_tpl_vars['item']['gag_time_str']; ?>
"> X</span><?php endif; ?>	
          <br/><?php echo $this->_tpl_vars['item']['uuid']; ?>

      </td>
      <td class="table-center" ><?php if ($this->_tpl_vars['item']['user_type'] == '1'): ?>普通用户<?php elseif ($this->_tpl_vars['item']['user_type'] == '2'): ?>认证商户<?php elseif ($this->_tpl_vars['item']['user_type'] == '3'): ?>营业员<?php endif; ?></td>
      <td class="table-center" ><?php if ($this->_tpl_vars['item']['user_type_shop'] == '1'): ?>营业员<?php elseif ($this->_tpl_vars['item']['user_type_shop'] == '2'): ?>店长<?php elseif ($this->_tpl_vars['item']['user_type_shop'] == '3'): ?>收银员<?php endif; ?></td>
      <!--<td class="table-center"><a href="javascript:view(<?php echo $this->_tpl_vars['item']['user_id']; ?>
)">查看</a></td>-->
      <td>
        <?php if ($this->_tpl_vars['item']['lasted_login_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['lasted_login_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?><br>
        <?php if ($this->_tpl_vars['item']['lasted_login_ip']): ?><?php echo $this->_tpl_vars['item']['lasted_login_ip']; ?>
<?php endif; ?>
      </td>
<!--      <td><?php echo $this->_tpl_vars['item']['through']; ?>
 / <?php echo $this->_tpl_vars['item']['total']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['integral']): ?><?php echo $this->_tpl_vars['item']['integral']; ?>
<?php endif; ?></td>
      <td><?php if ($this->_tpl_vars['item']['grade']): ?><?php echo $this->_tpl_vars['item']['grade']; ?>
<?php endif; ?></td>
      <td><a href="javascript:jumpToList('usergoodcollect', <?php echo $this->_tpl_vars['item']['user_id']; ?>
, '<?php echo $this->_tpl_vars['item']['user_name']; ?>
')"><?php echo $this->_tpl_vars['item']['favorite_number']; ?>
</a> 　/ 　<a href="javascript:jumpToList('usergoodlike', <?php echo $this->_tpl_vars['item']['user_id']; ?>
, '<?php echo $this->_tpl_vars['item']['user_name']; ?>
')"><?php echo $this->_tpl_vars['item']['concerned_number']; ?>
</a></td>
      <td><?php echo $this->_tpl_vars['item']['star']; ?>
</td>  -->
      <td>
      		<?php if ($this->_tpl_vars['item']['user_status'] == '0'): ?>正常
            <?php elseif ($this->_tpl_vars['item']['user_status'] == '1'): ?>黑名单
            <?php elseif ($this->_tpl_vars['item']['user_status'] == '2'): ?>禁言
            <?php endif; ?>
      </td>
      <td>
<!--         <?php if ($this->_tpl_vars['item']['user_type'] == '3'): ?>
         <a href="/admin/user/user-purview/uid:<?php echo $this->_tpl_vars['item']['user_id']; ?>
/uname:<?php echo $this->_tpl_vars['item']['user_name']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">店铺权限</a>　 |　 
         <?php endif; ?>-->
         <?php if ($this->_tpl_vars['item']['user_type_shop']): ?>
         <a href="/admin/user/user-rights/uid:<?php echo $this->_tpl_vars['item']['user_id']; ?>
/utype:<?php echo $this->_tpl_vars['item']['user_type_shop']; ?>
/uname:<?php echo $this->_tpl_vars['item']['user_name']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">用户权限</a>　 |　 
         <?php endif; ?>
      	 <a href="/admin/user/user-shop/uid:<?php echo $this->_tpl_vars['item']['user_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">关联店铺</a>　 | 　
         <a href="/admin/user/user-notice/uid:<?php echo $this->_tpl_vars['item']['user_id']; ?>
">个人消息</a>　 | 　
     	 <a href="javascript:jumpToLog('user', <?php echo $this->_tpl_vars['item']['user_id']; ?>
)">记录</a>
      </td>
    </tr>
    <!--<?php endforeach; else: ?>-->
    <tr class="no_data">
      <td colspan="8">暂无用户记录</td>
    </tr>
    <!--<?php endif; unset($_from); ?>-->
  </table>
  <!--<?php if ($this->_tpl_vars['users']): ?>-->
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn1" type="button" value="设置禁言" name="set_gag" id="set_gag" uri="set-gag"  />
      
      <input class="formbtn1" type="button" value="取消禁言" name="set_gag_no" id="set_gag_no" uri="set_gag_no"  />
      
      <input class="formbtn1" type="button" value="加黑名单" name="plus_blacklist" id="plus_blacklist" uri="plus-blacklist"  />
      
      <input class="formbtn1" type="button" value="加白名单" name="plus_whitelist" id="plus_whitelist" uri="plus-whitelist"  />
      
      <input class="formbtn1" type="button" value="封IP" name="set-ip" id="set-ip" uri="set-ip"  />
      
      <input class="formbtn1" type="button" value="解封IP" name="unset-ip" id="unset-ip" uri="unset-ip"  />
    </div>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="clear"></div>
  </div>
  <!--<?php endif; ?>-->
</div>
<script type="text/javascript">

	function jumpToList(type, uid, uname)
	{
		var open_item;
		
		switch(type) {
			case 'usergood': open_item = 'user_good';break;
			case 'usergoodcollect': open_item = 'user_good_collect';break;
			case 'usergoodlike': open_item = 'user_good_like';break;
		}
		window.parent.pickTab('user');
		window.parent.openItem(open_item);
		location.href='/admin/'+ type +'/list/uid:' + uid + '/uname:' + uname;
	}

	//设置禁言
	$('#set_gag').click(function(){
		setGag();
	});
	
	function setGag()
	{
		var items = '';
		if(arguments[0])
		{
			items += arguments[0] + ',';
		}
		else
		{	
			if($('.checkitem:checked').length == 0){
				alert('请选择禁言目标');
				return false;
			}
			$('.checkitem:checked:enabled').each(function(){
				items += this.value + ',';
			});
		}
		items = items.substr(0, (items.length - 1));
		var page = $('#page').val();
		$.dialog({
			title: '设置禁言',
			content: '时间：<br/>'
					 + '<input type="radio" name="gag" value="1" checked="checked" /> 3天<br/>' 
					 + '<input type="radio" name="gag" value="2" /> 一周<br/>'
					 + '<input type="radio" name="gag" value="3" /> 1个月<br/>'
					 + '<input type="radio" name="gag" value="4" /> 半年<br/>',
			okValue: '确定',
			ok: function () {			
				$.post('/admin/user/set-gag', {user_id:items, gag:$('input[name=gag]:checked').val()}, function(data){
					if(data == 'ok'){
						$.dialog({
							title : '结果',
							content : '禁言成功!', 
							ok : function () {location.href = '/admin/user/list/page:' + page;},
							cancel : false
						});
					}
				});
			},
			cancelValue: '取消',
			cancel : true
		});			
	}
	//取消禁言
	$('#set_gag_no').click(function(){
		var items = '';
	
		if($('.checkitem:checked').length == 0){
			alert('请选择取消禁言目标');
			return false;
		}
		$('.checkitem:checked:enabled').each(function(){
			items += this.value + ',';
		});

		items = items.substr(0, (items.length - 1));
		var page = $('#page').val();
		$.dialog({
			title: '提示',
			content : '取消禁言？',
			okValue: '确定',
			ok: function () {			
				$.post('/admin/user/set-gag-no', {user_id:items}, function(data){
					if(data == 'ok'){
						$.dialog({
							title : '结果',
							content : '取消禁言成功!', 
							ok : function () {location.href = '/admin/user/list/page:' + page;},
							cancel : false
						});
					}
				});
			},
			cancelValue: '取消',
			cancel : true
		});			
	});
	
	$('#plus_blacklist').click(function(){
		blacklist(1);
	});
	
	$('#plus_whitelist').click(function(){
		blacklist(2);
	});
	
	function blacklist()
	{
		var items = content = promo = url = '';
		if(arguments[0] == 1){
			content = '加黑名单？';
			url = '/admin/user/plus-blacklist';
			promo = '加黑名单成功';
		}
		else if(arguments[0] == 2)
		{
			content = '取消黑名单？';
			url = '/admin/user/plus-whitelist';
			promo = '取消黑名单成功'
		}
		if($('.checkitem:checked').length == 0){
			alert('请选择目标');
			return false;
		}
		$('.checkitem:checked:enabled').each(function(){
			items += this.value + ',';
		});

		items = items.substr(0, (items.length - 1));
		
		var page = $('#page').val();
		$.dialog({
			title: '提示',
			content : content,
			okValue: '确定',
			ok: function () {			
				$.post(url, {user_id:items}, function(data){
					if(data == 'ok'){
						$.dialog({
							title : '结果',
							content : promo, 
							ok : function () {location.href = '/admin/user/list/page:' + page;},
							cancel : false
						});
					}
				});
			},
			cancelValue: '取消',
			cancel : true
		});					
	}
	//封IP
	$('#set-ip').click(function(){
		ip(1);
	});
	//解封IP
	$('#unset-ip').click(function(){
		ip(2);
	});
	
	function ip()
	{
		var items = content = promo = promoErr = url = '';
		if(arguments[0] == 1){
			content = '封IP？';
			url = '/admin/user/set-ip';
			promo = '封IP成功';
			promoErr = '该用户的IP已封，请重新选择';
		}
		else if(arguments[0] == 2)
		{
			content = '解封IP？';
			url = '/admin/user/unset-ip';
			promo = '解封IP成功';
			promoErr = '该用户的IP没有封禁，请重新选择';
		}
		if($('.checkitem:checked').length == 0){
			alert('请选择目标');
			return false;
		}
		$('.checkitem:checked:enabled').each(function(){
			items += this.value + ',';
		});

		items = items.substr(0, (items.length - 1));
		
		var page = $('#page').val();
		$.dialog({
			title: '提示',
			content : content,
			okValue: '确定',
			ok: function () {			
				$.post(url, {user_id:items}, function(data){
					if(data == 1){
						$.dialog({
							title : '结果',
							content : promo, 
							ok : function () {location.href = '/admin/user/list/page:' + page;},
							cancel : false
						});
					}
					else
					{
						$.dialog({
							title : '结果',
							content : promoErr, 
							ok : function () {location.href = '/admin/user/list/page:' + page;},
							cancel : false
						});						
					}
				});
			},
			cancelValue: '取消',
			cancel : true
		});			
	}
	
	function view(uid) {
		var msg = '';
		$.post('/admin/user/get-shop', {uid:uid}, function(data){
			if(data) {
				var obj = eval('(' + data + ')');
				$.each(obj, function(index, sitem){
					msg += sitem.shop_name + '<br>';
				});
			} else {
				msg = '暂无名下店铺';
			}
			
			$.dialog({
				title : '结果',
				content : msg,
				okValue: '确定',
				ok: true
			});	
		});	
	}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>