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
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
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
                <table border="0" cellspacing="0" cellpadding="0" class="dataTable">
                	<thead>
                    	<tr>
                        <td width="210">商品名称</td>
                        <td width="60">售价</td>
                        <td width="60">原价</td>
                        <td width="60">数量</td>
                        <td width="140">所属店铺</td>
                        <td width="140">录入时间</td>
                        <td width="67">录入人</td>
                        <td width="60">是否返利</td> 
                        <td width="60">销售状态</td>
                        <td width="60">审核状态</td>
                        <td width="60">操作</td>
                    	</tr>
                    </thead>
                    <tbody>
                    {{foreach from=$buyGoodList.data key=key item=item}}
                    <tr>
           				<td width="210">{{$item.ticket_title}}</td>
                        <td width="60">{{$item.selling_price}}</td>
                        <td width="60">{{$item.par_value|floor}}</td>
                        <td width="60"><font class="color-blue">{{$item.total}}</font></td>
                        <td width="140">{{$item.shop_name}}</td>
                        <td width="140">{{$item.created|date_format:'%y.%m.%d %H:%M'}}</td>
                        <td width="67">{{$item.user_name}}</td>
                        <td width="60">{{if $item.rebates gt 0}}{{$item.rebates}}{{else}}否{{/if}}</td> 
                        <td width="60">
                        {{if $item.apply_status eq '-1'}}已过期
                        {{elseif $item.apply_status eq '0'}}未开始
                        {{elseif $item.apply_status eq '1'}}申领中
                        {{/if}}
                        </td>
                        <td width="60">
                        {{if $item.ticket_status eq '-1'}}不通过
                        {{elseif $item.ticket_status eq '0'}}待审核
                        {{elseif $item.ticket_status eq '1'}}已审核
                        {{/if}}
                        </td>
                        <td width="60">
                        {{if $item.is_online && $item.is_auth eq '1' && $item.ticket_status eq '1'}}
                        <a class="btn" href="javascript:OffShelf({{$item.ticket_id}})" id="off_shelf_{{$item.ticket_id}}">下架</a>
                        {{/if}}
                        </td>
                    </tr>
                    {{/foreach}} 
                    </tbody>
                </table>
            </div>
            <p class="btnBox"></p>
            <div class="pageList">{{$buyGoodList.pagestr}}</div>
            
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript">
function page(url) {
	location.href = url;
}

function OffShelf(tid) {
	$.dialog({
			title:'提示',
			content:'确定要下架当前团购？',
			follow : document.getElementById('off_shelf_' + tid),
			okValue : '确定',
			ok:function(){
				$.ajax({
					url : '/home/suser/ticket-off',
					type : 'get',
					data : {tid:tid},
					dataType : 'json',
					success : function(msg) {
						if(msg.res == 100) {
							$('#off_shelf_' + tid).fadeOut('slow');
						}
					},
					error : function() {
					}
				});	
			},
			cancelValue : '取消'
		}
	);	
}
</script>
</body>
</html>