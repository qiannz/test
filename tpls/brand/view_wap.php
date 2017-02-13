<!DOCTYPE html>
<html>
<head>
<title>{{$brandDetail.brand_name}}</title>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<meta name="description" content="">
<meta name="keywords" content="">

<link href="/css/wap.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
$(function(){
var imglist=document.getElementsByTagName('img'),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute('data-lazyload'));relist.push(imglist[i])}}
function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"));}}
function iEImg(ele,url){var Img=new Image();Img.src=url;Img.onreadystatechange=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"));Img.onreadystatechange=null;}}
for(var i=0;i<arrsrc.length;i++){if(ie.test(browser)){iEImg(relist[i],arrsrc[i])}else{LoadImg(relist[i],arrsrc[i])}}
/*var _w = window.navigator.userAgent;
function loadcss(filename){
	 var fileref = document.createElement('link');
			fileref.setAttribute("rel","stylesheet");
			fileref.setAttribute("type","text/css");
			fileref.setAttribute("href",filename);
			document.getElementsByTagName('head')[0].appendChild(fileref);
}	
	if((/android/i).test(_w)){
		loadcss('css/android.css');
	}else{
		loadcss('css/ipad.css');
	}*/
	
	$('#arrow-btn').toggle(function () {
			$(this).find('img').attr('class','on');
			$('.good-txt').animate({height:$('.good-txt p').height()},100);		
		}, function () {
			$(this).find('img').attr('class','');
		   $('.good-txt').animate({height:'3.5em'},100);		
		});
			
});
</script>
</head>
<body>
    <div class="top-con">
    	<div class="wrap">
			<img data-lazyload='{{$brandDetail.brand_head}}' src="/images/blank.png">					
		</div>
	</div>
	 <div class="wrap">
		<!--头-->
		<div class="brand-con">
			<div class="brand-top">
				<p class="brand-name">{{$brandDetail.brand_name}}</p>
				<div class="brand-img">
					<img src="{{$brandDetail.brand_icon}}">
				</div>
			</div>
			<!--点赞-->
			<div class="good-con">
				<div class="good-tit">{{$brandDetail.favorite_number}}</div>
				<div class="good-txt">
					<p>{{$brandDetail.brand_profile}}</p>
				</div>
				<div class="arrow-box">
					<span id="arrow-btn">更多<img src="/images/arrow.png"></span>
				</div>
			</div>
			<!--品牌活动-->
			<!--
			<div class="brand-act-tit">
				<img src="/images/brand-act-tit.png">
			</div>
			<div class="brand-act">
				<div class="brand-news-list">
					<p><span class="brand-news-l"><s></s>春季服装换季折扣5折起全店铺</span><span class="brand-news-r">7.18-8.30</span></p>
					<p><span class="brand-news-l"><s></s>2014新品9.5折全门店</span><span class="brand-news-r">12.16-12.30</span></p>
					<p><span class="brand-news-l"><s></s>配饰全场3折起仅限虹口旗舰店</span><span class="brand-news-r">12.16-12.30</span></p>
				</div>
				<img data-lazyload='/images/brand-act-list.png' src="/images/blank.png">
			</div>
			-->
			<!--品牌列表-->
			<div class="brand-list">
			{{foreach from=$shopInfo item=item}}
				<div class="brand-row">
					<div class="brand-logo">
						<img src="{{$brandDetail.brand_icon}}">
					</div>
					<div class="brand-info">
						<p class="brand-info-shop">{{$item.shop_name}}</p>
						<p class="brand-info-address">地点：{{$item.shop_address}}</p>
					</div>
				</div>
			{{/foreach}}	
			</div>
		</div>
    </div>
</body>
</html>
