<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>超级购-名品导购网</title>
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/ny.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
</head>
<body>
	<div class="w1210">
      <!--top-->
      {{include file='top.php'}}
      <!--nav-->
      {{include file='nav.php'}}
      <!--内页-->
      <div class="nyWaper">
          <div class="register-box">
                <!--头-->
                <div class="register-title">
                    <h2>提示<span class="en">Prompt</span></h2>
                </div>
                <!--头end-->
                <!--提示-->
                <div class="reg-prompt">
                    <p>
                        {{$message}}
                        {{if $redirect}}<br />
                        <a href="{{$redirect}}">返回上一页</a>
                        <script type="text/javascript">
                            setTimeout(function(){window.location = '{{$redirect}}'}, 3000);
                        </script>
                        {{/if}}
                    </p>
                    {{if $links}}
                    <p>您可以 {{foreach from=$links item=item}}<a href="{{$item.href}}"> {{$item.text}}</a>　{{/foreach}}</p>  
                    {{/if}}                 
                </div>
                <!--提示end-->
            </div>
      </div>
          <!--关于超级购-->
{{include file='bottom.php'}}
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
})
</script>
{{include file='footer.php'}}
