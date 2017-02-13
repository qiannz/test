<!DOCTYPE html>
<html>
<head>
<title>名品街APP用户调查问卷,名品导购网</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="MobileOptimized" content="320" /><meta name="apple-mobile-web-app-status-bar-style" content="black" />
<link rel="apple-touch-icon-precomposed" href="/images/logo-icon.png" />
<link rel="icon" href="http://mpimg.cn/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="http://mpimg.cn/jquery.js"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<style type="text/css">
/* HTML5 Tags *==|== Reset Styles ===================================================== */html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,button,input,select,textarea,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;}html{font-size:100%;overflow-y:scroll;-webkit-text-size-adjust:none;}*html{background-image:url(about:blank);background-attachment:fixed;}ol,ul{list-style:none;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}table{border-collapse:collapse;border-spacing:0;}/* ==|== Clearing Float ====================================================== */.clearfix:before,.clearfix:after{content:"";display:table;}.clearfix:after{clear:both;overflow:hidden;}.clearfix{zoom:1;}/* ==|== Public Style ====================================================== */a,a:visited{text-decoration:none;outline:none;hide-focus:expression(this.hideFocus=true);}a:hover,a:active{text-decoration:none;}body,button,input,select,textarea{background:#fff;font:12px/1.5 "Microsoft YaHei",arial,Hiragino Sans GB,\5b8b\4f53;-webkit-font-smoothing:antialiased;}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block;}img{max-width:100%;height:auto;width:auto\9;/* ie8 */vertical-align:top;}.video embed,.video object,.video iframe{width:100%;height:auto;}input[type="button"],input[type="submit"]{-webkit-appearance:none;outline:none}

body{
	font-family:"Microsoft YaHei";
	backgorund-color:#fff;
	color:#544326;
	font-size:16px;
	background:url(/images/bg1.jpg) no-repeat top center;
	background-size:cover;
}
@media screen and (min-width:480px){body{font-size:20px;}
}
@media screen and (min-width:640px){body{font-size:24px;}
}
.wrap{
	margin:0 auto;
	max-width:640px;
	min-width:320px;
	font-size:1.125em;
	
}
.header{
	text-align:center;
	padding-top:1.5em;
	}
.header h2{
	font-size:2.375em;
	font-weight:bold;
}
.header p{
	font-size:1.5625em;
}
.section{
	padding:1.5em 0.3em;
	line-height:1.625em;
}
.dear{
	font-weight:normal;
}
.start-txt{
	text-indent:2.25em;
}
.btn-pic{
	width:7.81em;
	height:1.81em;
	line-height:1.81em;
	display:block;
	margin:0 auto;
	background:#544326;
	border-radius:0.18em;
	color:#fff;
	text-align:center;
	margin-top:0.8em;
}
input[type='radio'],input[type='checkbox']{
	font-size:0.875em;
	width:0.875em;
	height:0.875em;
	vertical-align:middle;
}
.question-txt{
	overflow:hidden;
	zoom:1
}
.col{
	float:left;
	width:6em;
}
.textarea{
	font-size:1em;
	width:100%;
	height:10em;
	background:#fff;
	margin-top:0.5em;
	resize:none;
	border:1px solid #ccc;
}
.success-txt{
	font-size:1.2em;
	text-align:center;
	margin-bottom:2em;
}
</style>
</head>
<body>
<div class="wrap">
	<header class="header">
    	<h2>名品街APP</h2>
        <p class="">用户调查问卷</p>
    </header>
    <section class="section">
    {{if $step eq 'home'}}
    <h3 class="dear">尊敬的用户：</h3>
    <p class="start-txt">感谢您下载和使用名品街APP，您的支持是我们成长最大的动力。这款APP刚刚面世不久，我们迫切的希望了解您对它的评价和使用体验。如果您愿意花费几分钟时间帮我们填写一下下面的些许问题，必将给予我们莫大的帮助和激励。</p>
    <a class="btn-pic" href="/home/wap/inquire/step/{{$next}}">开  始</a>
    {{elseif $step eq 'submit'}}
    <p class="question-txt">感谢您的认真填写和宝贵时间，请点击以下按钮提交问卷。</p>
    <a class="btn-pic" href="/home/wap/inquire/step/{{$next}}">提交问卷</a>
    {{elseif $step eq 'success'}}
    <p class="success-txt">提交成功！</p>
    <p class="success-txt"> 请点击屏幕左上角返回名品街APP继续浏览商品</p>
    {{else}}
    	{{if $inquireContent}}
        	<p class="question-txt">{{$stepNum}}. {{$inquireContent.title}}</p>
            {{if $inquireContent.type eq 0}}
            	{{foreach from=$inquireContent.child key=key item=item}}
            	<p class="question-txt"><input type="radio" name="answer" value="{{$item.survey_detail_id}}">{{$item.survey_detail}}</p>
                {{/foreach}}
                <a class="btn-pic" href="javascript:void(0)" onclick="nextJump({{$inquireContent.type}}, {{$inquireContent.survey_id}}, {{$next}})">下一题</a>
            {{elseif $inquireContent.type eq 1}}
            	{{foreach from=$inquireContent.child key=key item=item}}
            	<p class="question-txt"><input type="checkbox" name="answer[]" value="{{$item.survey_detail_id}}">{{$item.survey_detail}}</p>
                {{/foreach}}
                <a class="btn-pic" href="javascript:void(0)" onclick="nextJump({{$inquireContent.type}}, {{$inquireContent.survey_id}}, {{$next}})">下一题</a>
            {{elseif $inquireContent.type eq 2}}
            	<textarea name="answer" cols="" rows="" class="textarea"></textarea >
                <a class="btn-pic" href="javascript:void(0)" onclick="nextJump({{$inquireContent.type}}, {{$inquireContent.survey_id}}, {{$next}})">下一题</a>
            {{/if}}
            <script type="text/javascript">
				function nextJump(itype, survey_id, next) {
					var answer = '';
					switch(itype) {
						case 0:
							if($("input[type=radio][name=answer]:checked").length == 0) {
								alert('请选择一个问卷答案');
								return false;
							}
							answer = $("input[type=radio][name=answer]:checked").val();
							break;
						case 1:
							if($("input[type=checkbox][name^=answer]:checked").length == 0) {
								alert('请至少选择一个问卷答案');
								return false;
							}
							$("input[type=checkbox][name^=answer]:checked").each(function(){
								answer += this.value + ',';
							});
							answer = answer.substr(0, (answer.length - 1));
							break;
						case 2:
							answer = $('textarea[name=answer]').val();
							break;
					}
					$.post("/home/wap/inquire-result", {itype:itype, survey_id:survey_id, answer:answer}, function(json) {
						var obj = eval( '(' + json + ')');
						if(obj.res == 100) {
							window.location.href = '/home/wap/inquire/step/' + next;
						}
					});
					
				}
            </script>
        {{/if}}
	{{/if}}
    </section>
</div>
</body>
</html>