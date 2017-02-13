<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>欢乐大转盘-名品导购网</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
<link href="css/wap.css" rel="stylesheet" type="text/css">
<style type="text/css">
/* HTML5 Tags *==|== Reset Styles ===================================================== */html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,button,input,select,textarea,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;}html{font-size:100%;overflow-y:scroll;-webkit-text-size-adjust:none;}*html{background-image:url(about:blank);background-attachment:fixed;}ol,ul{list-style:none;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}table{border-collapse:collapse;border-spacing:0;}/* ==|== Clearing Float ====================================================== */.clearfix:before,.clearfix:after{content:"";display:table;}.clearfix:after{clear:both;overflow:hidden;}.clearfix{zoom:1;}/* ==|== Public Style ====================================================== */a,a:visited{text-decoration:none;outline:none;hide-focus:expression(this.hideFocus=true);}a:hover,a:active{text-decoration:none;}body,button,input,select,textarea{background:#fff;-webkit-font-smoothing:antialiased;}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block;}img{max-width:100%;height:auto;width:auto\9;/* ie8 */vertical-align:top;}.video embed,.video object,.video iframe{width:100%;height:auto;}input[type="button"],input[type="submit"]{-webkit-appearance:none;outline:none}
body{
	font-family:"Microsoft YaHei";
	font-size:12px;
	background:#c0c0c0;
}

.wrap{
	min-width:320px;
	max-width:640px;
	margin:0 auto;
	background:url(/images/act/wheel/con_bg.jpg) no-repeat #481446 ;
	background-size:100% auto;
	 padding-top:8em;
}

.data_box { border:#000 solid 1px; width:21em; background:#FFF; margin:0 auto; padding:1em;}

.data_box ul{ float:left; width:5.2em; text-align:center; }
.data_box .li_top { background:#333333;color:#FFF; height:1.8em; line-height:1.8em;}
.data_box .li_con { border:#c9c9c9 solid 1px; height:2.7em; line-height:2.7em;}



.data_box td{border:#c9c9c9 solid 1px; height:2.7em;  width:5.13em;  text-align:center; vertical-align:middle;}

.more { text-align:center; display:block; cursor:pointer;}
@media screen and (min-width:480px){
	body{
		font-size:18px;
	}	
}
@media screen and (min-width:640px){
	body{
		font-size:24px;
	}

}
</style>
<script>
	$(function(){
		var iCur = 1;
		addhtml(iCur);
		$(".more").click(function(){
				iCur++;
				addhtml(iCur);
			});
		
		
		function addhtml(){
			var _html = "";
			
			$.getJSON('/active/wheel/my-ajax-wheel', {page:iCur}, function(data){
				$.each(data, function(k, v){
					_html += "<tr><td style='line-height:1.3em;'>" + v.month_day+ "<br> " + v.hour_minute+ "  </td> <td>" + v.action+ "</td><td>" + v.number+ "</td> <td>" + v.winning+ "</td> </tr>";
				});
				if(!_html) {
					alert('没有更多啦');
				} else {
					$('table').append(_html);
				}
			});
		}
	});

</script>
</head>
<body>
	 <div class="wrap" >
		<dl class="data_box clearfix">
        	<ul>
            <li class="li_top">时期</li>
          
           </ul>
           <ul>
            <li  class="li_top">操作</li>
            
           </ul>
           <ul>
            <li  class="li_top">幸运星</li>
          
        
           </ul>
           <ul>
            <li  class="li_top">获奖情况</li>
         
           </ul>
           <table  border="0" align="center">
                     

                    </table>
			<a class="more">查看更多</a>
        </dl>
		
	</div>

    
</body>
</html>