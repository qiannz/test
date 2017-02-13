<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' ticket=$ticketRow.ticket_title shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<link href="/css/wap.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
 <div class="wrap">
    <div class="header">
    	{{if $ticketRow.ticket_mark eq 'buygood'}}
        商品说明
        {{else}}
        本券使用说明
        {{/if}}
    </div>
    <div class="article">
    {{if $ticketRow.content}}
    	{{$ticketRow.content}}
    {{else}}
         {{$ticketRow.wap_content}}
         <br/>
         {{foreach from=$ticketRow.wap_img_list item=wap_row}}
            <img src="{{$wap_row.img_url}}" style="width:100%;"/>
         {{/foreach}}    
    {{/if}}
    </div>
</div>
</body>
</html>
