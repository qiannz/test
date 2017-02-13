<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/list.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
	  <div class="w1210">
      <!--top-->
      {{include file='top.php'}}
      </div>
      <!--nav-->
      {{include file='nav.php'}}
      <!--列表页-->
      <div class="w1210">
      <div class="listWaper">
     <!--分类-->
        	<div class="groupMenu">
            		<a class="more" id="selectBtn">更多品牌<s></s></a>
            		<p>
                    	<b>分类：</b>
                        <span>
                        <a href="/home/ticket/list/sid/0_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$storeList key=key item=item}}               
                        <a href="/home/ticket/list/sid/{{$key}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/{{$order}}" {{if $key eq $store_id}}class="selbg"{{/if}}>{{$item}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    <p>
                    	<b>品牌：</b>
                        <span id="selectTxt">
                        <a href="/home/ticket/list/sid/{{$store_id}}_0_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$brandList key=key item=item}}               
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$key}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/{{$order}}" {{if $key eq $brand_id}}class="selbg"{{/if}}>{{$item}}</a>
                        {{/foreach}}
                   		</span>
                    </p>
                    <p>
                    	<b>区域：</b>
                        <span>
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_0_{{$circle_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$regionList key=key item=item}}
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$key}}_{{$circle_id}}_{{$shop_id}}/order/{{$order}}" {{if $key eq $region_id}}class="selbg"{{/if}}>{{$item}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    <p>
                    	<b>商圈：</b>
                        <span>
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_0_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$circleList key=key item=item}}               
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$item.id}}_{{$shop_id}}/order/{{$order}}" {{if $item.id eq $circle_id}}class="selbg"{{/if}}>{{$item.name}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    {{if $region_id && $circle_id && $shopList}}
                    <p>
                    	<b>店铺：</b>
                        <span>
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_0/order/{{$order}}">不限</a>
                        {{foreach from=$shopList key=key item=item}}               
                        <a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$item.id}}/order/{{$order}}" {{if $item.id eq $shop_id}}class="selbg"{{/if}}>{{$item.name}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    {{/if}}
            </div>
         
    <!--瀑布流-->
    <div class="allGoods">
    	 
         <div class="picList">
      <div class="listTit">
         	  
         	<span>共有<font>{{$coupon.coupon_num}}</font>张优惠券</span>
            <span class="sort">
            	<a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/1" {{if $order eq 1}}class="sel"{{/if}}>按最新券</a>|
            	<a href="/home/ticket/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/2" {{if $order eq 2}}class="sel"{{/if}}>按最热券</a>
            </span>
         </div> 
      {{if $coupon.copon_info}}        
      <ul>
       {{foreach from=$coupon.copon_info key=key item=item}}       
       <li>
       		<a class="pic" href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank">
            <div class="mpCoupons">
            		<div class="mpCouponsTit"><span class="t1">MP</span><span  class="t2">{{$item.sort_name}}</span></div>
                    <div class="mpCouponsTxt">
                        <p>使用说明：{{$item.ticket_summary}}</p>
                        <p>有效期：{{$item.valid_time}}</p>
                    </div>
            </div>
            </a>
            <div class="txt">
                <p class="l1">
                    <span class="name"><a href="/home/shop/show/sid/{{$item.shop_id}}" target="_blank">{{$item.shop_name}}</a></span>
                    <span class="price">¥<font>{{$item.dis_price}}</font></span>
                </p>
                <p class="l2"><a href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank">{{$item.ticket_title}}</a></p>
            </div>
          </li>
		{{/foreach}}
      </ul>
      {{else}}
      <div class="picList waterfallList" style="height: 44px;">
             <ul id="waterfall">                
             <p class="loading">抱歉，当前没有符合条件的优惠券！</p></ul>
       </div>
      {{/if}}
    </div>
    </div> 
      </div>
      <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
	FnShow('selectBtn','selectTxt');
	showNav('selectTxt');
})
</script>
<!--登陆注册-->
<div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
{{include file='footer.php'}}