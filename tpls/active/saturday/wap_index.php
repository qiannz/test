<!DOCTYPE html>
<html>
<head>
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
<title>星期六活动--名品导购网</title>
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript" src="http://mpimg.cn/o2o/superbuy.js?v=2014-04-24.js"></script>

<!--<script type="text/javascript">
$(function(){
	var imglist=document.getElementsByTagName('img'),relist=[],arrsrc=[],Img=new Image(),iCur=0;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute('data-lazyload'));relist.push(imglist[i])}}
Img.src=arrsrc[0];Img.onload=function(){relist[iCur].src=Img.src;relist[iCur].removeAttributeNode(relist[iCur].getAttributeNode("data-lazyload"));if(iCur<relist.length-1){iCur++;Img.src=arrsrc[iCur];}};Img.src=arrsrc[iCur];
})</script>-->
<script type="text/javascript">
/**
share_weixin
设置分享内容
*/
var linkHref =  window.location.href;
var _link = linkHref;
var _share_src = "http://img.mpimg.cn/web/wxpic/share-20140814.jpg";
var _desc = "星期六集团旗下ST&SAT、D:FUSE等品牌全场新品折后再享70购100元现金券优惠！";
var _title = "8月12日-9月12日星期六集团新品折扣再享70购100元现金券优惠";

/*设置如何初始化分享*/
if (typeof WeixinJSBridge === "undefined") {
	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		do_weixin_share();
	}, false)
}else {
	do_weixin_share();
}
/*分享项*/
function do_weixin_share() {
	/*发送给好友*/
	WeixinJSBridge.on('menu:share:appmessage', function (argv) {
		share_weixin_friend();
	});
	/*分享到朋友圈*/
	WeixinJSBridge.on('menu:share:timeline', function (argv) {
		share_weixin_timeLine();
	});
	/*分享到微博*/
	WeixinJSBridge.on('menu:share:weibo', function (argv) {
		share_weixin_t();
	});
}
/*分享自定义*/
function share_weixin_friend() {
	WeixinJSBridge.invoke('sendAppMessage', {
	"img_url": _share_src,
	"img_width": "100",
	"img_height": "100",
	"link": _link,
	"desc": _desc,
	"title": _title
	});
	}
	function share_weixin_timeLine() {
	WeixinJSBridge.invoke('shareTimeline', {
	"img_url": _share_src,
	"img_width": "100",
	"img_height": "100",
	"link": _link,
	"desc": _desc,
	"title": _title
	});
	}
	function share_weixin_t() {
	WeixinJSBridge.invoke('shareWeibo', {
	"img_url": _share_src,
	"content": _title + "，快去看看吧~ " + _link,
	"url": _link
	});
}
</script>

<script>

	$(function(){
		
		

		
		var strLink = window.location.href,
	
	nowLink = $('.btn').attr('href'),
	re = /rid\=[\w\W]{0,}/;
  	strLink.replace(re,function(str){
	   $('.btn').attr('href',nowLink+'&'+str);
	  })

		
			$(function () {
		        var activityID = "bad427e2-351b-4830-993b-517d2ea25f8b";
				var stockObjID =  "40stock";
           //初始化
            superbuy_init(stockObjID, activityID,"40num");
			
			})
		
		
		
		})


</script>

</head>
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
        /*background:url(../images/loading.gif) no-repeat center center; */
    }



    .wrap{
        min-width:320px;
        max-width:640px;
        margin:0 auto;
        background:#fff;

    }

    #buy{ position:relative;}
    .buy_box { position:relative;z-index:1;width:100%}

    .buy_w { width:50%;position:absolute; bottom:0.2em; right:0;}
    .btn { display:block; width:60%;  height:2.8em; margin-left:3em; }
    .btn_ds { text-align:center; margin-left:1em;}
    .more{ display:block; width:100%; height:1.8em; background:#CC3;}


    @media screen and (min-width:480px){
        body{
            font-size:16px;
        }
        .con2 img{
            max-width:601px;

            width: 93.8%;
        }
    }
    @media screen and (min-width:640px){
        body{
            font-size:24px;
        }

    }

</style>


<body>
	<div class="wrap" id="buy">
    	<img src="/images/act/saturday/1_01.jpg" />
      	 <div class="buy_box">
         	<div class="buy_w">
                <a class="btn" href="http://superbuy.mplife.com/Wap/Pay/Buy.aspx?i=bad427e2-351b-4830-993b-517d2ea25f8b" target="_blank"></a>
                <div class="btn_ds">
                    <span>总计</span><span id="40num">100</span>张<span style="margin-left:0.2em;">已售</span><span id="40stock">100</span>张
                </div>
              
            </div>
            <img src="/images/act/saturday/1_02.jpg" />
            </div> 
            <a href="/active/saturday/wap-ny" target="_blank"><img src="/images/act/saturday/1_011.jpg" /></a>
        <img src="/images/act/saturday/1_03.jpg" />
        <img src="/images/act/saturday/1_04.jpg" />
        <img src="/images/act/saturday/1_05.jpg" />
        <img src="/images/act/saturday/1_06.jpg" />
        <img src="/images/act/saturday/1_07.jpg" />
        <img src="/images/act/saturday/1_08.jpg" />
        <img src="/images/act/saturday/1_09.jpg" />
        <img src="/images/act/saturday/1_10.jpg" />
       <!-- <div class="buy_box">
          
        </div>    -->
    </div>
</body>
</html>
