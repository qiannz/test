<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' good=$goods.good_name shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<link href="/css/good_wap.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
$(function(){
	var imglist = document.getElementsByTagName('img');
	var relist = []
	var arrsrc = [];
	var Img = new Image();
	var iCur = 0;
	//重置
	for(var i=0;i<imglist.length;i++){
		if(imglist[i].getAttributeNode("data-lazyload")){
			arrsrc.push(imglist[i].getAttribute('data-lazyload'));
			relist.push(imglist[i])
		}
	}
	
	Img.src = arrsrc[0];
	
	Img.onload = function(){
		relist[iCur].src = Img.src;
		relist[iCur].removeAttributeNode(relist[iCur].getAttributeNode("data-lazyload"));
		iCur++;
			Img.src = arrsrc[iCur];
	}
	Img.src = arrsrc[iCur];
});
	
function jumpTo() {
	if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {	
		var loadDateTime = new Date();
		window.setTimeout(function() {
		  var timeOutDateTime = new Date();
		  if (timeOutDateTime - loadDateTime < 5000) {
			window.location = "http://report.mplife.com/html/marketa/";
		  } else {
			window.close();
		  }
		},
		25);
		if (typeof WeixinJSBridge === "undefined") {
			window.location = "ChinaMplife://";
		} else {
			window.location = "http://report.mplife.com/html/marketa/";
		}
		
	  } else {
		window.location = "http://report.mplife.com/html/marketa/";
	  }
	}
</script>
</head>
<body>
	<div class="wrap">
    	<div class="header">{{$goods.good_name}}</div>
        <div class="scetion">
        	<div class="banner">
       	    	<img src="/images/wap/blank.gif" data-lazyload="{{$goods.img.img_url}}"/>
            </div>
            <div class="data-box">
            	<div class="item">
                	<p class="title">{{$goods.good_name}}</p>
                    <p class="time">{{$goods.created|date_format:'%Y.%m.%d'}}</p>
                </div>
                <div class="item">
                	<div class="certificate clearfix">
                    	<div class="c_l">
                        	<span class="price">￥{{$goods.dis_price}}</span>
                            <span class="certificate-item">
                            	{{if $goods.is_auth eq 1}}<img src="/images/wap/certificate.png"><b>Confirm</b>{{/if}}
                            </span>
                        </div>
                        <div class="c_r">
                        	<input type="button" class="star-on"><span>{{$goods.favorite_number}}</span>
                            <input type="button" class="love-on"><span>{{$goods.concerned_number}}</span>
                        </div>
                        {{if $goods.is_auth eq 1}}<div class="certificate-txt"><s></s>此商品信息已通过商家认证，符合商品实际情况。 </div>{{/if}}
                    </div>
                </div>
                <div class="item">
                	<p class="user-info">
                    	<img src="{{$goods.user.Avatar30}}"  width="39" height="39" class="user-pic">
                        <span class="user-name">-{{$goods.user.user_name}}</span>
                    </p>
                </div>
                 <div class="item">
                 	<p class="address"><img src="/images/wap/address-img.png"><span>{{$shopInfo.shop_address}}</span></p>
                 </div>
                 <div class="bottom">
                 <a class="downloading" id="applink1" href="javascript:jumpTo()">下载名品街APP 浏览更多商品</a>
                 </div>
            </div>
        </div>
    </div>   
</body>
</html>
