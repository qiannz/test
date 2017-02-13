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
<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css?t={{$_CONF.WEB_VERSION}}"  />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script> 
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
<style type="text/css">
.editable {
    background: url("/css/admin/images/editable.gif") no-repeat scroll right 2px rgba(0, 0, 0, 0);
    padding-right: 14px;
	cursor:pointer;
}
</style>
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
                    {{if $user.user_type eq 2}}
                        {{if $_CONF._A eq 'shop-edit'}}
                            <li class="sel" ><a href="javascript:void(0)" >编辑店铺</a></li>
                        {{else}}
                            <li><a href="/home/suser/shop-edit/sid/{{$sid}}" >编辑店铺</a></li>
                        {{/if}}
                    {{elseif $user.user_type eq 3}}
                        {{if in_array(5,$userPermission)}}
                            {{if $_CONF._A eq 'shop-edit'}}
                                <li class="sel" ><a href="javascript:void(0)" >编辑店铺</a></li>
                            {{else}}
                                <li><a href="/home/suser/shop-edit/sid/{{$sid}}" >编辑店铺</a></li>
                            {{/if}}
                        {{/if}}       
                    {{/if}}
                    
                    {{if $user.user_type eq 2 && $shopRow.is_flag eq 1}}
                        {{if $_CONF._A eq 'shop-decoration'}}
                            <li class="sel" ><a href="javascript:void(0)" >店铺推荐</a></li>
                        {{elseif $_CONF._A eq 'shop-decoration-add'}}
                            <li class="sel" ><a href="javascript:void(0)" >店铺推荐</a></li>
                        {{else}}
                        <li><a href="/home/suser/shop-decoration/sid/{{$sid}}">店铺推荐</a></li>
                        {{/if}}	
                    {{/if}}                    
                </ul>
            </div>            
            <div class="tableBox">
            	<div class="tableSearch">
                	<label>当前模板：</label>
                    <select name="template" id="template" class="tableSearchSelect">
                     	<option value="default">旗舰店默认模板</option>
                	</select>
                    <a class="tableSearchLink" href="/home/suser/shop-decoration-add/sid/{{$sid}}">新增推荐</a>
                    <a class="tableSearchLink" href="/home/shop/show/sid/{{$sid}}/f/1" target="_blank">店铺首页预览</a>
                </div>
                <h3 class="task-tit tableSearchMarginBottom"><span>推荐管理</span></h3>
                <div class="recommendBox">
                <form method="post" action="{{$_CONF.FORM_ACTION}}" id="myShop">
                <input type="hidden" name="sid" id="sid" value="{{$sid}}" />
                <div class="tableSearch">
                    <label>标题：</label>
                     <input type="text" name="title" id="title" value="{{$request.title}}" placeholder="输入关键字搜索">
                     <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;推荐位：</label>
                    <select name="pos_id" id="pos_id">
                    	<option value="">请选择...</option>
                        {{foreach from=$position key=key item=item}}
                    	<option value="{{$item.pos_id}}" {{if $request.pos_id eq $item.pos_id}}selected="selected"{{/if}}>{{$item.pos_name}}</option>
                        {{/foreach}}
                    </select>
                    <a class="searchBtn" href="javascript:void(0)">搜索</a>
                </div>
                </form>
                <table border="0" cellspacing="0" cellpadding="0" class="dataTable recommendTable">
                	<thead>
                    	<tr>
                            <td width="80">排序</td>
                            <td>推荐位</td>
                            <td>标题</td>
                            <td>链接</td>
                            <td>时间</td>
                            <td>操作</td>
                    	</tr>
                    </thead>
                    <tbody>
                    {{foreach from=$myShopDecorationList.data key=key item=item}}
                    <tr>
                        <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.shop_details_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
                        <td>{{$item.pos_name}}</td>
                        <td>{{$item.detail_title}}</td>
                        <td>{{$item.detail_url}}</td>
                        <td>{{$item.created|date_format:"%Y-%m-%d %H:%M:%S"}}</td>
                        <td><a class="btn" href="/home/suser/shop-decoration-add/sid/{{$sid}}/did/{{$item.shop_details_id}}">编辑</a><a class="btn" id="fix_del_{{$item.shop_details_id}}" href="javascript:delBtn({{$item.shop_details_id}})">删除</a></td>
                    </tr>
                    {{/foreach}}
                    </tbody>
                </table>
                <div class="pageList">{{$myShopDecorationList.pagestr}}</div>
           		</div>
            </div>
            
            
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
	{{include file='center/footer.php'}}   
<script type="text/javascript">
$('.searchBtn').on('click', function(){
	$('form#myShop').submit();
});

function page(url) {
	location.href = url;
}

function delBtn(did) {
	$.dialog(
		{
			title:'提示',
			content:'确定要删除当前推荐位？',
			follow : document.getElementById('fix_del_' + did),
			okValue : '确定',
			ok:function(){
				$.ajax({
					url : '/home/suser/shop-decoration-del',
					type : 'get',
					data : {did:did, sid:$('#sid').val()},
					dataType : 'json',
					success : function(msg) {
						if(msg.res == 100) {
							$('#fix_del_' + did).parent().parent().fadeOut("slow");
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

//检查提交内容的必须项
function required(str,s_value,jqobj)
{
	if(str == '')
	{
		jqobj.prev('span').show().text(s_value);
		jqobj.remove();
		alert('此项不能为空');
		return 0;
	}
	return 1;
}
//检查提交内容的类型是否合法
function check_type(type, value, s_value, jqobj)
{
	if(type == 'number')
	{
		if(isNaN(value))
		{
			jqobj.prev('span').show().text(s_value);
			jqobj.remove();
			alert('此项仅能为数字');
			return 0;
		}
	}
	if(type == 'int')
	{
		var regu = /^-{0,1}[0-9]{1,}$/;
		if(!regu.test(value))
		{
			jqobj.prev('span').show().text(s_value);
			jqobj.remove();
			alert('此项仅能为整数');
			return 0;
		}
	}
	if(type == 'pint')
	{
		var regu = /^[0-9]+$/;
		if(!regu.test(value))
		{
			jqobj.prev('span').show().text(s_value);
			jqobj.remove();
			alert('此项仅能为正整数');
			return 0;
		}
	}
	return 1;
}
//检查所填项的最大值
function check_max(str,s_value,max,jqobj)
{
	if(parseInt(str) > parseInt(max))
	{
		jqobj.prev('span').show().text(s_value);
		jqobj.remove();
		alert('此项应小于等于'+max);
		return 0;
	}
	return 1;
}
	
$(function(){
	$('span[ectype="inline_edit"]').click(function(){
		var s_value  = $(this).text();
		var s_name   = $(this).attr('fieldname');
		var s_id     = $(this).attr('fieldid');
		var req      = $(this).attr('required');
		var type     = $(this).attr('datatype');
		var max      = $(this).attr('maxvalue');
		$('<input type="text">').css({border:'1px solid #ccc',width:'50px',height:'20px'})
							.attr({value:s_value,size:5})
							.appendTo($(this).parent())
							.focus()
							.select()
							.keyup(function(event){
							if(event.keyCode == 13)
							{
								var value = $(this).val();
								if(req)
								{
									if(!required(value,s_value,$(this)))
									{
										return;
									}
								}
								if(type)
								{
									if(!check_type(type,value,s_value,$(this)))
									{
										return;
									}
								}
								if(max)
								{
									if(!check_max(value,s_value,max,$(this)))
									{
										return;
									}
								}
								$(this).prev('span').show().text(value);
								$.post('/home/suser/shop-ajax-col',{id:s_id,column:s_name,value:value,'ajax':1},function(data){
									if(data === 'false')
									{
										alert('此名称已存在，请您更换一个');
										$('span[fieldname="'+s_name+'"][fieldid="'+s_id+'"]').text(s_value);
										return;
									}
								});
								$(this).remove();
							}
						})
							.blur(function(){
							var value = $(this).val();
							if(req)
							{
								if(!required(value,s_value,$(this)))
								{
									return;
								}
							}
							if(type)
							{
								if(!check_type(type,value,s_value,$(this)))
								{
									return;
								}
							}
							if(max)
							{
								if(!check_max(value,s_value,max,$(this)))
								{
									return;
								}
							}
							$(this).prev('span').show().text(value);
							$.post('/home/suser/shop-ajax-col',{id:s_id,column:s_name,value:value,'ajax':1},function(data){
								if(data === 'false')
									{
										alert('此名称已存在，请您更换一个');
										$('span[fieldname="'+s_name+'"][fieldid="'+s_id+'"]').text(s_value);
										return;
									}
							});
							$(this).remove();
						});
		$(this).hide();
	});
});
</script>
</body>
</html>