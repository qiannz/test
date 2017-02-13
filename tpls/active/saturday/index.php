<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="">
<meta name="keywords" content="">
<link href="http://css.mpimg.cn/web/20120615/footer.css" rel="stylesheet" type="text/css">
<title>遇见星期六集团美鞋出柜,名品导购网</title>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="http://mpimg.cn/o2o/superbuy.js?v=2014-04-24.js"></script>
<script type="text/javascript" src="/js/active/js.js"></script>
<script type="text/javascript" src="/js/active/ZeroClipboard.js?t=1"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
$(function(){
var imglist=document.getElementsByTagName('img'),relist=[],arrsrc=[],iCur=0;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute('data-lazyload'));relist.push(imglist[i])}}
function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.height=Img.height;ele.width=Img.width;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"));}
ele.src=Img.src;};for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};

//复制链接
  var clip = null;
  function init() {
    clip = new ZeroClipboard.Client();
    clip.setHandCursor(true);

      clip.addEventListener('mouseOver', function (client) {
    // update the text on mouse over

          clip.setText( document.getElementById('fe_text').value);
    });

      clip.addEventListener('complete', function (client, text) {
    //debugstr("Copied text to clipboard: " + text );
    alert("该地址已经复制，你可以使用Ctrl+V 粘贴。");
    });
      clip.glue('clip_button', 'clip_container' );
  }
    {{if $user_id}}init();{{/if}}
				  
function show(btn,txt){
			$(btn).toggle(function(){
				$(this).text('查看详情-');
				$(txt).slideDown(500);
			},function(){
				$(this).text('查看详情+');
				$(txt).slideUp(500);
			})
		}
show('#showinfo-01','#ruletxt-01');
//show('#showinfo-02','#ruletxt-02');
mp({
	focusImg:'focus-img'
}).Focus();

mp({
	tabTitle:'tab-title',
	tabCon:'tab-con'
	}).Tab();

mp({gotop:'goTop'}).GoTop();

mp({
	tabTitle:'tab-title',
	tabCon:'tab-con',
	target:'target'
	}).SideTab();
	
	$(function () {
        var activityID = "bad427e2-351b-4830-993b-517d2ea25f8b";
        var stockObjID = "41stock";
        //初始化
        superbuy_init(stockObjID, activityID, "41num");
	});
});

</script>
<style type="text/css">
body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,input,textarea,th,td{margin:0;padding:0}
body,button,input,select,textarea{background:#fff;font:12px/1.5 arial,Hiragino Sans GB,\5b8b\4f53;-webkit-font-smoothing:antialiased;}
h1,h2,h3,h4,h5,h6{font-size:100%}
address,cite,dfn,em,var{font-style:normal}
code,kbd,pre,samp{font-family:courier new,courier,monospace}
ul,ol{list-style:none}
a{text-decoration:none}
a:hover{text-decoration:underline}
fieldset,img{border:0}
table{border-collapse:collapse;border-spacing:0}
.clearfix:before,.clearfix:after {content: ""; display: table;}
.clearfix:after {clear:both;overflow:hidden;}
.clearfix{zoom: 1;}
img{background:url(/images/act/saturday/loading.gif) no-repeat center center;}
.bg{background:url(/images/act/saturday/bg.jpg) no-repeat center top}
body{background:#edf4bf;}
.wrap{width:1000px; margin:0 auto}
.top-banner{text-align:center}
.top-banner img{vertical-align:top}
.focus{width:990px;height:441px;border:5px solid #79695c;background:#000;overflow:hidden;}
.focus ul{width:99999px;}
.focus-img{border-left:1px solid #2f2a26; border-bottom:1px solid #2f2a26;height:440px;font-size:0;}
.focus-img img{vertical-align:top;}
.focus-img li{width:72px;height:440px;display:inline-block;*display:inline;*zoom:1;overflow:hidden; background-position:right center; background-repeat:no-repeat;opacity:0.6;fitler:alpha(opacity=60);cursor:pointer;}
.focus-img .sel{opacity:1;fitler:alpha(opacity=100)}
.buy-coupons{height:189px;background:url(/images/act/saturday/buy-image.png) no-repeat;position:relative;z-index:1}
.box{position:absolute;top:49px;right:20px;width:237px;}
.box a{display:block}
.buy-btn{height:85px}
.link-more{height:25px;}
.box p{text-align:center;color:#fff; font-size:14px;}
.tab-title{height:59px;background:url(/images/act/saturday/tab-bg.png);position:relative;z-index:2}
.tab-title span{display:inline-block;*display:inline;*zoom:1;font-size:20px;font-family:"Microsoft YaHei";color:#fff;width:152px;height:45px;vertical-align:top;margin:14px 0 0 17px;line-height:45px;text-align:center;cursor:pointer;}
#m1{margin-left:315px;}
.tab-title .on{background:#fff;color:#403933;border-radius:4px 4px 0 0;}
.tab-logo{position:absolute;left:50%;top:0px;margin-left:-144px; vertical-align:top;z-index:2}
.tab-con{}
.tab-con img{vertical-align:top}
.tab-con li{display:none}
.side{position:fixed;top:50%;right:10px;height:626px;width:131px;background:url(/images/act/saturday/side.png) no-repeat;margin-top:-313px;}
.target{margin-top:93px;}
.target a{display:block;height:33px;; margin-bottom:4px;cursor:pointer;}
.bottom-dis{margin-top:215px;}
.bottom-dis a{display:block}
#goTop{height:29px;cursor:pointer}
.qq{height:67px;}
.look-shop{display:block;height:35px;}
.task-wrap{position:relative;z-index:9;}
.task-login,.task-r{float:left;position:relative;z-index:5}
.task-r{width:489px;background:url(/images/act/saturday/task-r.png) no-repeat;height:302px;}
.btn-r{font-size:20px;width:120px;height:27px;line-height:27px;color:#fff;text-align:center;background:#58c4b5; position:absolute;right:0;bottom:0; cursor:pointer}
.task-r-shade{ position:absolute;left:0;top:0;z-index:1;background:#000;opacity:0.6;fitler:alpha(opacity=60);height:100%;width:100%;}
.task-login-off,.task-login-on{width:511px;hposition:relative;z-index:1;height:302px;}
.task-login-on{background:url(/images/act/saturday/login-on.png) no-repeat;}
.task-login-off{background:url(/images/act/saturday/login-off.png) no-repeat;}
.login-btn{position:absolute;left:165px;top:158px;width:87px;height:22px;line-height:22px;background:#42342a;color:#fff;font-family:"Microsoft YaHei"; text-align:center;font-size:14px}
.ruletxt{ position:absolute;z-index:10;overflow:hidden;color:#fff;*background:#4c4c4c;background:rgba(0, 0, 0, 0.8); border:1px solid #fff;display:none;width:300px;}
.ruletxt p{ padding:0 3px;font-size:12px;color:#fff; font-weight:normal}
.btn-l{font-size:20px;width:120px;height:27px;line-height:27px;color:#fff;text-align:center;background:#9ed439; position:absolute;left:0;bottom:0; cursor:pointer}
.ruletxt_l{top:305px;left:0;}
.ruletxt_r{top:305px;right:0;}
.copy-link{position:absolute;top:172px;left:42px;}
.copy-link p{margin-bottom:7px;}
.link-text{width:240px;height:26px;border:none;padding-left:5px;color:#554d44; line-height:26px\9;}
.copy-link-title{color:#554d44;}
.copy-btn{display:block;background:#9ed439;color:#4f6a1c;width:53px;height:26px;line-height:26px;text-align:center;}
.task-code{position:absolute;top:161px;right:16px;}
.task-code img{vertical-align:top;}
.task-bottom{ position:absolute;left:172px;bottom:30px;color:#554d44;font-size:14px;}
.task-bottom a{padding:6px 10px; background:#42342a;color:#fff;margin-left:10px;}

.top1 { width:960px; height:30px; margin:0 auto; background:#555; line-height:30px; color:#ddd; font-size:12px; position:relative; z-index:2; overflow:hidden;font-family:"b8bf53";}
.top1 p { width:890px; height:30px; float:left; display:inline; padding-left:10px;font-size:12px;}
.top1 p a { margin:0 6px;}
.top1 p a:link,.top1 p a:visited,.top1 p a:hover,.top1 p a:active { line-height:30px; color:#ddd; font-size:12px;}
.top1 span { width:60px; height:30px; float:right; display:block; text-align:center;}
.top1 span a:link,.top1 span a:visited,.top1 span a:hover,.top1 span a:active { line-height:30px; color:#ddd; font-size:12px;}
.footer{background:#fff; text-align:center}
.footer img{ vertical-align:top}
</style>
</head>
<body>
<!--header-->
<div style="background: #555">
<div class="top1">
    <p style="padding-right:0;width: auto"><strong><a target="_blank" href="http://www.mplife.com/">首页</a></strong>&gt;&gt;<a target="_blank" href="http://www.mplife.com/zhekou/">折扣</a><a target="_blank" href="http://www.mplife.com/temai/">特卖</a><a target="_blank" href="http://www.mplife.com/shangquan/">商场</a><a target="_blank" href="http://www.mplife.com/global/">出境</a><a target="_blank" href="http://www.mplife.com/hk/">香港</a><a target="_blank" href="http://www.mplife.com/us/">美国</a><a target="_blank" href="http://www.mplife.com/korea/">韩国</a><a target="_blank" href="http://www.mplife.com/dress/">服饰</a><a target="_blank" href="http://www.mplife.com/star/">明星</a><a target="_blank" href="http://www.mplife.com/shoes/">鞋包</a><a target="_blank" href="http://www.mplife.com/luxury/">奢侈品</a><a target="_blank" href="http://www.mplife.com/beauty/">美颜</a><a target="_blank" href="http://www.mplife.com/shop/">淘店</a><a target="_blank" href="http://www.mplife.com/home/">生活</a><a target="_blank" href="http://www.mplife.com/digi/">酷玩</a><a target="_blank" href="http://www.mplife.com/picture/">美图</a><a target="_blank" href="http://www.mplife.com/special/">专题</a><a target="_blank" href="http://bbs.mplife.com/">论坛</a><a target="_blank" href="http://passport.mplife.com/cardindex.aspx">会员卡</a></p>
    <span style="padding-right:5px;width: auto" id="loginBox">
        {{if !$user_id}}
        <a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}">登录</a>
        {{else}}
        <a href="http://passport.mplife.com/userinfo/perProfile.aspx?u={{$user_name}}" target="_blank">{{$user_name}}</a> |
        <a href="javascript:loginEx();" id="loginEx">退出</a> |
        <a class="fontred" target="_blank" href="http://passport.mplife.com/O2oMyOrder/MyOrder.aspx">我的订单</a>
        {{/if}}
    </span>
</div>
</div>
<!--header-->
	<div class="bg">
		<div class="wrap">
			<div class="top-banner">
				<img data-lazyload='/images/act/saturday/top-banner-01.jpg' src="/images/act/saturday/blank.png">
				<img data-lazyload='/images/act/saturday/top-banner-02.jpg' src="/images/act/saturday/blank.png">
				<img data-lazyload='/images/act/saturday/top-banner-03.jpg' src="/images/act/saturday/blank.png">
			</div>
			<!--焦点图-->
			<div class="focus">
				<div class="focus-img" id="focus-img">
					<ul>
						<li style="background-image:url('/images/act/saturday/1.jpg');width:630px;" class="sel"></li>
						<li style="background-image:url('/images/act/saturday/2.jpg')"></li>
						<li style="background-image:url('/images/act/saturday/6.jpg')"></li>
						<li style="background-image:url('/images/act/saturday/5.jpg')"></li>
						<li style="background-image:url('/images/act/saturday/3.jpg')"></li>
						<li style="background-image:url('/images/act/saturday/4.jpg')"></li>
					</ul>
				</div>
			</div>
			<!--现金卷-->
			<div class="buy-coupons">
					<div class="box">
						<a class="buy-btn"  href="{{$recommend_url}}" target="_blank"></a>
						<a class="link-more" href="ny.html" target="_blank"></a>
						<p>总计<span id="41num"></span>张&nbsp;&nbsp;&nbsp;已售出<span id="41stock"></span>张</p>
					</div>
			</div>
			<!--任务-->
			<div class="task-wrap clearfix">
            <!--左边-->
				<div class="task-login">
					<span id="showinfo-01" class="btn-l">查看详情+</span>
                        <div class="ruletxt ruletxt_l" id="ruletxt-01">
                            <p>1.可累计使用，不兑换可退款</p>
                            <p>2.本券仅可在发券店指定百货服饰专柜使用</p>
                            <p>3.黄金、餐饮、租户、乐淘小铺及特例专柜除外</p>
                            <p>4.本券不参加VIP消费积分</p>
                            <p>5.本券使用细则如有调整，以现场告示为准</p>
                            <p>6.本券盖章有效，仅可在有效期内使用，逾期无效</p>
                            <p>7.本券一经兑换，不退现、不记名、不挂失、不兑换现金、不开具发票、不找零</p>                   
                        </div>
					<!--未登陆-->
                    {{if !$user_id}}
					<div class="task-login-off">
						<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank" class="login-btn">登录</a>
					</div>
                    {{else}}
					<!--登陆-->
					<div class="task-login-on">
						<div class="copy-link">
							<p class="copy-link-title">专属链接</p>
							<p><input type="text" class="link-text" id="fe_text" value="{{$url}}"></p>
							<p id="clip_container" style=" position:relative"><a class="copy-btn" id="clip_button">复  制</a></p>
						</div>
						<div class="task-code">
							<img src="{{$imgstr}}" width="74" height="78">
						</div>
						<div class="task-bottom">
							已成功邀请{{$invitenum}}张<a target="_blank" href="http://buy.mplife.com/home/user/my-task-info?task_type=7">查看我的返利金额</a>
						</div>
					</div>
                    {{/if}}
				</div>
				 <script type="text/javaScript">

			</script>
              <!--右边-->
               <div class="task-r">
					<div class="task-r-shade"></div>
					<span id="showinfo-02" class="btn-r">查看详情+</span>
                        <div class="ruletxt ruletxt_r" id="ruletxt-02">
                            <p>1.可累计使用，不兑换可退款</p>
                            <p>2.本券仅可在发券店指定百货服饰专柜使用</p>
                            <p>3.黄金、餐饮、租户、乐淘小铺及特例专柜除外</p>
                            <p>4.本券不参加VIP消费积分</p>
                            <p>5.本券使用细则如有调整，以现场告示为准</p>
                            <p>6.本券盖章有效，仅可在有效期内使用，逾期无效</p>
                            <p>7.本券一经兑换，不退现、不记名、不挂失、不兑换现金、不开具发票、不找零</p>                   
                      </div>
			   </div>
			</div>
			<!--任务 end-->
			<!--tab-->
			<div class="tab-title" id="tab-title">
				<span class="on">盛夏凉鞋</span>
				<span>舒适平底鞋</span>
				<span id='m1'>女王高跟鞋</span>
				<span>俏丽鱼嘴鞋</span>
				<img src="/images/act/saturday/tab-logo.jpg" width="321" height="78" class="tab-logo">
			</div>
			<div class="tab-con" id="tab-con">
				<ul>
					<li style="display:block">
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/1_1.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/1_2.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/1_3.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/1_4.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/1_5.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/1_6.jpg' src="/images/act/saturday/blank.png"></a>
					</li>
					<li>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/2_1.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/2_2.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/2_3.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/2_4.jpg' src="/images/act/saturday/blank.png"></a>
					</li>
					<li>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_1.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_2.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_3.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_4.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_5.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_6.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/3_7.jpg' src="/images/act/saturday/blank.png"></a>
					</li>
					<li>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/4_1.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/4_2.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/4_3.jpg' src="/images/act/saturday/blank.png"></a>
						<a href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"><img data-lazyload='/images/act/saturday/4_4.jpg' src="/images/act/saturday/blank.png"></a>
					</li>
				</ul>
			</div>
            <div class="footer">
                <img src="/images/act/saturday/footer.png">
            </div>
		</div>
	</div>
	<!--side-->
	<div class="side">
		<div class="target" id="target">
			<a href="#tab-title"></a>
			<a href="#tab-title"></a>
			<a href="#tab-title"></a>
			<a href="#tab-title"></a>
		</div>
		<a></a>
		<a class="look-shop" target="_blank" href="/active/saturday/ny"></a>
		<a class="look-shop" target="_blank" href="http://bbs.mplife.com/showtopic-1249026.html"></a>
		<div class="bottom-dis">
			<a id="goTop"></a>
			<a class="qq" href="tencent://message/?Menu=yes&uin=1692356120&Service=300&sigT=45a1e5847943b64c6ff3990f8a9e644d2b31356cb0b4ac6b24663a3c8dd0f8aa12a595b1714f9d45" target="_blank"></a>
		</div>
	</div>
     <!--footer-->
<script type="text/javascript" src="http://mpimg.cn/web/20120615/footer.js"></script>
	<!--footer--> 
</body>
<script>
    function loginEx(){
        $.ajax({
            url:"http://passport.mplife.com/tools/userlogin.ashx",
            dataType:"jsonp",
            data:{"act":"loginout","cross":1},
            jsonp:"jsoncallback",
            success:function(data){
                var _data=data[0]
                if(_data.result==100){
                    $("#loginBox").html("<a href=\"http:\/\/passport.mplife.com\/login.aspx\" target=\"_blank\">登录<\/a>");
                    location.href = location ;
                }
            }
        })
    }
</script>
</html>
