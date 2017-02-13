<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>{{$title}}</title>
<link href="/css/active/comeandgrap/common.css?t={{$version}}" rel="stylesheet" type="text/css">
</head>
<body>

    <div class="viewport">

<!--     <div class="top-header"> -->
<!--         <a class="top-header-return"></a> -->
<!--         <p>{$title}</p> -->
<!--      </div> -->
        <!--header-->
    <div class="header">

            <!-- <a class=" return-home"></a> -->

        <span class="header-left-txt">秒杀</span>

            <!-- a class="header-link-rule" ></a-->
			<a class="header-link-order" href="/active/comeandgrap/order-list"></a>
    </div>

    <!--tab title-->
    {{if $top_banner|@count neq 0}}
    <div class="tab-title">
		{{foreach from=$top_banner key=k item=banner}}
        <div class="tab-btn {{if $k eq 0}}select-on{{/if}}">
            <p class="font_16">周{{$banner.week}}</p>
            <p class="font_14">{{$banner.text}}</p>
        </div>
		{{/foreach}}
    </div>
    {{/if}}




        <!---1-->
        {{if $activities_in|@count neq 0 }}
        <div class="list" style="display: block">
        	{{foreach from=$activities_in item=acty_row}}
            <div class="box">

                <div class="box-pic">
                    <p class="over-time" data-status="in" data-value="{{$acty_row.timeout}}"></p>
                    <a href="/home/ticket/wap/tid/{{$acty_row.ticket_id}}">
                    	<div class="share-bg" style="display:none" id="display{{$acty_row.ticket_id}}"><span class="share-txt_01"></span></div>
                    	<img src="{{$acty_row.cover_img}}">
                    </a>
                </div>
                <div class="box-bottom">
                    <h4 class="box-bottom-title">{{$acty_row.ticket_title}}</h4>
                    <a class="btn_01" href="/home/ticket/wap/tid/{{$acty_row.ticket_id}}">立刻抢</a>
                    <div class="tag-box">
                        <p class="zhekou-tag">{{$acty_row.discount}}折</p>
                         惊爆价
                        <span class="tag-now">{{$acty_row.selling_price}}</span>
                        <span class="tag-old">{{$acty_row.par_value}}</span>
                    </div>

                </div>

                <div class="bar-box begin begin-{{$acty_row.ticket_id}}" data-tid="{{$acty_row.ticket_id}}" data-tuuid="{{$acty_row.ticket_uuid}}">

                    <div class="bar"  >
                        <p class="bar-color" style="width: 0%">0%</p>
                    </div>

                    <div class="number">

                        <div class="number-l">
                            <p>已发放</p>
                            <p id="hadsold{{$acty_row.ticket_id}}" class="clear">0名额</p>
                        </div>
                        <div class="number-r">
                            <p>剩余</p>
                            <p id="surplus{{$acty_row.ticket_id}}" class="stock">{{$acty_row.total}}名额</p>
                        </div>

                    </div>

                </div>

            </div>
			{{/foreach}}
        </div>
        {{/if}}
    	<!------2------>
    	{{foreach from=$activities_will item=activities}}
        <div class="list" {{if $activities_in|@count eq 0 }}style="display: block"{{/if}}>
        	{{foreach from=$activities item=acty_row}}
            <div class="box">

                <div class="box-pic">
                    <p class="over-time" data-status="new" data-value="{{$acty_row.timeout}}"></p>
                    <a href="/home/ticket/wap/tid/{{$acty_row.ticket_id}}"><img src="{{$acty_row.cover_img}}"></a>
                </div>
                <div class="box-bottom">
                    <h4 class="box-bottom-title">{{$acty_row.ticket_title}}</h4>
                    {{if $acty_row.is_notice eq 1}}
                    <a data-ticketid="{{$acty_row.ticket_id}}" class="btn_03">已提醒</a>
                    {{else}}
	                <a data-isnotice="{{$acty_row.is_notice}}" data-ticketid="{{$acty_row.ticket_id}}" class="btn_02">提醒我</a>
					{{/if}}
                    <div class="tag-box">
                        <p class="zhekou-tag">{{$acty_row.discount}}折</p>
                        惊爆价
                        <span class="tag-now">{{$acty_row.selling_price}}</span>
                        <span class="tag-old">{{$acty_row.par_value}}</span>
                    </div>

                </div>

                <div class="bar-box">
                    <p class="follow">已关注人数：<span class="color_01">{{$acty_row.prompted_num}}</span></p>

                </div>

            </div>
            {{/foreach}}
        </div>
        {{/foreach}}
    	<!------3------>
    	{{if $activities_tomorrow|@count neq 0 }}
        <div class="list" {{if $activities_in|@count eq 0 AND $activities_will|@count eq 0 }}style="display: block"{{/if}}>
        	{{foreach from=$activities_tomorrow item=acty_row}}
            <div class="box">

                <div class="box-pic">
                    <p class="over-time" data-status="new" data-value="{{$acty_row.timeout}}"></p>
                    <a href="/home/ticket/wap/tid/{{$acty_row.ticket_id}}">
                    	<img src="{{$acty_row.cover_img}}">
                    </a>
                </div>
                <div class="box-bottom">
                    <h4 class="box-bottom-title">{{$acty_row.ticket_title}}</h4>
                    {{if $acty_row.is_notice eq 1}}
                    <a data-ticketid="{{$acty_row.ticket_id}}" class="btn_03">已提醒</a>
                    {{else}}
	                <a data-isnotice="{{$acty_row.is_notice}}" data-ticketid="{{$acty_row.ticket_id}}" class="btn_02">提醒我</a>
					{{/if}}
                    <div class="tag-box">
                        <p class="zhekou-tag">{{$acty_row.discount}}折</p>
                        惊爆价
                        <span class="tag-now">{{$acty_row.selling_price}}</span>
                        <span class="tag-old">{{$acty_row.par_value}}</span>
                    </div>

                </div>

                <div class="bar-box">
                    <p class="follow">已关注人数：<span class="color_01">{{$acty_row.prompted_num}}</span></p>

                </div>

            </div>
			{{/foreach}}




        </div>
		{{/if}}
	


    <div class="u">
        <h3><img class="img_u" src="/images/active/comeandgrap/love.png"></h3>
		<div class="ajax_more"></div>
        <p class="loading load_img"></p>
    </div>
    </div>
    <script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
	<script type="text/javascript" src="/js/active/comeandgrap.js?t={{$version}}"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
	$(function(){
		$(".btn_02").click(function(e){
			var id = this;
			$.ajax({
                type:'POST',
                url:"/active/oneyuanpurchase/notice-me",
                data:{ticket_id:$(this).data("ticketid")},
                dataType:'json',
                success:function(data){
	                if( data.res == 101 ){
						//未登录
						window.location.href = "/active/oneyuanpurchase/login?jumpfrom=/active/comeandgrap";
		            }else if( data.res == 103 ){
		            	alert(data.msg);
			        }else if( data.res == 100 ){
			        	alert(data.msg);
			            var num = $(id).parents(".box").find(".color_01").html();
			            num = parseInt(num);
			            num++;
			            $(id).parents(".box").find(".color_01").html( num );
			            $(id).unbind('click').attr('class', 'btn_03').html('已提醒');
		            }
                }
            });
            e.stopPropagation();
		});
		var curPage = 1;
		moreAjax(curPage);
		$(".loading").click(function(){
			$(".loading").html("");
			$(".loading").addClass("load_img");
			curPage++;
			moreAjax( curPage );
		});
		$(".begin").each(function(){
			remainingTickets($(this).data("tid"),$(this).data("tuuid"));
		});
	});
	
	function moreAjax(page) {
		$.ajax({
			url:"{{$_CONF.GLOBAL_CONF.SITE_URL}}/home/daren/love-more",
			dataType:"json",
			data:{sid:"{{$sid}}","page":page},
			success:function(data){
				var _html = '';
				var _len = data.length;
				if(page > 1 && !_len) {
					alert('没有更多啦');
					$(".loading").hide();
					return false;
				}
				for(var i=0; i< _len; i++ ){
					_html += '<div class="pic1">'
					_html += '<div class="t">'+data[i].title+'</div>'
					_html += '<div class="price_foot">';
					if(data[i].cmark == 'voucher_view') {	
						_html += '<p class="p1">原价<span class="old_p_foot">¥' + data[i].par_value + '</span></p>'
						_html += '<p class="p2">¥<span class="big">'+data[i].selling_price+'</span></p>';
					}
					_html +='</div>'
					_html +='<a href="{{$_CONF.GLOBAL_CONF.SITE_URL}}/home/ticket/wap/tid/'+data[i].ticket_id+'/from/ios" target="_blank"><img src="' + data[i].img_url + '"></div></a>';
				}
				if(_html != '') {
					//$(_html).appendTo($("div.u").find("img").after());
					$(".ajax_more").append(_html);
					$(".loading").removeClass("load_img");
					$(".loading").html("点击加载更多");
				}
			}
		});	
	}
	
    function remainingTickets( ticket_id , ticket_uuid ){
		$.ajax({
            type:'POST',
            url:'/active/oneyuanpurchase/ticket-sold-num',
            data:{"tuuid":ticket_uuid},
            dataType:'json',
            success:function(data){
                $("#hadsold"+ticket_id).html(data["hadsold"]+"名额");
                $("#surplus"+ticket_id).html(data["surplus"]+"名额");
				if( data["surplus"] <= 0 ){
					$("#display"+ticket_id).show();
				}
				if( data["hadsold"]+data["surplus"] > 0 ){
					processFn($('.begin-'+ticket_id));
				}
				
            }
		});
	}

    $(function(){
    	
		wx.config({
			debug: false,
			appId: '{{$weixinKeyArr.Result.AppId}}',
			timestamp: {{$weixinKeyArr.Result.TimeStamp}}, 
			nonceStr: '{{$weixinKeyArr.Result.NonceStr}}', 
			signature: '{{$weixinKeyArr.Result.Signature}}',
			jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo']
		});
				 
		wx.ready(function() {
			var title = '{{$title}}';
			var desc = '{{$desc}}';
			var buyUrl = '{{$share_url}}';
			var imgUrl = '{{$share_img_url}}';
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
	
	});
	
	setTimeout(function(){
		var cmslog = document.createElement("script");
		cmslog.src = "http://buy.mplife.com/js/log.js";
		document.body.appendChild(cmslog);
	}, 1000);		
    </script>
</body>
</html>
