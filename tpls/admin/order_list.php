{{include file='admin/header.php'}}
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

<form method="post" action="{{$_CONF.FORM_ACTION}}">
<div class="mrightTop">
  <div class="fontl">

       <div class="left">
           成交时间：
           <input class="queryInput" type="text" name="startDate" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd'})" value="{{$request.startDate}}" placeholder="开始时间" />&nbsp;-&nbsp;
           <input class="queryInput" type="text" name="overDate" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd'})" value="{{$request.overDate}}" placeholder="结束时间" />
           提货方式：<select name="receiveType">
                    <option value="">所有</option>
                    <option value="1" {{if $request.receiveType eq 1}}selected="selected"{{/if}}>快递送货</option>
                    <option value="2" {{if $request.receiveType eq 2}}selected="selected"{{/if}}>到店自提</option>
		           </select>
           订单状态：<select name="orderStatus">
                    <option value="">所有</option>
                    <option value="1" {{if $request.orderStatus eq 1}}selected="selected"{{/if}}>已取消</option>
                    <option value="2" {{if $request.orderStatus eq 2}}selected="selected"{{/if}}>等待付款</option>
                    <option value="3" {{if $request.orderStatus eq 3}}selected="selected"{{/if}}>完成支付</option>
                    <option value="4" {{if $request.orderStatus eq 4}}selected="selected"{{/if}}>申请退款</option>
		           </select>                        
      </div>
  </div>
</div>

<div class="mrightTop">
  <div class="fontl">
       <div class="left">
           订单号：
           <input class="queryInput" type="text" name="orderNum" value="{{$request.orderNum}}" />
           商品名称：
           <input class="queryInput" type="text" name="productName" value="{{$request.productName}}" />
           手机号码：
           <input class="queryInput" type="text" name="mobile" value="{{$request.mobile}}" />
 		   店铺名称：
           <input class="queryInput" type="text" name="shopName" value="{{$request.shopName}}" />
　　 　　　　<input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/order/list">撤销检索</a>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
</form>
    
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
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
    {{foreach from=$data.data item=item}}
    <tr class="tatr2">
      <td>{{$item.Order.OrderNum}}</td>
      <td>{{$item.Product.ProductName}}</td>
      <td>{{$item.OrderDetail.ActivityPrice}}</td>
      <td>{{$item.OrderDetail.OrderDetailsCount}}</td>
      <td>{{$item.OrderDetail.OrderDetailsPrice}}</td>
      <td>{{$item.Order.OrderTime}}</td>
      <td><a href="javascript:disOrderMsg('{{$item.ReceiptInfo.AreaName}}','{{$item.ReceiptInfo.ProductSkuName}}', '{{$item.ReceiptInfo.Name}}', '{{$item.ReceiptInfo.Mobile}}', '{{$item.ReceiptInfo.Address}}')">{{$item.ReceiveText}}</a></td>
      <td>{{$item.StatusText}}</td>
      <td>{{$item.ReceiptText}}</td>
      <td>{{$item.Order.Mobile}}<br/>
      {{if $item.Order.ReceiptStatus eq 2 && $item.Order.OrderStatus eq 2}}
      	<a href="javascript:deliverInfo('{{$item.Order.DeliverInfo.ExpressCompany}}', '{{$item.Order.DeliverInfo.ExpressNumber}}')">快递查看</a>
      {{/if}}
      
      </td>
      <td>      	
        {{if $item.Order.ReceiptStatus eq 1 && $item.Order.OrderStatus eq 2}}
            <a href="javascript:performAction('delivery', '{{$item.Order.OrderNum}}', {{$page}})" id="order_{{$item.Order.OrderNum}}">发货</a>
        {{/if}}
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="11">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
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
{{include file='admin/footer.php'}}