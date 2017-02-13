<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
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
            
            {{if $_CONF._A eq 'coupon-list'}}
                <li class="sel" ><a href="javascript:void(0)" >券管理</a></li>
            {{else}}
                <li><a href="/home/suser/coupon-list/sid/{{$sid}}">券管理</a></li>
            {{/if}}
            
            {{if $user.user_type eq 2}}    
                {{if $_CONF._A eq 'add-coupon'}}
                    <li class="sel" ><a href="javascript:void(0)" >发券</a></li>
                {{else}}
                    <li><a href="/home/suser/add-coupon/sid/{{$sid}}">发券</a></li>
                {{/if}}
            {{/if}}
            
            {{if $user.user_type eq 2}}
                {{if $_CONF._A eq 'valid'}}
                <li class="sel" ><a href="javascript:void(0)" >券验证</a></li>
                {{else}}
                <li><a href="/home/suser/valid/sid/{{$sid}}">券验证</a></li>
                {{/if}}
            {{elseif $user.user_type eq 3}}
                {{if in_array(4,$userPermission)}}
                    {{if $_CONF._A eq 'valid'}}
                    <li class="sel" ><a href="javascript:void(0)" >券验证</a></li>
                    {{else}}
                    <li><a href="/home/suser/valid/sid/{{$sid}}">券验证</a></li>
                    {{/if}}
                {{/if}}
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