<!DOCTYPE html>
<html>
<head>
<title>{{$share.title}}</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="format-detection" content="telephone=no"/>
<meta name="MplifeShareWeixinTitle" content="{{$share.title}}"/>
<meta name="MplifeShareWeixinDesc" content="{{$share.desc}}"/>
<meta name="MplifeShareWeixinImageUrl" content="{{$share.img_url}}"/>
<meta name="MplifeShareWeixinUrl" content="{{$share.www_url}}"/>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/active/game.js?version={{$version}}"></script>
<link href="/css/active/scratch/common.css?version={{$version}}" rel="stylesheet" type="text/css">
<script type="text/javascript">
    $(function(){
        var o = gameCanvas(['/images/active/scratch/canvas-bg.png']);
        {{if $mobileRow.is_award eq 1}}
        o.init(['¥100','恭喜您刮中¥100，支付时可直接抵扣~'],function(){
				$.ajax("/active/scratch/send-message", {}, function(){});
			});
        {{else}}
        o.init(['谢谢参与！',''],function(){
				$.ajax("/active/scratch/send-message", {}, function(){});
			});
        {{/if}}
    })
</script>
</head>
<body>

    <div class="viewport bg_01">
            <div class="pic-block">
                <img src="/images/active/scratch/1_01.jpg" />
                <img src="/images/active/scratch/1_02.jpg" />
                <img src="/images/active/scratch/1_03.jpg" />
            </div>
            <!---刮刮乐-->
            <div class="game">
                <div class="game-box" onclick="$(this).remove();"></div>

                <div class="canvas-box">
                    <canvas width="550" height="335" id="canvas"></canvas>
                </div>
                <img src="/images/active/scratch/game-bg.png">
            </div>
            <!--over-->
            {{if $latestRow}}
            <div class="winner">
                <p class="float_left">最新中奖者：{{$latestRow.mobile}}</p>
                <p class="float_right">{{$latestRow.created|date_format:'%m-%d %H:%M'}}</p>
            </div>
            {{/if}}
            <div class="pic-block">
                <img src="/images/active/scratch/1_04.png" />
                <img src="/images/active/scratch/1_05.png" />
             </div>
            <!---list-->
                <div class="list">
						{{foreach from=$latestGoodArr.data item=item}}
                        <div class="list-col">
                            <div class="list-pic" style="background-image: url({{$item.imgList.0.img_url}})">
                                <span class="list-tag">{{$item.discount}}折</span>
                            </div>
                            <div class="list-right">
                                <p class="list-right-title">{{$item.ticket_title}}</p>
                                <p class="list-price">¥{{$item.selling_price}}</p>
                                <a class="list-btn" href="{{$item.buy_url}}">立即购买</a>
                            </div>
                            <img src="/images/active/scratch/col-bg.png">
                        </div>
					{{/foreach}}
              

                 </div>
                 <a class="link-more" href='#json={"cmark":"commodity_index"}'>更多商品，尽在名品街APP</a>
             <!--over-->
        <div class="pic-block">
            <img src="/images/active/scratch/1_06.png" />
        </div>
    </div>
<script type='text/javascript' src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
<script type="text/javascript">
$(function (){	
	wx.config({
		debug: false,
		appId: '{{$weixinKeyArr.Result.AppId}}',
		timestamp: {{$weixinKeyArr.Result.TimeStamp}}, 
		nonceStr: '{{$weixinKeyArr.Result.NonceStr}}', 
		signature: '{{$weixinKeyArr.Result.Signature}}',
		jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo']
	});
			 
	wx.ready(function() {
		var title = '{{$share.title}}';
		var desc = '{{$share.desc}}';
		var buyUrl = '{{$share.www_url}}';
		var imgUrl = '{{$share.img_url}}';
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
				getWin();
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
				getWin();
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
				getWin();
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
				getWin();
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
		cmslog.src = "/js/log.js";
		document.body.appendChild(cmslog);
	}, 1000);	
});
</script>
</body>
</html>