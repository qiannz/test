<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
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
                
                {{if $_CONF._A eq 'coupon-list'}}
                    <li class="sel" ><a href="javascript:void(0)" >券管理</a></li>
                {{else}}
                    <li><a href="/home/suser/coupon-list/sid/{{$sid}}">券管理</a></li>
                {{/if}}
                
               {{if $user.user_type eq 2}}     
                    {{if $_CONF._A eq 'add-coupon'}}
                        <li class="sel" ><a href="javascript:void(0)" >发券</a></li>
                    {{else}}
                        <li><a href="/home/suser/add-coupon/sid/{{$sid}}">发券</a></li>
                    {{/if}}
                {{/if}}
                
                {{if $user.user_type eq 2}}
                    {{if $_CONF._A eq 'valid'}}
                    <li class="sel" ><a href="javascript:void(0)" >券验证</a></li>
                    {{else}}
                    <li><a href="/home/suser/valid/sid/{{$sid}}">券验证</a></li>
                    {{/if}}
                {{elseif $user.user_type eq 3}}
                    {{if in_array(4,$userPermission)}}
                        {{if $_CONF._A eq 'valid'}}
                        <li class="sel" ><a href="javascript:void(0)" >券验证</a></li>
                        {{else}}
                        <li><a href="/home/suser/valid/sid/{{$sid}}">券验证</a></li>
                        {{/if}}
                    {{/if}}
                {{/if}}
                         
                </ul>
            </div>
            <div class="tableBox">
                <table border="0" cellspacing="0" cellpadding="0" class="dataTable">
                	<thead>
                    	<tr>
                            <td class="d11">券标题</td>
                            <td>券类型</td>
                            <td>券面值</td>
                            <td>录入人</td>
                            <td>录入时间</td>
                            <td>有效期</td>
                            <td>状态</td>
                            <td>领取人数</td> 
                            <td class="d12">操作</td>
                    	</tr>
                    </thead>
                    <tbody>
                    {{foreach from=$myCouponList.data key=key item=item}}
                    <tr>
                        <td><a href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank">{{$item.ticket_title}}</a></td>
                        <td>{{$item.ticket_type_name}}</td>
                        <td><font>{{$item.par_value}}</font></td>
                        <td><font>{{$item.user_name}}</font></td>
                        <td>{{$item.created|date_format:'%y.%m.%d %H:%M'}}</td>
                        <td>{{$item.valid_stime|date_format:'%m.%d'}}-{{$item.valid_etime|date_format:'%m.%d'}}</td>
                        <td>{{if $item.ticket_status eq '-1'}}审核不通过{{elseif $item.ticket_status eq '0'}}待审核{{elseif $item.ticket_status eq '1'}}已审核{{/if}}</td>
                        <td>{{$item.has_led}}</td>
                        <td>
                        	{{if $item.is_online && $item.is_auth eq '1' && $item.ticket_status eq '1'}}
                        	<a class="btn" href="javascript:OffShelf({{$item.ticket_id}})" id="off_shelf_{{$item.ticket_id}}">下架</a>
                            {{/if}}
                            {{if $item.is_online && $item.ticket_status neq '-1'}}
                            <a class="btn" href="/home/suser/coupon-edit/tid/{{$item.ticket_id}}/sid/{{$sid}}">编辑</a>
                            {{/if}}
                            
                            {{if $item.ticket_status eq '1'}}
                                {{if $user.user_type eq 2}}
                                <a class="btn" href="/home/suser/valid/ctype/{{$item.ticket_type_mark}}/tid/{{$item.ticket_id}}/sid/{{$sid}}" target="_blank">验证</a>
                                {{elseif $user.user_type eq 3 && in_array(4, $userPermission)}}
                                <a class="btn" href="/home/suser/valid/ctype/{{$item.ticket_type_mark}}/tid/{{$item.ticket_id}}/sid/{{$sid}}" target="_blank">验证</a>
                                {{/if}}				
                       		{{/if}}
                            
                            {{if $item.ticket_status eq '-1'}}
                            <a class="btn" href="javascript:showReason('{{$item.reason}}')">查看原因</a>	
                            {{/if}}
                       </td>
                    </tr>
                    {{/foreach}}
                    </tbody>
                </table>
            </div>
            <p class="btnBox"><!--<a href="javascript:OffShelfAll()" id="off_shelf_all">下架</a>--></p>
            <div class="pageList">{{$myCouponList.pagestr}}</div>
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
    
{{include file='center/footer.php'}}
<script type="text/javascript">
function page(url) {
	location.href = url;
}

function showReason() {
	$.dialog.alert(arguments[0]);
}

function OffShelf(tid) {
	$.dialog({
			title:'提示',
			content:'确定要下架当前优惠券？',
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

function OffShelfAll() {
		var items = '';
	
	$('input[name=tid]:checked:enabled').each(function(index, element) {
        items += element.value + ',';
    });
	
	if(items != '') {
		items = items.substr(0, (items.length - 1));
		$.dialog({
				title:'提示',
				content:'确定要下架选中优惠券？',
				follow : document.getElementById('off_shelf_all'),
				okValue : '确定',
				ok:function(){
					$.ajax({
						url : '/home/suser/ticket-offs',
						type : 'get',
						data : {ids:items},
						dataType : 'json',
						success : function(msg) {
							if(msg.res == 100) {
								for( var i=0; i<msg.extra.length;i++) {
									$('#off_shelf_' + msg.extra[i]).fadeOut('slow').closest('tr').children('td')
									.eq(0).find('input').attr('checked', false).attr('disabled', true);
								}
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
}
</script>
</body>
</html>