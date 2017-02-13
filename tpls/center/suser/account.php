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
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js"></script>
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
            	<form action="{{$_CONF.FORM_ACTION}}" method="post" id="accountForm">
                <input type="hidden" name="sid" id="sid" value="{{$sid}}"  />
                <input type="hidden" name="page" id="page" value="{{$page}}" />
                <label>开始日期：</label><input type="text" value="{{$request.stime}}" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  name="stime" id="stime"/>
                <label>&nbsp;&nbsp;&nbsp;&nbsp;结束日期：</label><input type="text" value="{{$request.etime}}" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})" name="etime" id="etime"/>
                <a href="javascript:searchSubmit()" class="searchBtn">查询</a>
                </form>
            </div>
            <div class="now-balance">
                <b>当前余额：</b><span>￥<font>{{$data.balance}}</font></span>
            </div>
            <table border="0" cellspacing="0" cellpadding="0" class="dataTable">
                <thead>
                    <tr>
                        <td width="90">日期</td>
                        <td width="90">时间</td>
                        <td width="120">支出</td>
                        <td width="100">收入</td>
                        <td width="100">账户余额</td>
                        <td width="177">类型</td>
                        <td width="300">备注</td>
                    </tr>
                </thead>
                <tbody>
                {{foreach from=$data.data key=key item=item}}
                <tr>
                    <td>{{$item.PostTime|date_format:'%Y-%m-%d'}}</td>
                    <td>{{$item.PostTime|date_format:'%H:%M'}}</td>
                    <td>{{if $item.AccountType eq '支出'}}<font>{{$item.Price}}</font>{{/if}}</td>
                    <td>{{if $item.AccountType eq '收入'}}<font>{{$item.Price}}</font>{{/if}}</td>
                    <td>{{$item.Balance}}</td>
                    <td>{{$item.AccountCategory}}</td>
                    <td>{{$item.Remark}}</td>
                </tr>
                {{/foreach}}
                 
                </tbody>
            </table>
        </div>
        <p class="btnBox"></p>
        <div class="pageList">{{$data.pagestr}}</div>
</div>
</div>
<!--外围end-->
<!--底部-->
{{include file='center/footer.php'}}

<script type="text/javascript">
function searchSubmit() {
	var url = '';
	var sid = $('#sid').val();
	var page = $('#page').val();
	if($('#stime').val().length != 0) {
		url += '/stime/' + $('#stime').val();
	}
	if($('#etime').val().length != 0) {
		url += '/etime/' + $('#etime').val();
	}
	window.location.href = '/home/suser/my-account/sid/' + sid + url;
}

function page(url) {
	location.href = url;
}	
</script>
</body>
</html>