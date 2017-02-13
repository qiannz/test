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
<link  rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}" />
<style type="text/css">
form label.right {
    background: url("/images/right.gif") no-repeat scroll 0 0 rgba(0, 0, 0, 0);
    color: #008000;
    float: none;
    font-style: italic;
    margin-left: 5px;
    padding-left: 12px;
}

</style>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
$(function(){
	var ie = jQuery.support.htmlSerialize;
	if(ie) {
		$('#money')[0].oninput = changeNum;
        $('#cardMoney')[0].oninput = changeNumForCard;
	} else {
		$('#money')[0].onpropertychange  = changeNum;
        $('#cardMoney')[0].oninput = changeNumForCard;
	}
	
	function changeNum() {
		var money =Number($('#money').val());
		var myMoney = Number($('.bonus-num-color').html());
		if($('#money').val() <= 0) {
			$.dialog.alert('请输入正确的金额');
            $('#money').val('');
            $('.mymoney em').html(myMoney);
			return false;
		}
        if(!/^[0-9]+$/.test(money)){
            $.dialog.alert('提现只能提取整数金额');
            $('#money').val('');
            $('.mymoney em').html(myMoney);
            return false;
        }
		if(money > myMoney) {
			$('#money').val(myMoney);
		}
		$('.mymoney em').html(myMoney - money > 0 ? myMoney - money : 0);
	}

    function changeNumForCard() {
        var money =Number($('#cardMoney').val());
        var myMoney = Number($('.bonus-num-color').html());
        if($('#cardMoney').val() <=  0) {
            $.dialog.alert('请输入正确的金额');
            $('#cardMoney').val('');
            $('.mymoney1 em').html(myMoney);
            return false;
        }
        if(!/^[0-9]+$/.test(money)){
            $.dialog.alert('提现只能提取整数金额');
            $('#cardMoney').val('');
            $('.mymoney1 em').html(myMoney);
            return false;
        }
        if(money > myMoney) {
            $('#cardMoney').val(myMoney);
        }
        $('.mymoney1 em').html(myMoney - money > 0 ? myMoney - money : 0);
    }
	
	$('form#fillMoney').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        submitHandler: function(form) {
            $(form).find(".fill-infor-btn").attr("disabled", true).attr("value","提交...");
            form.submit();
        },
        success:function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup:false,
        rules:{
            money : {
                required : true
            },
            realName : {
                required : true
            },
            paypal : {
                required : true
            },
            repaypal : {
                required : true,
                equalTo: "#paypal"
            }
        },
        messages : {
            money : {
                required : '提取金额不能为空 '
            },
            realName : {
                required : '请输入姓名 '
            },
            paypal : {
                required : '请输入支付宝帐号'
            },
            repaypal : {
                required : '重复支付宝帐号',
                equalTo: '两次输入不一致'
            }
        }
    });


    $('form#fillCard').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        submitHandler: function(form) {
            $(form).find(".fill-infor-btn").attr("disabled", true).attr("value","提交...");
            form.submit();
        },
        success:function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup:false,
        rules:{
            cardMoney : {
                required : true
            },
            cardrealName : {
                required : true
            },
            bankName:{
                required:true
            },
            cardNum : {
                required : true
            },
            repayCardNum : {
                required : true,
                equalTo: "#cardNum"
            }
        },
        messages : {
            cardMoney : {
                required : '提取金额不能为空 '
            },
            cardrealName : {
                required : '请输入姓名 '
            },
            bankName:{
                required:'请输入开户银行'
            },
            cardNum : {
                required : '请输入银行卡卡号'
            },
            repayCardNum : {
                required : '重复银行卡卡号',
                equalTo: '两次输入不一致'
            }
        }
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
                    <li class="sel"><a href="/home/user/my-task">街友会</a></li>
                </ul>
                <span><a class="mp-friends-more" href="javascript:history.back(-1)">返回</a></span>
            </div>
            <div class="mp-friends-box">
            	<div class="mybonus-box">
                	<ul>
                    	<li class="bonus-num">我的奖金： <span class="bonus-num-color">{{$myBonus}}</span> 元</li>
                    </ul>
                </div>
                  <!--任务列表-->
                <h3 class="task-tit"><span>提现</span></h3>
                <div class="draw-rule"><p style="margin-bottom:10px;">提现的帐户名必须和登记姓名一致，不支持支付宝付款。</p>在您提交提现申请后，我们大概需要一周左右完成审核，审核通过后钱款将直接汇入您指定的银行账号。</div>
<!--                <div class="tableSearch">
                    <a class="tableSearchLink">支付宝</a>
                    <a class="tableSearchLink">银行卡</a>
                </div>-->

                <div class="friends-draw" style="display: none">
                    <form action="" method="post" id="fillMoney">
                    <input type="hidden" name="formhash" value="{{$formhash}}" />
                    <div class="fill-infor">
                        	<p>
                            <label class="fill-infor-label">提现金额</label>
                            <input type="text" class="fill-infor-text-center" value="" name="money" id="money" />
                            <label class="field_notice"></label>
                            </p>
                            <p class="mymoney">提现后剩余金额为： ￥ <em>0</em></p>
                            <p>
                            <label class="fill-infor-label">真实姓名</label>
                            <input type="text" class="fill-infor-text" name="realName" id="realName" />
                            <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p>
                            <label class="fill-infor-label">支付宝账号</label>
                            <input type="text" class="fill-infor-text" name="paypal" id="paypal" />
                            <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p>
                            <label class="fill-infor-label">请再输入一次</label>
                            <input type="text" class="fill-infor-text" name="repaypal" id="repaypal" />
                            <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p class="fill-infor-center">
                                <input type="hidden" name="type" value="alipay"/>
                            	<input type="submit" class="fill-infor-btn" value="提交申请" />
                                <a class="fill-infor-btn" href="/home/user/my-task-extract">查看提现历史</a>
                            </p>
                    </div>
                    </form>
                </div>

                <div class="friends-draw">
                    <form action="" method="post" id="fillCard">
                        <input type="hidden" name="formhash" value="{{$formhash}}" />
                        <div class="fill-infor">
                            <p>
                                <label class="fill-infor-label">提现金额</label>
                                <input type="text" class="fill-infor-text-center" value="" name="cardMoney" id="cardMoney" />
                                <label class="field_notice"></label>
                            </p>
                            <p class="mymoney1">提现后剩余金额为： ￥ <em>0</em></p>
                            <p>
                                <label class="fill-infor-label">真实姓名</label>
                                <input type="text" class="fill-infor-text" name="cardRealName" id="cardrealName" />
                                <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p>
                                <label class="fill-infor-label">开户银行</label>
                                <input type="text" class="fill-infor-text" name="bankName" id="bankName" />
                                <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p>
                                <label class="fill-infor-label">银行卡卡号</label>
                                <input type="text" class="fill-infor-text" name="cardNum" id="cardNum" />
                                <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p>
                                <label class="fill-infor-label">请再输入一次</label>
                                <input type="text" class="fill-infor-text" name="repayCardNum" id="repayCardNum" />
                                <label class="field_notice"></label>
                            </p>
                            <p class="fill-infor-error"></p>
                            <p class="fill-infor-center">
                                <input type="hidden" name="type" value="bank"/>
                                <input type="submit" class="fill-infor-btn" value="提交申请" />
                                <a class="fill-infor-btn" href="/home/user/my-task-extract">查看提现历史</a>
                            </p>
                        </div>
                    </form>
                </div>
    </div>
    </div>
   </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
</body>
<script type="text/javascript">
    //$('.friends-draw').eq(0).css({'display':'block'});
    $('.tableSearchLink').eq(0).attr('class','tableSearchLink sel');
    $('.tableSearchLink').click(function(){
		$('.tableSearchLink').attr('class','tableSearchLink').eq($(this).index()).attr('class','tableSearchLink sel');
		$('.friends-draw').css({'display':'none'}).eq($(this).index()).css({'display':'block'});
    })
	$(".mymoney em, .mymoney1 em").html(Number($(".bonus-num-color").html()));
</script>
</html>