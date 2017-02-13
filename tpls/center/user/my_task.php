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
<script type="text/javascript">
var uid = {{$user.user_id}};
$(function(){
	$('.progress-bar').each(function(i){
		var	long = $(this).width(),
			molnum = $(this).find('.molecule').text(), 	//分子
			dennum = $(this).find('.denominator').text(); //分母
		
		$(this).find('.finished-bar').css({
			'width':molnum/dennum*long
		});
		$(this).find('.finished-bar').next().css({
			'left':molnum/dennum*(long-$(this).find('.finished-bar').next().width())
		});
	});
		
});
</script>
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
                    <li class="on"><a href="{{$_CONF.SITE_URL}}/home/user/my-task">街友会</a></li>
	            </ul>
	        </div>
	    </div>       
	</div>
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
    		<div class="tab-title">
            	<ul>
                    <li class="sel"><a href="javascript:void(0)">街友会</a></li>
                </ul>
                <a class="mp-friends-more" onClick="$('#friendsPopup').css({'display':'block'})" >街友会须知</a>
            </div>
            <div class="mp-friends-box">
            	<div class="mybonus-box">
                	<ul>
                    	<li class="bonus-num">我的奖金：<span class="bonus-num-color"> {{$myBonus}} </span>元</li>
                        <li class="mybonus-box-li"><a class="bonus-num-btn" href="/home/user/my-task-info">查看详细</a></li>
                        <li class="mybonus-box-li"><a class="bonus-num-btn" href="/home/user/app-extract">申请提现</a></li>
                    </ul>
                </div>
                  <!--任务列表-->
                <h3 class="task-tit"><span>任务列表</span></h3>
                <div class="task-list">
					{{if $user.user_type eq 3}}
                    <div class="task-box">
                        <div class="task-img">
                            <img src="/images/user/task-pic-01.png" width="131" height="131">
                        </div>
                        <div class="task-con">
                            <ul>
                                <li><span class="task-name">上传奖励<a onclick="$('#morePopup_01').css({'display':'block'})" href="javascript:void(0)"><img src="/images/user/more.png" width="33" height="33"></a></span><span class="task-over-time">活动截止：2015-8-28</span></li>
                                <li><span class="task-con-color">上传<font>每件</font>商品并且通过审核，将获取0.5元奖励</span></li>

                                <li>
                                    <span class="task-prize">奖励：0.5元现金</span>
                                </li>
                            </ul>
                        </div>
                    </div>
					{{/if}}

<!--                    <div class="task-box">-->
<!--                    	<div class="task-img">-->
<!--                        	<img src="/images/user/task-pic-01.png" width="131" height="131">-->
<!--                        </div>-->
<!--                        <div class="task-con">-->
<!--                        	<ul>-->
<!--                            	<li><span class="task-name">天天向上<a onclick="$('#morePopup_01').css({'display':'block'})" href="javascript:void(0)"><img src="/images/user/more.png" width="33" height="33"></a></span><span class="task-over-time">活动截止：{{$task_end_time}}</span></li>-->
<!--                                <li><span class="task-con-color">每天上传<font>20</font>件商品并且通过审核</span></li>-->
<!--                                <li>-->
<!--                                	<span class="progress-name">当前完成</span>-->
<!--                                    <div class="progress-bar">-->
<!--                                    	<span class="not-finished-bar"></span>-->
<!--                                        <span class="finished-bar" ></span>-->
<!--                                        <p class="slide-dis">-->
<!--                                        	<span class="molecule">{{$myTodayUploads}}</span>/<span class="denominator" >20</span>-->
<!--                                        </p>-->
<!--                                    </div>-->
<!--                               </li>-->
<!--                               <li>-->
<!--                               		<span class="task-prize">奖励：5元现金</span>-->
<!--                               </li>-->
<!--                            </ul>-->
<!--                    	</div>-->
<!--                        {{if $myTodayUploads eq 20}}-->
<!--                        <span class="task-confirm">完成</span>-->
<!--                        {{/if}}-->
<!--                </div>-->
<!--                <div class="task-box">-->
<!--                    	<div class="task-img">-->
<!--                        	<img src="/images/user/task-pic-01.png" width="131" height="131">-->
<!--                        </div>-->
<!--                        <div class="task-con">-->
<!--                        	<ul>-->
<!--                            	<li><span class="task-name">十全大补<a onclick="$('#morePopup_02').css({'display':'block'})" href="javascript:void(0)"><img src="/images/user/more.png" width="33" height="33"></a></span><span class="task-over-time">活动截止：{{$task_end_time}}</span></li>-->
<!--                                <li><span class="task-con-color">连续<font>10</font>天完成"天天向上"，中断则重新计算。</span></li>-->
<!--                                <li>-->
<!--                                	<span class="progress-name">当前完成</span>-->
<!--                                    <div class="progress-bar">-->
<!--                                    	<span class="not-finished-bar"></span>-->
<!--                                        <span class="finished-bar" ></span>-->
<!--                                        <p class="slide-dis">-->
<!--                                        	<span class="molecule">{{$myTenDays}}</span>/<span class="denominator" >10</span>-->
<!--                                        </p>-->
<!--                                    </div>-->
<!--                               </li>-->
<!--                               <li>-->
<!--                               		<span class="task-prize">奖励：50元现金</span>-->
<!--                               </li>-->
<!--                            </ul>-->
<!--                    	</div>-->
<!--                </div>-->
<!--                <div class="task-box">-->
<!--                    	<div class="task-img">-->
<!--                        	<img src="/images/user/task-pic-02.png" width="131" height="131">-->
<!--                        </div>-->
<!--                        <div class="task-con">-->
<!--                        	<ul>-->
<!--                            	<li><span class="task-name">畅游韩国<a onclick="$('#morePopup_03').css({'display':'block'})" href="javascript:void(0)"><img src="/images/user/more.png" width="33" height="33"></a></span><span class="task-over-time">活动截止：{{$task_end_time}}</span></li>-->
<!--                                <li><span class="task-con-color">活动截止时贡献值最大（上传验证商品数量）的前<b>10</b>名，可获得韩国单人游奖励。</span></li>-->
<!--                                <li>-->
<!--                                	<span class="progress-name">当前完成</span>-->
<!--                                    <div class="progress-bar">-->
<!--                                    	<span class="not-finished-bar"></span>-->
<!--                                        <span class="finished-bar" ></span>-->
<!--                                        <p class="long-slide-dis">-->
<!--                                        	<span class="molecule">{{$myTotalUploads}}</span>/<span class="denominator" >{{$maxUploads}}</span>（当前最高）-->
<!--                                        </p>-->
<!--                                    </div>-->
<!--                               </li>-->
<!--                               <li>-->
<!--                               		<span class="task-prize">奖励：畅游韩国</span>-->
<!--                               </li>-->
<!--                            </ul>-->
<!--                    	</div>-->
<!--                </div>-->
<!--                <div class="task-box">-->
<!--                    	<div class="task-img">-->
<!--                        	<img src="/images/user/task-pic-03.png" width="131" height="131">-->
<!--                        </div>-->
<!--                        <div class="task-con">-->
<!--                        	<ul>-->
<!--                            	<li><span class="task-name">街友最划算<a onclick="$('#morePopup_04').css({'display':'block'})" href="javascript:void(0)"><img src="/images/user/more.png" width="33" height="33"></a></span><span class="task-over-time">活动截止：{{$task_end_time}}</span></li>-->
<!--                                <li><span class="task-con-color">购买名品街优惠券并完成消费验证，即可获得一张刮刮卡</span></li>-->
<!--                                <li>-->
<!--                                	<a class="{{if $myClientEffectiveNum > 0}}lottery-on{{else}}lottery-off{{/if}}" href="{{if $myClientEffectiveNum > 0}}javascript:clientScratch({{$user.user_id}}){{else}}javascript:void(0){{/if}}" id="clientScratch">立即刮卡</a>-->
<!--                               </li>-->
<!--                               <li>-->
<!--                               		<span class="task-prize">奖励：现金刮刮卡</span>-->
<!--                               </li>-->
<!--                            </ul>-->
<!--                    	</div>-->
<!--                        <span class="{{if $myClientEffectiveNum > 0}}task-num-on{{else}}task-num-off{{/if}}" id="clientEffectiveNum">{{$myClientEffectiveNum}}</span> -->
<!--                </div>-->
<!--                {{if $user.user_type eq 3}}
                <div class="task-box">
                    	<div class="task-img">
                        	<img src="/images/user/task-pic-03.png" width="131" height="131">
                        </div>
                        <div class="task-con">
                        	<ul>
                            	<li><span class="task-name">店员最划算<a onclick="$('#morePopup_05').css({'display':'block'})" href="javascript:void(0)"><img src="/images/user/more.png" width="33" height="33"></a></span><span class="task-over-time">活动截止：{{$task_end_time}}</span></li>
                                <li><span class="task-con-color">帮助用户完成一次优惠券消费验证，即可获得一张刮刮卡</span></li>
                                <li>
                                	<a class="{{if $myClerkEffectiveNum > 0}}lottery-on{{else}}lottery-off{{/if}}" href="{{if $myClerkEffectiveNum > 0}}javascript:clerkScratch({{$user.user_id}}){{else}}javascript:void(0){{/if}}" id="clerkScratch">立即刮卡</a>
                               </li>
                               <li>
                               		<span class="task-prize">奖励：现金刮刮卡</span>
                               </li>
                            </ul>
                    	</div>
                        <span class="{{if $myClerkEffectiveNum > 0}}task-num-on{{else}}task-num-off{{/if}}" id="clerkEffectiveNum">{{$myClerkEffectiveNum}}</span> 
                </div>
                {{/if}}-->
            </div>
          
    </div>
    </div>
   </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<!--弹窗-->
	<div class="popup" id="friendsPopup" style=" display:none">
    	<div class="friends-popup">
        	<a class="popup-close" onClick="$('#friendsPopup').css({'display':'none'})">关闭</a>
            <div class="friends-tips">
            	<h3 class="tips-tit">街友会须知：</h3>
                <p class="tips-txt-tit">1. 上传商品相关的任务，参与计数的商品必须符合以下条件</p>
                <p class="tips-txt">a)商品信息真实有效</p>
                <p class="tips-txt">b)图片、标题、介绍、价格需与实际商品符合并相互匹配</p>
                <p class="tips-txt">c)不可重复上传商品</p>
                <p class="tips-txt">d)不得上传关联店铺内实际不存在或已下线的商品</p>
                <p class="tips-txt">e)商品描述不得包含违反国家法律法规的文字和图片内容</p>
                <p class="tips-txt">f)商品描述不得包含违反其他名品导购网补充限制的内容</p>
                <p class="tips-txt">凡不符合以上要求的商品，将无法审核通过。情节严重的情况，将删除相关商品信息甚至停用相关会员账号</p>
                <p class="tips-txt-tit">2. 不得使用任何第三方外挂程序恶意上传商品获取奖励。一经发现一律停用账号并取消所有已得奖励。</p>
                <a class="popup-btn" onClick="$('#friendsPopup').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
 <!--中奖-->
 <div class="popup" id="winPopup" style="display:none">
    	<div class="win-popup">
        	<a class="popup-close" onClick="$('#winPopup').css({'display':'none'})">关闭</a>
            <div class="win-box">
            	<p class="win-txt">恭喜您！中奖啦！</p>
                <p class="win-con">您刮到了<span>5元</span>现金</p>
                <a class="popup-btn" onClick="$('#winPopup').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
<!--天天向上-->
<div class="popup" id="morePopup_01" style=" display:none">
    	<div class="friends-popup">
        	<a class="popup-close" onClick="$('#morePopup_01').css({'display':'none'})">关闭</a>
            <div class="friends-tips">
            	<h3 class="tips-tit">上传奖励：</h3>
                <p class="tips-txt-tit">任务目标：上传每件商品并且通过审核</p>
                <p class="tips-txt">a)必须为实际存在的常规线下店铺内的商品</p>
                <p class="tips-txt">b)必须为以下品类：服装、鞋帽、箱包、配饰、美妆、家居、婴童</p>
                <p class="tips-txt">c)商品信息真实有效</p>
                <p class="tips-txt">d)图片、标题、介绍、价格需与实际商品符合并相互匹配</p>
                <p class="tips-txt">e)不可重复上传商品</p>
                <p class="tips-txt">f)不得上传关联店铺内实际不存在或已下线的商品</p>
                <p class="tips-txt">g)商品描述不得包含违反国家法律法规的文字和图片内容</p>
                <p class="tips-txt">h)商品描述不得包含违反其他名品导购网补充限制的内容</p>
                <p class="tips-txt">凡不符合以上要求的商品，将无法审核通过。情节严重的情况，将删除相关商品信息甚至停用相关会员账号</p>
                <p class="tips-txt-tit">任务奖励：0.5元现金</p>
                <a class="popup-btn" onClick="$('#morePopup_01').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
<!--十全大补-->
<div class="popup" id="morePopup_02" style=" display:none">
    	<div class="friends-popup">
        	<a class="popup-close" onClick="$('#morePopup_02').css({'display':'none'})">关闭</a>
            <div class="friends-tips">
            	<h3 class="tips-tit">十全大补：</h3>
                <p class="tips-txt-tit">任务目标：连续十天（不可中断）完成"天天向上"。</p>
                <p class="tips-txt-tit">任务说明：连续十天不中断的完成"天天向上"任务，中途间断则重新计算。</p>
                <p class="tips-txt-tit">任务奖励：50元现金。</p>
                <a class="popup-btn" onClick="$('#morePopup_02').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
<!--畅游韩国-->
<div class="popup" id="morePopup_03" style=" display:none">
    	<div class="friends-popup">
        	<a class="popup-close" onClick="$('#morePopup_03').css({'display':'none'})">关闭</a>
            <div class="friends-tips">
            	<h3 class="tips-tit">畅游韩国：</h3>
                <p class="tips-txt-tit">任务目标：在整个活动结束时，贡献度排名前十。</p>
                <p class="tips-txt-tit">任务说明：当活动结束后，名品导购网会统计所有参与上传的网友的上传数量，排名前十的网友将获得韩国旅游的机会。</p>
                <p class="tips-txt-tit">任务奖励：韩国旅游机会。</p>
                <a class="popup-btn" onClick="$('#morePopup_03').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
<!--街友最划算-->
<div class="popup" id="morePopup_04" style=" display:none">
    	<div class="friends-popup">
        	<a class="popup-close" onClick="$('#morePopup_04').css({'display':'none'})">关闭</a>
            <div class="friends-tips">
            	<h3 class="tips-tit">街友最划算：</h3>
                <p class="tips-txt-tit">任务目标：购买名品街优惠券并完成消费验证。</p>
                <p class="tips-txt-tit">任务说明：用户通过名品街或名品街APP购买商户付费优惠券，至商家消费时验证使用成功，即可获得一次刮卡抽奖机会，有几率获得现金返利。</p>
                <p class="tips-txt-tit">任务奖励：现金刮刮卡。</p>
                <a class="popup-btn" onClick="$('#morePopup_04').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
 <!--店员最划算-->
 <div class="popup" id="morePopup_05" style=" display:none">
    	<div class="friends-popup">
        	<a class="popup-close" onClick="$('#morePopup_05').css({'display':'none'})">关闭</a>
            <div class="friends-tips">
            	<h3 class="tips-tit">店员最划算：</h3>
                <p class="tips-txt-tit">任务目标：协助用户完成优惠券验证。</p>
                <p class="tips-txt-tit">任务说明：以店员身份，帮助用户成功验证通过名品街或名品街APP购买的付费优惠券时，可获得一次刮卡抽奖机会，有几率获得现金返利。</p>
                <p class="tips-txt-tit">任务奖励：现金刮刮卡。</p>
                <a class="popup-btn" onClick="$('#morePopup_05').css({'display':'none'})">确定</a>
            </div>    
        </div>
        <div class="shade"></div>
    </div>
<script type="text/javascript">
function shoutPopup() {
	$('#friendsPopup').css({'display':'none'});
}
function shoutWinPopup() {
	$('#winPopup').css({'display':'none'}).html('');
}

function clientScratch() {
	$('#clientScratch').html('刮奖中<img src="/images/user/loading_pic.gif" width="20" height="20">').attr('href', 'javascript:void(0)');
	$.ajax({
		type : "POST",
		url : "/home/user/client-scratch",
		data : "uid=" + uid,
		dataType : "json",
		success : function(json) {			
			if(json.extra.over == 0) {
				$('#clientEffectiveNum').html(json.extra.over).attr('class', 'task-num-off');
				$('#clientScratch').html('立即刮奖').attr('href', 'javascript:void(0)').attr('class', 'lottery-off');
			} else {
				$('#clientEffectiveNum').html(json.extra.over);
				$('#clientScratch').html('立即刮奖').attr('href', 'javascript:clientScratch()');
			}
			$('#winPopup').html(json.extra.html).show();		
		}
	});
}

/*function clerkScratch() {
	$('#clerkScratch').html('刮奖中<img src="/images/user/loading_pic.gif" width="20" height="20">').attr('href', 'javascript:void(0)');
	$.ajax({
		type : "POST",
		url : "/home/user/clerk-scratch",
		data : "uid=" + uid,
		dataType : "json",
		success : function(json) {			
			if(json.extra.over == 0) {
				$('#clerkEffectiveNum').html(json.extra.over).attr('class', 'task-num-off');
				$('#clerkScratch').html('立即刮奖').attr('href', 'javascript:void(0)').attr('class', 'lottery-off');
			} else {
				$('#clerkEffectiveNum').html(json.extra.over);
				$('#clerkScratch').html('立即刮奖').attr('href', 'javascript:clerkScratch()');
			}
			$('#winPopup').html(json.extra.html).show();		
		}
	});
}*/
</script>
</body>
</html>