<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link  rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
</head>
<body>
    <!--site-->
    {{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
    {{include file='center/left.php'}}
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
    		<div class="tab-title">
                <ul>
                    {{if $_CONF._A eq 'buy-good'}}
                    	<li class="sel" ><a href="javascript:void(0)">团购商品</a></li>
                    {{else}}
                    	<li><a href="/home/suser/buy-good/sid/{{$sid}}">商品管理</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'sold-orders'}}
                        <li class="sel"><a href="javascript:void(0)">售出订单</a></li>
                    {{else}}
                        <li><a href="/home/suser/sold-orders/sid/{{$sid}}">售出订单</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'buy-release'}}
                        <li class="sel"><a href="javascript:void(0)">发起团购</a></li>
                    {{else}}
                        <li><a href="/home/suser/buy-release/sid/{{$sid}}">发起团购</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'veriy'}}
                        <li class="sel"><a href="javascript:void(0)">团购验证</a></li>
                    {{else}}
                        <li><a href="/home/suser/veriy/sid/{{$sid}}">团购验证</a></li>
                    {{/if}}
                </ul>
            </div> 
           {{include file='center/suser/valid_voucher.php'}}
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
</body>
</html>