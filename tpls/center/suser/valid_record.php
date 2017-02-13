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
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js?t={{$_CONF.WEB_VERSION}}"></script>
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
                {{if $user.user_type eq 2}}
                    {{if $_CONF._A eq 'valid-record'}}
                    <li class="sel" ><a href="javascript:void(0)" >验证记录</a></li>
                    {{else}}
                    <li><a href="/home/suser/valid-record/sid/{{$sid}}">验证记录</a></li>
                    {{/if}}
                {{/if}}
                {{if $user.user_type eq 2}}
                    {{if $_CONF._A eq 'my-account'}}
                    <li class="sel" ><a href="javascript:void(0)" >账户记录</a></li>
                    {{else}}
                    <li><a href="/home/suser/my-account/sid/{{$sid}}">账户记录</a></li>
                    {{/if}}
                {{/if}}                
                </ul>
            </div>
            <div class="tableBox">
            	<div class="tableSearch">
                <form method="post" action="{{$_CONF.FORM_ACTION}}" id="myVlidRecord">
                	<label>开始日期：</label>
                    <input type="text" name="sdate" id="sdate" class="short" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})" value="{{$request.sdate}}" />
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;结束日期：</label>
                    <input type="text" name="edate" id="edate" class="short" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'sdate\',{d:1})}'})" value="{{$request.edate}}"/>
					<label>&nbsp;&nbsp;&nbsp;&nbsp;券名称：</label>
                    <input type="text" name="title" id="title" value="{{$request.title}}" />
					<label>&nbsp;&nbsp;&nbsp;&nbsp;验证店铺：</label>
                    <select name="vid">
                      <option value="">所有</option>
                      {{foreach from=$validShopArray key=key item=item}}
                	  <option value="{{$item.verify_shop_id}}" {{if $request.vid eq $item.verify_shop_id}}selected="selected"{{/if}}>{{$item.verify_shop_name}}</option>
                      {{/foreach}}
                	</select>
                    <a class="searchBtn" href="javascript:void(0)">查询</a>
                </form>
                </div>             
                <table border="0" cellspacing="0" cellpadding="0" class="dataTable">
                	<thead>
                    	<tr>
                            <td width="200">券名称</td>
                            <td width="70">券类别</td>
                            <td width="177">所属店铺</td>
                            <td width="170">验证店铺</td>
                            <td width="120">验证者</td>
                            <td width="140">验证时间</td>
                            <td width="100">验证码/手机</td>
                    	</tr>
                    </thead>
                    <tbody>
                    {{foreach from=$myValidRecordList.data key=key item=item}}
                    <tr>
                        <td>{{$item.ticket_title}}</td>
                        <td>付费券</td>
                        <td>{{$item.owner_shop_name}}</td>
                        <td>{{$item.verify_shop_name}}</td>
                        <td>{{$item.user_name}}</td>
                        <td>{{$item.verify_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
                        <td>{{$item.verify_code_phone}}</td>
                    </tr>
                    {{/foreach}}
                    </tbody>
                </table>
            </div>
			<p class="btnBox"></p>
            <div class="pageList">{{$myGoodList.pagestr}}</div>
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript">
$(function(){
	$('.searchBtn').on('click', function(){
		$('form#myVlidRecord').submit();
	});
});

function page(url) {
	location.href = url;
}
</script>
</body>
</html>