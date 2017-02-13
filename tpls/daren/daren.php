<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="format-detection" content="telephone=no"/>
<meta name="share_title" content="{{$share.share_title}}"/>
<meta name="share_desc" content="{{$share.share_desc}}"/>
<meta name="share_img_url" content="{{$share.share_img_url}}"/>
<meta name="MplifeShareWeixinTitle" content="{{$share.share_title}}"/>
<meta name="MplifeShareWeixinDesc" content="{{$share.share_desc}}"/>
<meta name="MplifeShareWeixinImageUrl" content="{{$share.share_img_url}}"/>
<meta name="MplifeShareWeixinUrl" content=""/>
<title>名品导购网</title>
<link href="/css/discount.css" rel="stylesheet" type="text/css">
</head>
<body>

    <div class="viewport">
		<p>
			{{$daren.content}}
		</p>
        <!---hot shop-->
        <div class="hot-shop">
            <div class="shop-list">


            </div>


        </div>

    </div>
	<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
	<script type='text/javascript' src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
	<script>
	$(function(){
		$.ajax({
			url:"{{$_CONF.GLOBAL_CONF.SITE_URL}}/home/daren/good-list",
			dataType:"json",
			data:{sid:"{{$sid}}","page":1},
			cache: false,
			success:function(data){
				for(var i=0; i<data.length; i++ ){
					$(".shop-list").append('<li onclick="javascript:window.location.href=\''+data[i]["detail_url"]+'\'">\
	                        <div class="shop-pic">\
	                        <a class="shop-pic-bg" style="background-image: url('+data[i]["first_img"]["img_url"]+')"></a>\
	                        <img src="/images/shop-blank.png" />\
	                    </div>\
	                    <p class="shop-txt">'+data[i]["ticket_title"]+'</p>\
	                    <div class="shop-bottom">\
	                        <span class="now">'+data[i]["par_value"]+'</span>\
	                        <span class="old">'+data[i]["selling_price"]+'</span>\
	                        <a href="'+data[i]["buy_url"]+'" class="buy-btn"></a>\
	                    </div>\
	                </li>');
				}
			}
		});
	
		wx.config({
			debug: false,
			appId: '{{$weixinKeyArr.Result.AppId}}',
			timestamp:'{{$weixinKeyArr.Result.TimeStamp}}', 
			nonceStr: '{{$weixinKeyArr.Result.NonceStr}}', 
			signature: '{{$weixinKeyArr.Result.Signature}}',
			jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo']
		});
				 
		wx.ready(function() {
			var title = "{{$share.share_title}}";
			var desc = "{{$share.share_desc}}";
			var buyUrl = document.URL;
			var imgUrl = "{{$share.share_img_url}}";
			//注册分享给朋友
			wx.onMenuShareAppMessage({
				title: title, 
				desc: desc, //描述
				link: buyUrl, //分享地址
				imgUrl: imgUrl, //图片地址
				trigger: function (res) {
					// 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
					//alert('用户点击发送给朋友');
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次介绍给您的朋友哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
		
			//注册朋友圈信息
			wx.onMenuShareTimeline({
				title: title,
				link: buyUrl,
				imgUrl: imgUrl,
				trigger: function (res) {
					// 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
					//alert('用户点击分享到朋友圈');
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次分享到您的朋友圈哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
			//分享到QQ
			wx.onMenuShareQQ({
				title: title,
				desc: desc,
				link: buyUrl,
				imgUrl: imgUrl,
				trigger: function (res) {
					//alert('用户点击分享到QQ');
				},
				complete: function (res) {
					// alert(JSON.stringify(res));
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次介绍给您的朋友哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
		
			//分享到微博
			wx.onMenuShareWeibo({
				title: title,
				desc: desc,
				link: buyUrl,
				imgUrl: imgUrl,
				trigger: function (res) {
					//alert('用户点击分享到微博');
				},
				complete: function (res) {
					//alert(JSON.stringify(res));
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次介绍给您的朋友哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
		});
		
		setTimeout(function(){
			var cmslog = document.createElement("script");
			cmslog.src = "http://www.mplife.com/tools/cmslog/log.js";
			document.body.appendChild(cmslog);
		}, 1000);	
	
	});
	</script>
</body>
</html>
