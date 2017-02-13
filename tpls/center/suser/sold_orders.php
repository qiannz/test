<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js"></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
</head>
<body>
    <!--site-->
    {{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
    {{include file='center/left.php'}}
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
    		<div class="tab-title">
                <ul>
                    {{if $_CONF._A eq 'buy-good'}}
                    	<li class="sel" ><a href="javascript:void(0)">团购商品</a></li>
                    {{else}}
                    	<li><a href="/home/suser/buy-good/sid/{{$sid}}">商品管理</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'sold-orders'}}
                        <li class="sel"><a href="javascript:void(0)">售出订单</a></li>
                    {{else}}
                        <li><a href="/home/suser/sold-orders/sid/{{$sid}}">售出订单</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'buy-release'}}
                        <li class="sel"><a href="javascript:void(0)">发起团购</a></li>
                    {{else}}
                        <li><a href="/home/suser/buy-release/sid/{{$sid}}">发起团购</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'veriy'}}
                        <li class="sel"><a href="javascript:void(0)">团购验证</a></li>
                    {{else}}
                        <li><a href="/home/suser/veriy/sid/{{$sid}}">团购验证</a></li>
                    {{/if}}
                </ul>
            </div>            

            <div class="tableBox">
            	<form action="{{$_CONF.FORM_ACTION}}/sid/{{$sid}}" method="post" id="orderSearchForm">
            	<div class="tableSearch">
                <p class="table-col">
                	<label>开始日期：</label><input type="text" name="startDate" value="{{$request.startDate}}" class="short" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd'})"/>
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;结束日期：</label><input type="text" name="overDate" value="{{$request.overDate}}" class="short" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd'})"/>
						<label>&nbsp;&nbsp;&nbsp;&nbsp;订单状态：</label>
                    <select name="orderStatus">
                	  <option value="">所有</option>
                      <option value="1" {{if $request.orderStatus eq 1}}selected="selected"{{/if}}>已取消</option>
                      <option value="2" {{if $request.orderStatus eq 2}}selected="selected"{{/if}}>等待付款</option>
                      <option value="3" {{if $request.orderStatus eq 3}}selected="selected"{{/if}}>完成支付</option>
                      <option value="4" {{if $request.orderStatus eq 4}}selected="selected"{{/if}}>申请退款</option>
                	</select>
						<label>&nbsp;&nbsp;&nbsp;&nbsp;提货方式：</label>
                    <select name="receiveType">
                	  <option value="">所有</option>
                      <option value="1" {{if $request.receiveType eq 1}}selected="selected"{{/if}}>快递送货</option>
                      <option value="2" {{if $request.receiveType eq 2}}selected="selected"{{/if}}>到店自提</option>
                	</select>
                 </p>
                 <p class="table-col">
                 	<label>&nbsp;&nbsp;订单号：</label><input type="text" name="orderNum" value="{{$request.orderNum}}" class="short"/>
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;商品名称：</label><input type="text" name="productName" value="{{$request.productName}}" class="short"/>
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;手机号码：</label><input type="text" name="mobile" value="{{$request.mobile}}" class="table-order-tel"/>
                    <a class="searchBtn">查询</a>
                 </p>
                </div>
                </form>       
                <table border="0" cellspacing="0" cellpadding="0" class="dataTable">
                	<thead>
                    	<tr>
                            <td>订单号</td>
                            <td>商品名称</td>
                            <td>单价</td>
                            <td>数量</td>
                            <td>订单金额</td>
                            <td>成交时间</td>
                            <td>提货方式</td>
                            <td>订单状态</td>
                            <td>配送状态</td>
                            <td>手机号码</td> 
                            <td width="90">操作</td>
                    	</tr>
                    </thead>
                    <tbody>
                    {{foreach from=$soldOrderList.data key=key item=item}}
                    <tr>
                        <td>{{$item.Order.OrderNum}}</td>
                        <td>{{$item.Product.ProductName}}</td>
                        <td>{{$item.OrderDetail.ActivityPrice}}</td>
                        <td>{{$item.OrderDetail.OrderDetailsCount}}</td>
                        <td>{{$item.OrderDetail.OrderDetailsPrice}}</td>
                        <td>{{$item.Order.OrderTime}}</td>
                        <td><a href="javascript:disOrderMsg('{{$item.ReceiptInfo.AreaName}}','{{$item.ReceiptInfo.ProductSkuName}}','{{$item.ReceiptInfo.Name}}', '{{$item.ReceiptInfo.Mobile}}', '{{$item.ReceiptInfo.Address}}')">{{$item.ReceiveText}}</a></td>
                        <td>{{$item.StatusText}}</td>
                        <td>{{$item.ReceiptText}}</td>
                        <td>{{$item.Order.Mobile}}</td>
                        <td>
                        {{if $item.Order.ReceiptStatus eq 1 && $item.Order.OrderStatus eq 2}}
                        	<font class="color-red"><a class="btn" href="javascript:performAction('delivery', '{{$item.Order.OrderNum}}')" id="order_{{$item.Order.OrderNum}}">发货</a></font>
                        {{/if}}
                        </td>
                    </tr>
                    {{foreachelse}}
                    <tr><td colspan="11">暂无订单</td></tr>
                    {{/foreach}}                    
                    </tbody>
                </table>
            </div>
            <p class="btnBox"></p>
            {{if $soldOrderList.pagestr}}
            <div class="pageList">{{$soldOrderList.pagestr}}</div>
            {{/if}}
            
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript">

function page(url) {
	location.href = url;
}

$(function(){
	$(".searchBtn").bind('click', function(){
		$("form#orderSearchForm").submit();
	});
});

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
			   url: "/home/suser/change-order",
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
		cancelValue: '取消',
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
</body>
</html>