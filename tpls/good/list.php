<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' store=$store brand=$brand region=$region circle=$circle}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/list.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="{{$_CONF.SITE_URL}}/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
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
                        <a href="/home/good/list/sid/0_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$storeList key=key item=item}}               
                        <a href="/home/good/list/sid/{{$key}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}" {{if $key eq $store_id}}class="selbg"{{/if}}>{{$item}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    <p>
                    	<b>品牌：</b>
                        <span id="selectTxt">
                        <a href="/home/good/list/sid/{{$store_id}}_0_{{$region_id}}_{{$circle_id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$brandList key=key item=item}}               
                        <a href="/home/good/list/sid/{{$store_id}}_{{$key}}_{{$region_id}}_{{$circle_id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}" {{if $key eq $brand_id}}class="selbg"{{/if}}>{{$item}}</a>
                        {{/foreach}}
                   		</span>
                    </p>
                    <p>
                    	<b>区域：</b>
                        <span>
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_0_{{$circle_id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$regionList key=key item=item}}               
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$key}}_{{$circle_id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}" {{if $key eq $region_id}}class="selbg"{{/if}}>{{$item}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    <p>
                    	<b>商圈：</b>
                        <span>
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_0_{{$market_id}}_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$circleList key=key item=item}}               
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$item.id}}_{{$market_id}}_{{$shop_id}}/order/{{$order}}" {{if $item.id eq $circle_id}}class="selbg"{{/if}}>{{$item.name}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    <p>
                    	<b>商场：</b>
                        <span>
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_0_{{$shop_id}}/order/{{$order}}">不限</a>
                        {{foreach from=$marketList key=key item=item}}               
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$item.id}}_{{$shop_id}}/order/{{$order}}" {{if $item.id eq $market_id}}class="selbg"{{/if}}>{{$item.name}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    {{if $region_id && $circle_id && $shopList}}
                    <p>
                    	<b>店铺：</b>
                        <span>
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$market_id}}_0/order/{{$order}}">不限</a>
                        {{foreach from=$shopList key=key item=item}}               
                        <a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$market_id}}_{{$item.id}}/order/{{$order}}" {{if $item.id eq $shop_id}}class="selbg"{{/if}}>{{$item.name}}</a>
                        {{/foreach}}
                        </span>
                    </p>
                    {{/if}}
            </div>
         
    <!--瀑布流-->
    <div class="allGoods">
    	 <div class="listTit">
         	<span>共有<font id="total_num"></font>件商品</span>
            <span class="sort">
            	<a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/1" {{if $order eq 1}}class="sel"{{/if}}>按最新查看</a>|<a href="/home/good/list/sid/{{$store_id}}_{{$brand_id}}_{{$region_id}}_{{$circle_id}}_{{$shop_id}}/order/2" {{if $order eq 2}}class="sel"{{/if}}>按最热查看</a>
            </span>
         </div>
         <div class="clearfix">
             <div class="picList waterfallList{{if $marketDiscountRow}} wrap-left{{/if}}">
                 <ul id="waterfall">                
                 </ul>
             </div>
             {{if $marketDiscountRow}}
             <div class="wrap-right">
                    <div class="wrap-right-box">
                        <h2 class="wrap-right-title">{{$marketDiscountRow.market_name}}</h2>
                        <div class="wrap-right-con">
                        	{{if $marketDiscountRow.discount.URL}}
                            <h3 class="wrap-right-con-tit">最新活动</h3>
                            <div class="new-pic">
                                <a href="{{$marketDiscountRow.discount.URL}}" target="_blank" title="{{$marketDiscountRow.discount.Title}}"><img src="{{$marketDiscountRow.discount.ImageUrl}}" width="160" height="120" alt="{{$marketDiscountRow.discount.Title}}" border="0" /></a>
                                <p class="new-pic-txt"><a href="{{$marketDiscountRow.discount.URL}}" target="_blank" title="{{$marketDiscountRow.discount.Title}}">{{$marketDiscountRow.discount.Title}}</a></p>
                            </div>
                            {{/if}}
                            <h3 class="wrap-right-con-titfont">详细地址：</h3>
                            <ul class="wrap-right-address">
                                <li>{{$marketDiscountRow.market_address}}</li>
                            </ul>
                            <h3 class="wrap-right-con-titfont">交通信息：</h3>
                            <div class="traffic">
                               <p>{{$marketDiscountRow.trafficInfo}}</p>
                               <a class="look-map" href="http://www.mplife.com/zhekou/baidupage/?coordinate_x={{$marketDiscountRow.lng}}_y={{$marketDiscountRow.lat}}" target="_blank">查看地图</a>
                            </div>
                             <h3 class="wrap-right-con-titfont">商场介绍：</h3>
                             <ul class="wrap-right-address">
                                <li>{{$marketDiscountRow.intro}}</li>
                            </ul>
                        </div>
                    </div>
             </div>
             {{/if}}
         </div>
    </div> 
      </div>
      <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<!--弹窗-->
<script type="text/javascript" src="/js/waterfall.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
var seid = {{$store_id}};
var bdid = {{$brand_id}};
var rnid = {{$region_id}};
var ceid = {{$circle_id}};
var mkid = {{$market_id}};
var spid = {{$shop_id}};
var order = {{$order}};
var site_url = '{{$_CONF.SITE_URL}}';

$(function(){	
	FnHover('allBtn','allBox');
	FnShow('selectBtn','selectTxt');
	showNav('selectTxt');
	var col = parseInt($('.waterfallList').width()/220);
	Waterfall({id:'waterfall',listwidth:220,colnum:col,m:22});
});

function Concern(gid, type) {
	$.getJSON(site_url + '/home/index/concern/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function Favorite(gid, type) {
	$.getJSON(site_url + '/home/index/favorite/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function getTypeId(gid, type) { return type + '_' + gid;}

setTimeout(function(){
	var cmslog = document.createElement("script");
	cmslog.src = "http://www.mplife.com/tools/cmslog/log.js";
	document.body.appendChild(cmslog);
}, 1000);

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