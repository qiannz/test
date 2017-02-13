<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta charset="utf-8">
<title>优惠券-名品街-名品导购网</title>
<meta name="keywords" content="优惠券,上海优惠,上海优惠券" />
<meta name="description" content="名品街优惠券频道提供上海百货消费最全最给力的优惠券，包括商场优惠券、品牌优惠券、专柜优惠券、小店优惠券等，逛街首先优惠券就在名品导购网名品街优惠券频道。" />
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<link href="/css/wap.css?t={{$_CONF.WEB_VERSION}}2" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
  $(function(){
var imglist=document.getElementsByTagName('img'),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute('data-lazyload'));relist.push(imglist[i])}}
function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"));}}
function iEImg(ele,url){var Img=new Image();Img.src=url;Img.onreadystatechange=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"));Img.onreadystatechange=null;}}
for(var i=0;i<arrsrc.length;i++){if(ie.test(browser)){iEImg(relist[i],arrsrc[i])}else{LoadImg(relist[i],arrsrc[i])}}
})
</script>
</head>
<body>
    <div class="wrap bg-color-1">
    	<div class="header">
			名品券
			<a class="header-r-btn">我的订单</a>
		</div>
		<!--名品劵列表-->
		<div class="tab-title">
				<a href="/home/ticket/wap-list/city/{{$city}}" {{if !$class}}class="sel"{{/if}}>全部</a>
                <a href="/home/ticket/wap-list/city/{{$city}}/class/2" {{if $class eq '2'}}class="sel"{{/if}}>品牌</a>
                <a href="/home/ticket/wap-list/city/{{$city}}/class/1" {{if $class eq '1'}}class="sel"{{/if}}>商场</a>
                <a href="/home/ticket/wap-list/city/{{$city}}/class/3" {{if $class eq '3'}}class="sel"{{/if}}>特卖</a>
		</div>
		<div class="mp-coupon-list">
        	{{if $type && $type eq 'v'}}
                {{foreach from=$coupon.copon_info key=key item=item}}
                {{if $item.coupon_type eq 'voucher'}}
                <div class="mp-coupon-box">
                    <div class="mp-coupon-banner">
                        <a class="mp-coupon-price-on" href="/home/ticket/wap/tid/{{$item.ticket_id}}">
                            <span class="mp-coupon-price-old">¥{{$item.par_value}}</span>
                            <span class="mp-coupon-price-now">{{$item.dis_price}}</span>							
                        </a>                    
                        <a href="/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank"><img data-lazyload='{{if $item.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$item.cover_img}}{{else}}/images/default_wap.png{{/if}}' src="{{if $item.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$item.cover_img}}{{else}}/images/default_wap.png{{/if}}"></a>
                    </div>
                    <div class="mp-coupon-txt">
                        <a href="/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank">{{$item.ticket_title}}</a>
                    </div>
                </div>
                {{/if}}
                {{/foreach}}           
            {{elseif $type && $type eq 'c'}}
                {{foreach from=$coupon.copon_info key=key item=item}}
                {{if $item.coupon_type eq 'coupon'}}
                <div class="mp-coupon-box">
                    <div class="mp-coupon-banner">
                        <a class="mp-coupon-price-draw" href="#">
                            <span class="mp-coupon-price-old">¥{{$item.par_value}}</span>
                            <span class="mp-coupon-price-now">{{$item.dis_price}}</span>							
                        </a>                    
                        <a href="/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank"><img data-lazyload='{{if $item.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$item.cover_img}}{{else}}/images/default_wap.png{{/if}}' src="{{if $item.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$item.cover_img}}{{else}}/images/default_wap.png{{/if}}"></a>
                    </div>
                    <div class="mp-coupon-txt">
                        <a href="/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank">{{$item.ticket_title}}</a>
                    </div>
                </div>
                {{/if}}
                {{/foreach}}            
            {{else}}
                {{foreach from=$coupon.copon_info key=key item=item}}
                <div class="mp-coupon-box">
                    <div class="mp-coupon-banner">
                        {{if $item.coupon_type eq 'voucher'}}
                        <a class="mp-coupon-price-on" href="/home/ticket/wap/tid/{{$item.ticket_id}}">
                                <span class="mp-coupon-price-old">¥{{$item.par_value}}</span>
                                <span class="mp-coupon-price-now">{{$item.dis_price}}</span>							
                        </a>
                        {{elseif $item.coupon_type eq 'coupon'}}
                        <a class="mp-coupon-price-draw" href="#">
                                <span class="mp-coupon-price-old">¥{{$item.par_value}}</span>
                                <span class="mp-coupon-price-now">{{$item.dis_price}}</span>							
                        </a>                    
                        {{/if}}
                        <a href="/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank"><img data-lazyload='{{if $item.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$item.cover_img}}{{else}}/images/default_wap.png{{/if}}' src="{{if $item.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$item.cover_img}}{{else}}/images/default_wap.png{{/if}}"></a>
                    </div>
                    <div class="mp-coupon-txt">
                        <a href="/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank">{{$item.ticket_title}}</a>
                    </div>
                </div>                
                {{/foreach}}
            {{/if}}            
		</div>
    </div>
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
function applyVoucher(tid) {
	var phone = $('#phone').val();
	var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;

	/*	if(!phoneReg.test(phone)) {
	 $.dialog.alert('请输入正确的手机号码');
	 } else {
	 */
	$.getJSON('/home/ticket/apply-ticket-voucher', { tid:tid ,wap:true /*, phone:phone*/}, function(json){
		switch(json.res) {
			case 100:
				$('#voucher').removeClass().addClass('loading').attr('value', '提交中...');
				window.location.href = 'http://superbuy.mplife.com/Wap/Pay/Buy.aspx?i=' + json.extra.guid;
				break;
			case 105:
				$('#surplus').html(json.extra.lave);
				$.dialog.alert(json.msg);
				break;
			default:
				$.dialog.alert(json.msg);
				break;
		}
	});
	/*	}*/
}

$(function(){
	$(".header-r-btn").unbind().bind("click", function () {
		window.location.href = "http://superbuy.mplife.com/Wap/Pay/MyOrder.aspx?t=" + Math.random();
	});

});
</script>
</body>
</html>