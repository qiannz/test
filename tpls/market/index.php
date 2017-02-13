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
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/focus.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
	//loading img
	var imglist=document.getElementsByTagName("img"),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute("data-lazyload"));relist.push(imglist[i])}}function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"))};if(ie.test(browser)){ele.src=Img.src}}for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};
	
	getMarket({{$firstCid}});
})
 
function getMarket(cid){
var data = {{$marketbycircle}};
var market = data[cid].market; 

var _html = "";
	$.each(market, function(k, v){
		_html += '<ul style="display: block;">';
		_html += '<li>';
		_html += '<a href="/home/market/show/mid/' + v.market_id + '" class="shop-center-list-pic"><img src="'+ v.logo_img +'"  width="125" height="125"></a>';
		_html += '<a href="/home/market/show/mid/' + v.market_id + '" class= "shop-center-list-txt">' + v.market_name + '</a>';
		_html += '</li>';
		_html += '</ul>';

	});
	$('.shop-center-list').html(_html);	
}

</script>
</head>
<body>
	<div class="w1210">
    	<!--top-->
    	{{include file='top.php'}}
    </div>
      <!--nav-->
     {{include file='nav.php'}}
      <!--------------------------商场-------------------------->
	  <div class="w1210">
       <div class="shop-wrap">
       		<!---导航--->
            <div class="shop-wrap-nav">
            	<div class="shop-wrap-nav-list">
                	<ul>
                    	{{foreach from=$hotCircle key=key item=item}}
                    	<li><a onmouseover="getMarket({{$item.id}})" >{{$item.name}}</a><s class="list-type-on"></s></li>
                        {{/foreach}}
                    </ul>
                </div>
            </div>
            <!--center list-->
            <div class="shop-center-list">
            </div>
            <!-----right list------>
            <div class="shop-right-list">
            	<h2 class="shop-right-list-title">推荐商场</h2>
                {{foreach from=$recommMarket key=key item=item}}
                <div class="shop-right-list-col">
                <a href="{{if $item.come_from_id neq 0}}/home/market/show/mid/{{$item.come_from_id}}{{else}}{{$item.www_url}}{{/if}}" target="_blank" class="shop-right-list-col-pic"><img src="/images/blank.png"  data-lazyload="{{$item.img_url}}" width="186" height="196"></a>
                <a href="{{if $item.come_from_id neq 0}}/home/market/show/mid/{{$item.come_from_id}}{{else}}{{$item.www_url}}{{/if}}" target="_blank" class="shop-right-list-col-txt">{{$item.title}}</a>
                </div>
                {{/foreach}}
            </div>
            {{foreach from=$regionMarket key=key item=item}}
           	<div class="shop-area-col">
            	<h3 class="shop-area-col-title" id="{{$item.region_id}}">{{$item.region_name}}</h3>
                <div class="shop-area-list-pic">
                	<ul>
                    	{{foreach from=$item.market_img key=key_img item=item_img}}
                    	<li><a href="/home/market/show/mid/{{$item_img.market_id}}"><img src="/images/blank.png"  data-lazyload="{{$item_img.logo_img}}" width="125" height="125"></a></li>
         				{{/foreach}}
                    </ul>
                </div>
                <div class="shop-area-list-txt">
                	<ul>
                    	{{foreach from=$item.market_no_img key=key_no_img item=item_mo_img}}
                    	<li>
                        	<a href="/home/market/show/mid/{{$item_mo_img.market_id}}">{{$item_mo_img.market_name}}</a>
                        </li>
                        {{/foreach}}
                    </ul>
                </div>
            </div>
            {{/foreach}}
            
       </div> 
       <!------------------------------>
       
       {{include file='bottom.php'}} 
    
 <!--登陆注册-->
 <div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
{{include file='footer.php'}}