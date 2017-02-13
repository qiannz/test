<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js"></script>
<script type="text/javascript" src="/js/focus.js"></script>
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
	
//loading img
var imglist=document.getElementsByTagName("img"),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute("data-lazyload"));relist.push(imglist[i])}}function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"))};if(ie.test(browser)){ele.src=Img.src}}for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};})
</script>
<title>超级购-名品导购网</title>
</head>
<body>
	<div class="w1210">
		{{include file='top.php'}}
    </div>
      <!--nav-->
		{{include file='nav.php'}}
        <!--------------------------品牌-------------------------->
      <div class="buy-wrap">
      		 <!--------------------------首屏-------------------------->
      		<div class="first-buy-screen">
            <!--左边-->
            	
            	<div class="left-focus">
                    	<a {{if $recommBrandBid.0.come_from_id}} href="/home/brand/show/bid/{{$recommBrandBid.0.come_from_id}}" {{else}} href="{{$recommBrandBid.0.www_url}}" {{/if}} target="_blank"><img src="/images/blank.png"  data-lazyload="{{$recommBrandBid.0.img_url}}" width="294" height="412"></a>
                    <div class="left-focus-bottom">
                    	<span class="left-focus-brand-logo"><img src="/images/blank.png"  data-lazyload="{{$recommBrandBid.0.brand_logo}}" width="69" height="69"></span>
                    	{{if $recommBrandBid.0.ticket.ticket_id}}
                    	<span class="left-focus-now">¥<font>{{$recommBrandBid.0.ticket.selling_price}}</font></span>
                        <span class="left-focus-old" >抢购 ¥ {{$recommBrandBid.0.ticket.par_value}} 现金券</span>
                        <a class="left-focus-btn buy-coupon" data-tid="{{$recommBrandBid.0.ticket.ticket_id}}">立即抢购</a>
                        {{/if}}
                    </div>
                 
                </div>
               
                
             <!--右边-->
             	{{foreach from=$recommBrandSmall key=key item=item}}
             	<div class="first-right-col">
                	<div class="first-right-col-pic">
                    	<a {{if $item.come_from_id}} href="/home/brand/show/bid/{{$item.come_from_id}}" {{else}} href="{{$item.www_url}}" {{/if}} target="_blank"><img src="/images/blank.png"  data-lazyload="{{$item.img_url}}" width="294" height="200"></a>
                        <span class="first-right-col-txt"><a {{if $item.come_from_id}} href="/home/brand/show/bid/{{$item.come_from_id}}" {{else}} href="{{$item.www_url}}" {{/if}} target="_blank">{{$item.brand_name}}</a></span>
                    </div>
                    {{if $item.ticket.ticket_id}}
                    <div class="first-right-col-info">
                    	<p class="first-right-col-info-now"><font>{{$item.ticket.selling_price}}</font>元</p>
                        <a class="first-right-col-info-btn buy-coupon" data-tid="{{$item.ticket.ticket_id}}">立即抢购</a>
                        <p class="first-right-col-info-old">{{$item.ticket.par_value}}元<img src="/images/xjq.png"  width="60" height="21"></p>
                    </div>
                    {{/if}}
                </div>
                {{/foreach}}

                
            </div>
             <!--------------------------女装-------------------------->
             {{foreach from=$brand key=key item=item}}
             <div class="buy-section">
             	<h2 class="buy-section-title" id="{{$item.store_id}}">{{$item.store_name}}</h2>
                
                <div class="left-focus_01">
                    <div class="left-focus-pic">
                    	<a {{if $item.store_recomm_brand.brand_id}} href="/home/brand/show/bid/{{$item.store_recomm_brand.brand_id}}" target="_blank" {{/if}}><img src="/images/blank.png"  data-lazyload="{{$item.store_recomm_brand.img_url}}" width="294" height="200"></a>
                    </div>
                    <div class="left-focus-bottom">
                    	<span class="left-focus-brand-logo"><a {{if $item.store_recomm_brand.brand_id}} href="/home/brand/show/bid/{{$item.store_recomm_brand.brand_id}}" target="_blank" {{/if}}><img src="/images/blank.png"  data-lazyload="{{$item.store_recomm_brand.brand_icon}}" width="69" height="69"></a></span>
                    	{{if $item.store_recomm_brand.ticket.ticket_id}}
                    	<span class="left-focus-now">¥<font>{{$item.store_recomm_brand.ticket.selling_price}}</font></span>
                        <span class="left-focus-old">抢购 ¥ {{$item.store_recomm_brand.ticket.par_value}} 现金券</span>
                        <a class="left-focus-btn buy-coupon" data-tid="{{$item.store_recomm_brand.ticket.ticket_id}}">立即抢购</a>
                        {{/if}}
                    </div>
                </div>
                
	                {{foreach from=$item.store_brand key=k item=v}}
	                <div class="buy-secion-col">
	                	<div class="buy-secion-col-pic">
	                    	<a href="/home/brand/show/bid/{{$v.brand_id}}"><img src="/images/blank.png"  data-lazyload="{{$v.brand_logo}}" width="168" height="68"></a>
	                    </div>
	                    <div class="buy-secion-col-bottom">
	                    		<a href="/home/brand/show/bid/{{$v.brand_id}}"><span class="buy-secion-col-bottom-l">{{$v.brand_name}}</span></a>
	                            <s {{if $v.is_ticket}}class="ticket-on"{{else}}class="ticket-off"{{/if}}></s>
	                    </div>
	                </div>
	                {{/foreach}}
             </div>
              {{/foreach}}

              <!----->
    <!--关于超级购-->
  {{include file='bottom.php'}}  
    
 <!--登陆注册-->
 <div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>


<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
  $(function(){
  	$(".buy-coupon").live('click', function() {
		var tid = $(this).attr('data-tid');
		var _this = $(this);		
		$.getJSON(site_url + '/home/ticket/apply-ticket-voucher', { tid:tid }, function(json) {
			switch(json.res) {
				case 100:
					window.location = 'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=' + json.extra.guid;
					break;
				case 99:
					$("#popupLogin").show();
					break;
				default:
					alert(json.msg);
					break;					
			}
		});		
	});
  });
</script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
{{include file='footer.php'}}
