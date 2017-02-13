<?php /* Smarty version 2.6.27, created on 2016-02-03 10:19:59
         compiled from admin/wbmember_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/wbmember_list.php', 70, false),)), $this); ?>
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
  <p>微商管理</p>
  <ul class="subnav">
    <li><span>会员管理</span></li>
<!--     <li><a class="btn1" href="/admin/wbmember/add">新建会员</a></li> -->
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
       <div class="left">
                                   用户姓名：
            <input class="queryInput" type="text" name="realname" value="<?php echo $this->_tpl_vars['request']['realname']; ?>
" />
                                 手机号码：
            <input class="queryInput" type="text" name="mobile" value="<?php echo $this->_tpl_vars['request']['mobile']; ?>
" />
                                  用户类型：
            <select name="ut" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['ut'] == 1): ?>selected="selected"<?php endif; ?>>微商</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['ut'] == 2): ?>selected="selected"<?php endif; ?>>代购</option>
                <option value="3" <?php if ($this->_tpl_vars['request']['ut'] == 3): ?>selected="selected"<?php endif; ?>>切货</option>
                <option value="4" <?php if ($this->_tpl_vars['request']['ut'] == 4): ?>selected="selected"<?php endif; ?>>游客VIP</option>
            </select>
                                审核状态：
			<select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" <?php if ($this->_tpl_vars['request']['st'] == 1): ?>selected="selected"<?php endif; ?>>未审核</option>
                <option value="2" <?php if ($this->_tpl_vars['request']['st'] == 2): ?>selected="selected"<?php endif; ?>>审核通过</option>
                <option value="3" <?php if ($this->_tpl_vars['request']['st'] == 3): ?>selected="selected"<?php endif; ?>>审核拒绝</option>
            </select>
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/wbmember/list">撤销检索</a>
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
      <td>ID</td>
      <td width="15%">手机号</td>
      <td>姓名</td>
      <td>申请类型</td>
	  <td>申请说明</td>
	  <td>申请时间</td>
	  <td>审核状态</td>
      <td width="250">操作</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['user_id']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['mobile']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['realname']; ?>
</td>
      <td>
      		<?php if ($this->_tpl_vars['item']['user_type'] == '1'): ?>微商
      		<?php elseif ($this->_tpl_vars['item']['user_type'] == '2'): ?>代购
      		<?php elseif ($this->_tpl_vars['item']['user_type'] == '3'): ?>切货
      		<?php elseif ($this->_tpl_vars['item']['user_type'] == '4'): ?>游客VIP
      		<?php endif; ?>
      </td>
      <td><?php echo $this->_tpl_vars['item']['apply_reason']; ?>
</td>
      <td><?php if ($this->_tpl_vars['item']['created']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?></td>
      <td>
      		<?php if ($this->_tpl_vars['item']['user_status'] == '-1'): ?>审核拒绝
      		<?php elseif ($this->_tpl_vars['item']['user_status'] == '0'): ?>未审核
			<?php elseif ($this->_tpl_vars['item']['user_status'] == '1'): ?>审核通过
            <?php endif; ?>
      </td>
      <td width="250">
      		<a href="/admin/wbmember/edit/uid:<?php echo $this->_tpl_vars['item']['user_id']; ?>
/page:<?php echo $this->_tpl_vars['page']; ?>
">编辑</a> |
            <?php if ($this->_tpl_vars['item']['user_status'] == 0): ?>
            <a href="javascript:audit(<?php echo $this->_tpl_vars['item']['user_id']; ?>
,<?php echo $this->_tpl_vars['item']['user_type']; ?>
)">审核</a> |
            <?php endif; ?>
      		<a href="javascript:jumpToLog('wbmember', '<?php echo $this->_tpl_vars['item']['user_id']; ?>
')">记录</a>
      </td>
    </tr>
   <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="9">暂无数据</td>
    </tr>
    <?php endif; unset($_from); ?>
  </table>
  <div id="dataFuncs">
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function audit( user_id , user_type ){
	var content = '用户类型：<select id="user_type">'
					+'<option '+(user_type==1?'checked="checked"':'')+' value="1">微商</option>'
					+'<option '+(user_type==2?'checked="checked"':'')+' value="2">代购</option>'
					+'<option '+(user_type==3?'checked="checked"':'')+' value="3">切货</option>'
					+'<option '+(user_type==4?'checked="checked"':'')+' value="4">游客VIP</option>'
					+'</select>';
	
	$.dialog({
		title:'警告',
		content: content,
		button : [
					{
						value : '审核通过',
						callback:function(){
							$.ajax({
				                type:'POST',
				                url:'/admin/wbmember/audit',
				                data:{uid:user_id,user_type:$("#user_type").val(),user_status:1},
				                dataType:'json',
				                success:function(data){
				                    if( 1== data ){
					                    alert("审核通过成功");
					                    window.location.reload();
					                }else{
										alert("审核通过失败");
							        }
				                }
				            });				
						}
					},
					{
						value : '审核拒绝',
						callback:function(){
							$.ajax({
				                type:'POST',
				                url:'/admin/wbmember/audit',
				                data:{uid:user_id,user_type:$("#user_type").val(),user_status:-1},
				                dataType:'json',
				                success:function(data){
				                    if( 1== data ){
					                    alert("审核拒绝成功");
					                    window.location.reload();
					                }else{
										alert("审核拒绝失败");
							        }
				                }
				            });
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