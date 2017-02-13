<!DOCTYPE html>
<html>
<head>
<title>达人说</title>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<meta name="share_title" content="{{$share.share_title}}"/>
<meta name="share_desc" content="{{$share.share_desc}}"/>
<meta name="share_img_url" content="{{$share.share_img_url}}"/>
<meta name="MplifeShareWeixinTitle" content="{{$share.share_title}}"/>
<meta name="MplifeShareWeixinDesc" content="{{$share.share_desc}}"/>
<meta name="MplifeShareWeixinImageUrl" content="{{$share.share_img_url}}"/>
<meta name="MplifeShareWeixinUrl" content=""/>
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<style type="text/css">
/* HTML5 Tags *==|== Reset Styles ===================================================== */html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,button,input,select,textarea,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;}html{font-size:100%;overflow-y:scroll;-webkit-text-size-adjust:none;}*html{background-image:url(about:blank);background-attachment:fixed;}ol,ul{list-style:none;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}table{border-collapse:collapse;border-spacing:0;}/* ==|== Clearing Float ====================================================== */.clearfix:before,.clearfix:after{content:"";display:table;}.clearfix:after{clear:both;overflow:hidden;}.clearfix{zoom:1;}/* ==|== Public Style ====================================================== */a,a:visited{text-decoration:none;outline:none;hide-focus:expression(this.hideFocus=true);}a:hover,a:active{text-decoration:none;}body,button,input,select,textarea{background:#fff;-webkit-font-smoothing:antialiased;}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block;}img{max-width:100%;height:auto;width:auto\9;/* ie8 */vertical-align:top;}.video embed,.video object,.video iframe{width:100%;height:auto;}input[type="button"],input[type="submit"]{-webkit-appearance:none;outline:none}


body{
	font-family:"Microsoft YaHei";
	font-size:12px;

}
img{
	max-width:640px;
	width:100%;
	vertical-align:top;
	
}
 a:link,a:visited,a:hover,a:active{color: #000;}

.wrap{
	min-width:320px;
	max-width:640px;
	margin:0 auto;
	background:#fff;
	width:100%;
	position: relative;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-ms-box-sizing: border-box;
	-o-box-sizing: border-box;
	box-sizing: border-box;
}

.wrap .title{ padding:2rem 1.5rem; text-align: center; font-size: 18px; font-weight: bold;  }
.wrap .infor{ padding: 0 1.5rem 2rem; font-size: 16px; color: #6f6f6f; line-height: 24px;text-align: justify; }
.line{height: 1px;  background:#eee;width: 90%; margin: 0 auto; margin-bottom: 14px;}
.list{ padding: 0 1.5rem;  overflow: hidden; background: url('/images/active/daren/line_03.gif') repeat-y center top;}

.list_item{width: 50%;border-bottom: 1px solid #eee; padding-bottom: 10px;  position: relative; float:left;margin-bottom: 10px; box-sizing:border-box; }
.list_item:nth-child(2n){  padding-left: 10px;}

.list_item:nth-child(2n+1){  padding-right: 10px;}
/*.list_item:last-child{border-bottom:none;}
.list_item:nth-last-child(2){border-bottom:none;}*/

.f{overflow: hidden; padding: 10px 0;font-size: 0.9rem}
.f_r{ float: right; }
.f_l{ float: left;max-width: 100%;text-overflow:ellipsis;overflow:hidden;white-space:nowrap; }

.list_item .dz{ color: #fff; font-size: 0.8rem; text-align: center; background: url('/images/active/daren/03.png') no-repeat center top; background-size: 100% auto; position: absolute; top: 9rem; left:1px; width: 4rem; height: 1.3rem; line-height: 1.3rem; }
.list_item:nth-child(2n) .dz{ left: 11px; } 
.price .by{ padding: 0.25rem 0.4rem;background: #23cdb7; color: #fff;border-radius: 2px; font-size: 14px; }
.old_price{text-decoration: line-through;font-size: 0.625rem;margin-left: 5.8rem;}
.new_price{color: #f00;font-size: 1rem; margin-left: 2.5rem;}
.u{ background:#eeeeee;/*background:#eeeeee url('../images/love.png') no-repeat center 0.6rem; background-size: 80% auto;*/padding: 0 1.5rem; /*padding-top: 3.5rem;*/ padding-bottom: 3rem;}
.img_u{padding-top: 0.8rem; padding-bottom: 0.8rem;}
.pic1{ 
	position: relative;margin-bottom: 4px;
   
 }
 .t{
 	position: absolute;
 	z-index: 8;
 	bottom: 0;
 	width: 100%; height: 25%;
 	background: -moz-linear-gradient(top,  rgba(250,250,250,0) 20%,  rgba(0,0,0,0.8) 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, top center, bottom center, color-stop(20%,rgba(250,250,250,0)), color-stop(100%,rgba(0,0,0,0.8))); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, rgba(250,250,250,0) 20%,rgba(0,0,0,0.8) 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, rgba(250,250,250,0) 20%,rgba(0,0,0,0.8) 100%); /* Opera 11.10+ */
    background: linear-gradient(to bottom, rgba(250,250,250,0) 20%,rgba(0,0,0,0.8) 100%); /* W3C */
 }
.price_foot{ position: absolute; left: 0; top: 4.5rem;z-index: 9; }
.price_foot .p1{ background: #f82a46; padding: 0.25rem 0.6rem; color: #fff; z-index: 9; }
.price_foot .p2{ background: #3c3a39; padding: 0.25rem 0.6rem;  color: #fff; z-index: 9; }
.old_p_foot{text-decoration: line-through;}
.price_foot .big{ font-size: 1.4rem; }
.pic1 .p3{ 
	text-align: center;
    position: absolute;
    bottom: 0.2rem;
    font-size: 16px;
    width: 100%; color: #fff;  z-index: 9;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    padding: 0 1.6%;
    box-sizing: border-box;
}
.pic1 .p3 a{ color: #fff; }
.list_item_img{ background-color: #fff; background-position: center; background-repeat: no-repeat; width: auto; height: 10.6rem; background-size:cover; }

.loading{ text-align: center; font-size: 14px;height: 16px; line-height: 16px; }

.load_img{ background: url('/images/active/daren/load.gif') no-repeat center;}

@media screen and (min-width:320px){
    html,body{
        font-size:12px;
    }

}

@media screen and (min-width:360px){
    html,body{
		font-size:13.5px;
	}
	
}

@media screen and (min-width:375px){
    html,body{
		font-size:14px;
	}

}

@media screen and (min-width:384px){
    html,body{
        font-size:14.4px;
    }

}

@media screen and (min-width:414px){
    html,body{
		font-size:15.5px;
	}

}

@media screen and (min-width:480px){
    html,body{
		font-size:18px;
	}
	
}
@media screen and (min-width:640px){
    html,body{
		font-size:24px;
	}

}
</style>
</head>
<body>
<div class="wrap">
    <div class="section">{{$specialRow.content}}</div>
	<div class="line"></div>



    <!--列表 -->
    <div class="list">
    	{{foreach from=$goodList key=key item=item}}
        <div class="list_item">
            <div class="dz">
                {{$item.discount}}折
            </div>
            
           <a href="{{$_CONF.GLOBAL_CONF.SITE_URL}}/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank"> <div class="list_item_img" style="background-image: url({{$item.first_img.img_url}});"></div></a>
            <div class="f">
            <p class="f_r"></p>
            <p class="f_l"><a href="{{$_CONF.GLOBAL_CONF.SITE_URL}}/home/ticket/wap/tid/{{$item.ticket_id}}" target="_blank">{{$item.ticket_title}}</a></p>
            </div>
            <div class="price">
                {{if $item.free_shipping eq 1}}<span class="by">包邮</span>{{/if}}
                <span class="new_price">¥{{$item.selling_price}}</span>
                <span class="old_price">¥{{$item.par_value}}</span>
    
            </div>
        </div>
        {{/foreach}}
    </div>


    <div class="u">
        <img class="img_u" src="/images/active/daren/wonderful.png">
       <div class="ajax_more"></div>
    </div>

</div>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type='text/javascript' src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
<script type="text/javascript">
$(function (){	

	$.ajax({
		url:"{{$_CONF.GLOBAL_CONF.SITE_URL}}/home/daren/daren-list",
		dataType:"json",
		data:{sid:"{{$sid}}"},
		success:function(data){
			var _html = '';
			var _len = data.length;
			for(var i=0; i< _len; i++ ){
				_html += '<div class="pic1">'
				_html += '<div class="t"></div>'
				_html += '<div class="price_foot">';
				_html +='</div><p class="p3"><a href="'+data[i].www_url+'" target="_blank">' + data[i].title + '</a></p>'
				_html +='<a href="'+data[i].www_url+'" target="_blank"><img src="' + data[i].img_url + '"></div></a>';
			}
			if(_html != '') {
				$(".ajax_more").append(_html);
			}
		}
	});	


	var share_title = "{{$share.share_title}}";
	var share_desc = "{{$share.share_desc}}";
	var share_img_url = "{{$share.share_img_url}}";
	var share_url = document.URL;
	$(function(){
		$.ajax({
			 url:"http://buy.mplife.com/api/good/get-weixin-key",
			 dataType:"jsonp",
			 data:{'request_url':share_url},
			 jsonp:"jsoncallback",
			 success:function(data){
				if( data.res != 100 ){
					return;
				}
				wx.config({
					debug: false,
					appId: data.extra.Result.AppId,
					timestamp: data.extra.Result.TimeStamp, 
					nonceStr: data.extra.Result.NonceStr, 
					signature: data.extra.Result.Signature,
					jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo']
				});
					 
				wx.ready(function() {
					var title = share_title;
					var desc = share_desc;
					var buyUrl = share_url;
					var imgUrl = share_img_url;
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
			},
			error:function(){
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