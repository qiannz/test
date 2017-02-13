<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
</head>
<body>
    <!--site-->
    {{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
    {{include file='center/left_2.php'}}
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
			{{include file='center/nav.php'}}
            <div class="tableBox">
            	<div class="tableSearch">
                <form method="post" action="{{$_CONF.FORM_ACTION}}" id="myGood">
                	<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
                	<label>认证状态：</label>
                    <select name="auth" id="auth">
                	  <option value="">所有</option>
                      <option value="1" {{if $request.auth eq 1}}selected="selected"{{/if}}>待认证</option>
                      <option value="2" {{if $request.auth eq 2}}selected="selected"{{/if}}>已认证</option>
                      <option value="3" {{if $request.auth eq 3}}selected="selected"{{/if}}>不通过</option>
                	</select>
                    <input type="text" name="gname" id="gname" value="{{$request.gname}}" />
                    <a class="searchBtn" href="javascript:void(0)">搜索</a>
                </form>
                </div>
                <table border="0" cellspacing="0" cellpadding="0" class="dataTable">
                	<thead>
                    	<tr>
                            <td class="w1"></td>
                            <td class="w2">标题</td>
                            <td class="w3">录入人</td>
                            <td class="w4">录入时间</td>
                            <td class="w5">认证状态</td>
                            <td class="w6">热度</td>
                            <td class="w7">操作</td>
                    	</tr>
                    </thead>
                    <tbody>
                    {{foreach from=$myGoodList.data key=key item=item}}
                    <tr>
                        <td><input type="checkbox" name="gid" id="gid" value="{{$item.good_id}}" {{if $item.is_auth neq 0}}disabled="disabled"{{/if}}/></td>
                        <td><a href="/home/good/show/gid/{{$item.good_id}}" target="_blank">{{$item.good_name}}<font>/{{$item.dis_price}}</font></a></td>
                        <td><font>{{$item.user_name}}</font></td>
                        <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
                        <td>{{if $item.is_auth eq 0}}待认证{{elseif $item.is_auth eq 1}}已认证{{elseif $item.is_auth eq '-1'}}不通过{{/if}}</td>
                        <td>{{$item.concerned_number}} / {{$item.favorite_number}}</td>
                        <td>
                        	{{if $user.user_type eq 2}}
                        		<a class="btn" href="javascript:fixDel({{$item.good_id}})" id="fix_del_{{$item.good_id}}">删除</a>
                            {{elseif $user.user_type eq 3}}
                            	{{if in_array(3,$userPermission)}}
                            	<a class="btn" href="javascript:fixDel({{$item.good_id}})" id="fix_del_{{$item.good_id}}">删除</a>
                                {{/if}}
                            {{/if}}
                            
                            {{if $user.user_type eq 2}}
                            	<a class="btn" href="/home/suser/good-edit/sid/{{$sid}}/gid/{{$item.good_id}}">编辑</a>
                            {{elseif $user.user_type eq 3}}
                            	{{if in_array(2,$userPermission)}}
                                <a class="btn" href="/home/suser/good-edit/sid/{{$sid}}/gid/{{$item.good_id}}">编辑</a>
                                {{/if}}
                            {{/if}}
                            
                            {{if $item.is_auth eq 0}}
                            	{{if $user.user_type eq 2}}
                            	<a class="btn" href="javascript:fixAuth({{$item.good_id}})" id="fix_auth_{{$item.good_id}}">认证</a>
                                {{elseif $user.user_type eq 3}}
                                	{{if in_array(1,$userPermission)}}
                                	<a class="btn" href="javascript:fixAuth({{$item.good_id}})" id="fix_auth_{{$item.good_id}}">认证</a>
                                    {{/if}}	
                                {{/if}}
                            {{/if}}
                        </td>
                    </tr>
                    {{/foreach}}
                    </tbody>
                </table>
            </div>
            <p class="btnBox">
            {{if $user.user_type eq 2}}
            	<a href="javascript:authenticate()" id="authenticate">认证</a>
            {{elseif $user.user_type eq 3}}
            	{{if in_array(1,$userPermission)}}
            	<a href="javascript:authenticate()" id="authenticate">认证</a>
                {{/if}}
            {{/if}}
            </p>
            <div class="pageList">{{$myGoodList.pagestr}}</div>
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript">
$(function(){
	$('.searchBtn').on('click', function(){
		$('form#myGood').submit();
	});
	
	$('#addNewShop').click(function(){
		$('body').append('<div class="Popup" id="addShopPopup"></div>');
		$('#addShopPopup').load('/home/suser/add-shop');
	});	
});

function page(url) {
	location.href = url;
}

function fixDel(gid) {
	$.dialog(
		{
			title:'提示',
			content:'确定要删除当前商品？',
			follow : document.getElementById('fix_del_' + gid),
			okValue : '确定',
			ok:function(){
				$.ajax({
					url : '/home/suser/good-del',
					type : 'get',
					data : {gid:gid},
					dataType : 'json',
					success : function(msg) {
						if(msg.res == 100) {
							$('#fix_del_' + gid).parent().parent().fadeOut('slow');
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

function fixAuth(gid) {
	$.dialog({
		title:'提示',
		follow : document.getElementById('fix_auth_' + gid),
		content : '请认证商品？',
		button: [
			{
				value: '通过',
				callback: function () {
					$.ajax({
						url : '/home/suser/good-auth',
						type : 'get',
						data : {gid:gid},
						dataType : 'json',
						success : function(msg) {
							if(msg.res == 100) {
								$('#fix_auth_' + gid).fadeOut('slow').closest('tr').children('td')
								.eq(0).find('input').attr('disabled', true)
								.end().end()
								.eq(4).html('已认证');
							}
						},
						error : function() {
						}
					});	
				},
				focus: true
			},
			{
				value: '不通过',
				callback: function () {
					$.ajax({
						url : '/home/suser/good-auth-no',
						type : 'get',
						data : {gid:gid},
						dataType : 'json',
						success : function(msg) {
							if(msg.res == 300) {
								$('#fix_auth_' + gid).fadeOut('slow').closest('tr').children('td')
								.eq(0).find('input').attr('disabled', true)
								.end().end()
								.eq(4).html('不通过');
							}
						},
						error : function() {
						}
					});					
				}
			}
		]
	});
}

function authenticate() {
	var items = '';
	
	$('input[name=gid]:checked:enabled').each(function(index, element) {
        items += element.value + ',';
    });
	
	if(items != '') {
		items = items.substr(0, (items.length - 1));
		
		$.dialog({
			title:'提示',
			content:'认证选中的商品？',
			follow : document.getElementById('authenticate'),
			button: [
				{
					value: '通过',
					callback: function () {
						$.ajax({
							url : '/home/suser/goods-auth',
							type : 'get',
							data : {ids:items},
							dataType : 'json',
							success : function(msg) {
								if(msg.res == 100) {
									for( var i=0; i<msg.extra.length;i++) {
										$('#fix_auth_' + msg.extra[i]).fadeOut('slow').closest('tr').children('td')
										.eq(0).find('input').attr('checked', false).attr('disabled', true)
										.end().end()
										.eq(4).html('已认证');
									}
								}
							},
							error : function() {
							}
						});	
					},
					focus: true
				},
				{
					value: '不通过',
					callback: function () {
						$.ajax({
							url : '/home/suser/goods-auth-no',
							type : 'get',
							data : {ids:items},
							dataType : 'json',
							success : function(msg) {
								if(msg.res == 300) {
									for( var i=0; i<msg.extra.length;i++) {
										$('#fix_auth_' + msg.extra[i]).fadeOut('slow').closest('tr').children('td')									
										.eq(0).find('input').attr('checked', false).attr('disabled', true)
										.end().end()
										.eq(4).html('不通过');
									}
								}
							},
							error : function() {
							}
						});					
					}
				}
			]
		});		
	}
	
}
</script>

</body>
</html>