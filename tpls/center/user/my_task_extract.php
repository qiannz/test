<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>街友会-我的名品街-名品导购网</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
    <!--site-->
    {{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
    <div class="persub">
	    <!--个人信息-->
	    <div class="perinfo">
	        <b class="title"></b>
	        <div class="perbox">
	            <div class="info"><div class="imgbox">
	            <a href="http://passport.mplife.com/settings/perAvatar.aspx" title="修改头像" target="_blank">
	            <img src="{{$userSync.Avatar50}}" alt="" width="60" height="60"></a></div>
	            <div class="text">
	            <p><a href="http://passport.mplife.com/settings/perManage.aspx" title="资料修改" target="_blank">{{$user.user_name}}</a></p>
	            <p> {{$userSync.GroupTitle}}</p>
	            <p>
	            <a href="http://passport.mplife.com/settings/perManage.aspx" title="修改性别" target="_blank"><img src="{{if $userSync.UserSex eq 1}}/images/user/male.png{{else}}/images/user/female.png{{/if}}" alt=""></a>
	            <a href="http://passport.mplife.com/settings/perManage.aspx" title="{{$userSync.CityTitle}}" target="_blank">{{$userSync.CityTitle}}</a>
	            </p>
	            </div>
	            </div>
	            <div class="hot-line">会员热线：021-52519666</div><div class="pertask"></div>
	        </div>
	    </div>
	    <!--个人信息end-->
	    <!--后台管理列表-->
	    <div class="back-sidebar">
	        <div class="sub-nav">
	            <ul>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=1">我上传的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=2">我收藏的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=3">我喜欢的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/bclist.aspx">我的商圈</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oMyOrder/MyMpTicket.aspx">我的优惠卷</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oMyOrder/MyOrder.aspx">我的订单</a></li>
                    <li><a href="{{$_CONF.SITE_URL}}/home/user/my-task">街友会</a></li>
	            </ul>
	        </div>
	    </div>       
	</div>
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
    		<div class="tab-title">
            	<ul>
                    <li class="not-style"><a href="/home/user/my-task">街友会</a></li>
                    <li class="sel"><a href="javascript:void(0)">提现历史</a></li>
                </ul>
                <a class="mp-friends-more" href="javascript:history.back(-1)">返回</a>
            </div>
            <div class="mp-friends-box">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mp-friends-table">
                	<thead>
                    	<tr>
                            <td width="20%">时间</td>
                            <td width="20%">提现金额</td>
                            <td width="25%">状态</td>
                            <td width="35%"></td>
                        </tr>
                    </thead>
                    <tbody>
                    	{{foreach from=$taskMoney.data key=key item=item}}
                        <tr>
                            <td>{{$item.app_time|date_format:'%Y-%m-%d'}}</td>
                            <td class="table-bold">获得<span>{{$item.amount}}</span>元</td>
                            <td>
                            {{if $item.operat_status eq 1}}申请中
                            {{elseif $item.operat_status eq 2}}
                            	{{if $item.operat_result eq -1}}提现失败
                                {{elseif $item.operat_result eq 1}}提现成功
                                {{/if}}
                            {{/if}}
                            </td>
                            <td><span class="color-blue">{{if $item.operat_status eq 2}}{{$item.reason_of_failure}}{{/if}}</span></td>
                        </tr>
                        {{/foreach}}
                    </tbody>
                </table>
                {{if $taskMoney.pagestr}}
                <div class="pageList">{{$taskMoney.pagestr}}</div>
                {{/if}}
  			</div>
    </div>
   </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript">
	function page(url) {
		window.location.href = url;
	}
</script>
</body>
</html>