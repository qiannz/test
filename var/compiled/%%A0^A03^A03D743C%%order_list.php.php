<?php /* Smarty version 2.6.27, created on 2016-02-04 10:17:13
         compiled from admin/order_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js"></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav">
    <li><span>订单管理</span></li>
  </ul>
</div>

<form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
<div class="mrightTop">
  <div class="fontl">

       <div class="left">
           成交时间：
           <input class="queryInput" type="text" name="startDate" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd'})" value="<?php echo $this->_tpl_vars['request']['startDate']; ?>
" placeholder="开始时间" />&nbsp;-&nbsp;
           <input class="queryInput" type="text" name="overDate" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd'})" value="<?php echo $this->_tpl_vars['request']['overDate']; ?>
" placeholder="结束时间" />
           提货方式：<select name="receiveType">
                    <option value="">所有</option>
                    <option value="1" <?php if ($this->_tpl_vars['request']['receiveType'] == 1): ?>selected="selected"<?php endif; ?>>快递送货</option>
                    <option value="2" <?php if ($this->_tpl_vars['request']['receiveType'] == 2): ?>selected="selected"<?php endif; ?>>到店自提</option>
		           </select>
           订单状态：<select name="orderStatus">
                    <option value="">所有</option>
                    <option value="1" <?php if ($this->_tpl_vars['request']['orderStatus'] == 1): ?>selected="selected"<?php endif; ?>>已取消</option>
                    <option value="2" <?php if ($this->_tpl_vars['request']['orderStatus'] == 2): ?>selected="selected"<?php endif; ?>>等待付款</option>
                    <option value="3" <?php if ($this->_tpl_vars['request']['orderStatus'] == 3): ?>selected="selected"<?php endif; ?>>完成支付</option>
                    <option value="4" <?php if ($this->_tpl_vars['request']['orderStatus'] == 4): ?>selected="selected"<?php endif; ?>>申请退款</option>
		           </select>                        
      </div>
  </div>
</div>

<div class="mrightTop">
  <div class="fontl">
       <div class="left">
           订单号：
           <input class="queryInput" type="text" name="orderNum" value="<?php echo $this->_tpl_vars['request']['orderNum']; ?>
" />
           商品名称：
           <input class="queryInput" type="text" name="productName" value="<?php echo $this->_tpl_vars['request']['productName']; ?>
" />
           手机号码：
           <input class="queryInput" type="text" name="mobile" value="<?php echo $this->_tpl_vars['request']['mobile']; ?>
" />
 		   店铺名称：
           <input class="queryInput" type="text" name="shopName" value="<?php echo $this->_tpl_vars['request']['shopName']; ?>
" />
　　 　　　　<input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/order/list">撤销检索</a>
  </div>
  <div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
</form>
    
<div class="tdare">
  <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>订单号</td>      
      <td width="200">商品名称</td>
      <td>单价</td>
      <td>数量</td>
      <td>订单金额</td>
      <td>成交时间</td>
      <td>提货方式</td>
      <td>订单状态</td>
      <td>配送状态</td>
      <td>手机号码</td>
      <td>操作</td>
    </tr>
    <?php $_from = $this->_tpl_vars['data']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_tpl_vars['item']['Order']['OrderNum']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['Product']['ProductName']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['OrderDetail']['ActivityPrice']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['OrderDetail']['OrderDetailsCount']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['OrderDetail']['OrderDetailsPrice']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['Order']['OrderTime']; ?>
</td>
      <td><a href="javascript:disOrderMsg('<?php echo $this->_tpl_vars['item']['ReceiptInfo']['AreaName']; ?>
','<?php echo $this->_tpl_vars['item']['ReceiptInfo']['ProductSkuName']; ?>
', '<?php echo $this->_tpl_vars['item']['ReceiptInfo']['Name']; ?>
', '<?php echo $this->_tpl_vars['item']['ReceiptInfo']['Mobile']; ?>
', '<?php echo $this->_tpl_vars['item']['ReceiptInfo']['Address']; ?>
')"><?php echo $this->_tpl_vars['item']['ReceiveText']; ?>
</a></td>
      <td><?php echo $this->_tpl_vars['item']['StatusText']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['ReceiptText']; ?>
</td>
      <td><?php echo $this->_tpl_vars['item']['Order']['Mobile']; ?>
<br/>
      <?php if ($this->_tpl_vars['item']['Order']['ReceiptStatus'] == 2 && $this->_tpl_vars['item']['Order']['OrderStatus'] == 2): ?>
      	<a href="javascript:deliverInfo('<?php echo $this->_tpl_vars['item']['Order']['DeliverInfo']['ExpressCompany']; ?>
', '<?php echo $this->_tpl_vars['item']['Order']['DeliverInfo']['ExpressNumber']; ?>
')">快递查看</a>
      <?php endif; ?>
      
      </td>
      <td>      	
        <?php if ($this->_tpl_vars['item']['Order']['ReceiptStatus'] == 1 && $this->_tpl_vars['item']['Order']['OrderStatus'] == 2): ?>
            <a href="javascript:performAction('delivery', '<?php echo $this->_tpl_vars['item']['Order']['OrderNum']; ?>
', <?php echo $this->_tpl_vars['page']; ?>
)" id="order_<?php echo $this->_tpl_vars['item']['Order']['OrderNum']; ?>
">发货</a>
        <?php endif; ?>
      </td>  
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="11">暂无记录</td>
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
function deliverInfo(company, number) {
	$.dialog.alert('快递公司： ' + company + '<br/>' 
				 + '快递单号：' + number );
}

function performAction(method, orderNo, page) {
	$.dialog({
		title: '提示',
		content: '快递公司：<br/>'
				 + '<input type="text" name="express_company" id="express_company" /><br/><br/>' 
				 + '快递单号：<br/>'
				 + '<input type="text" name="express_number" id="express_number" /><br/><br/>',
		okValue: '确定发货',
		follow : document.getElementById('order_' + orderNo),
		ok: function () {
			$.ajax({
			   type: "POST",
			   url: "/admin/order/change-order",
			   data: {method:method, orderNo:orderNo, expressCompany:$("#express_company").val(), expressNumber:$("#express_number").val(), page:page},
			   dataType : "json",
			   success: function(obj){
				 if(obj.res == 100) {
					$('#order_' + orderNo).closest("tr").find("td").eq(8).html('已发货')
					$('#order_' + orderNo).parent().remove();
				 } else {
				 	$.dialog.alert(obj.extra);
				 }
			   }
			});
		},
		cancelValue: '取消发货',
		cancel : true
	});		
}

function disOrderMsg(area, pname, name,mobile,address) {
	var _html = 
		'商品名称：' + pname + "<br /><br />"
		+ '姓名：' + name + "<br /><br />"
		+ '手机号码：' + mobile + "<br /><br />";
	
	if(!!area) {
		_html += '区域地址：' + area + "<br /><br />";
	}
	
	_html += '发货地址：' + address;
	
	$.dialog.alert(
		_html
	);
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>