<!DOCTYPE html>
<html>
<head>
<title>{{$title}}</title>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
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
.list_item:last-child{border-bottom:none;}
.list_item:nth-last-child(2){border-bottom:none;}

.f{overflow: hidden; padding: 10px 0;font-size: 0.9rem}
.f_r{ float: right; }
.f_l{ float: left;max-width: 7rem;text-overflow:ellipsis;overflow:hidden;white-space:nowrap; }
.list_item .dz{ color: #fff; font-size: 0.8rem; text-align: center; background: url('/images/active/daren/03.png') no-repeat center top; background-size: 100% auto; position: absolute; top: 9rem; left:1px; width: 4rem; height: 1.3rem; line-height: 1.3rem; }
.list_item:nth-child(2n) .dz{ left: 11px; } 
.price .by{ padding: 0.25rem 0.4rem;background: #23cdb7; color: #fff;border-radius: 2px; font-size: 14px; }
.old_price{text-decoration: line-through;font-size: 0.625rem;margin-left: 0.5rem;}
.new_price{color: #f00;font-size: 1.25rem; margin-left: 0.5rem;}
.u{ background:#eeeeee;/*background:#eeeeee url('../images/love.png') no-repeat center 0.6rem; background-size: 80% auto;*/padding: 0 1.5rem; /*padding-top: 3.5rem;*/ padding-bottom: 3rem;}
.img_u{padding-top: 0.8rem; padding-bottom: 0.8rem;}
.pic1{ 
	position: relative;margin-bottom: 4px;
   
 }
 .t{
 	position: absolute;
 	z-index: 8;
 	top: 5.4rem;
 	width: 100%; height: 50%;
 	background: -moz-linear-gradient(top,  rgba(250,250,250,0) 0%,  rgba(0,0,0,0.8) 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, top center, bottom center, color-stop(0%,rgba(250,250,250,0)), color-stop(100%,rgba(0,0,0,0.8))); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, rgba(250,250,250,0) 0%,rgba(0,0,0,0.8) 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, rgba(250,250,250,0) 0%,rgba(0,0,0,0.8) 100%); /* Opera 11.10+ */
    background: linear-gradient(to bottom, rgba(250,250,250,0) 0%,rgba(0,0,0,0.8) 100%); /* W3C */
 }
.price_foot{ position: absolute; left: 0; top: 4.5rem;z-index: 9; }
.price_foot .p1{ background: #f82a46; padding: 0.25rem 0.6rem; color: #fff; z-index: 9; }
.price_foot .p2{ background: #3c3a39; padding: 0.25rem 0.6rem;  color: #fff; z-index: 9; }
.old_p_foot{text-decoration: line-through;}
.price_foot .big{ font-size: 1.4rem; }
.pic1 .p3{ 
	text-align: center;
    position: absolute;
    bottom: 0.6rem;
    font-size: 16px;
    width: 100%; color: #fff;  z-index: 9;}


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
    <div class="section">
    <h3 class="title">日本经常卖断货的10款超红好物</h3>
    <p class="infor">为什么日本的东东辣么火？当然除了华丽丽的外表，确实是因为真的好用，人气口碑都很高。于是口口相传，引起各大代购们的疯抢。究竟那些产品是代购卖的最火爆的呢？下面编辑就为大家盘点去日本必抢的10款好物，也是最近代购很容易卖断货的热销产品哦！</p>
    </div>
<div class="line"></div>



<!--列表 -->
<div class="list">

    <div class="list_item">
        <div class="dz">
            7.2折
        </div>
        <img src="/images/active/daren/1.jpg">
        <div class="f">
        <p class="f_r"> < 100m </p>
        <p class="f_l">Forever21 上衣</p>
        </div>
        <div class="price">
            <span class="by">包邮</span>
            <span class="new_price">¥285</span>
            <span class="old_price">¥890</span>

        </div>
    </div>



</div>


<div class="u">
	<img class="img_u" src="/images/active/daren/love.png">
    
    <div class="pic1">
        <div class="t"></div>
        <div class="price_foot">
            <p class="p1">原价<span class="old_p_foot">¥300</span></p>
            <p class="p2">¥<span class="big">100</span></p>
        </div>
        <p class="p3">Forever new 现金券3折甩卖</p>
        <img src="/images/active/daren/pic1.jpg">
    </div>





<p class="loading">点击加载更多</p>


</div>

</div>

<script type="text/javascript">
$(function (){
	$(".loading").click(function (){
		$(this).addClass("load_img").text(" ")
	})
})
</script>
</body>
</html>