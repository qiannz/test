<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>名品导购网</title>
<link href="/css/discount.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">

</head>
<body>

    <div class="viewport">

        <!--header-->
        <!--<div class="header">
            <a class="return-link"></a>
            <a class="share-link"></a>
            <p>折扣详情</p>
        </div>-->
        <!--zhekou data-->
        <div class="zk-data">
            <h3>{{$discount.title}}</h3>
            {{if $discount.brand|@count neq 0 }}
            	<p class="zk-data-row">
	               <span class="col_01">品牌</span>
	               <span class="col_02">
	                   	{{foreach from=$discount.brand key=key item=item}}               
	                       {{$item.brand_name}}&nbsp;&nbsp;
	                    {{/foreach}}
	               </span>
	             </p>
			{{/if}}

            <p class="zk-data-row">
                <span class="col_01">时间</span>
                <span class="col_02">{{$discount.date}}</span>
                <!-- <a class="zk-btn btnStyle_01"></a> -->
            </p>
			
            <p class="zk-data-row">
                <span class="col_01">地点</span>
                <span class="col_02">{{$discount.address}}</span>
                <!-- <a class="zk-btn btnStyle_02"></a> -->
            </p>
			
			{{if $discount.telephone}}
            <p class="zk-data-row">
                <span class="col_01">电话</span>
                <span class="col_02">{{$discount.telephone}}</span>
            </p>
           	{{/if}}
			
			{{if $discount.circle_name}}
            <p class="zk-data-row">
                <span class="col_01">商圈</span>
                <span class="col_02">{{$discount.circle_name}}</span>
            </p>
            {{/if}}
			
			{{if $discount.market_name}}
            <p class="zk-data-row">
                <span class="col_01">商场</span>
                <span class="col_02">{{$discount.market_name}}</span>
            </p>
            {{/if}}

        </div>

        <!--article-->
        <div class="art-wrap">
			{{if $discount.wap_content}}
            <p> {{$discount.wap_content}} </p>
            {{/if}}
			{{if $discount.wap_img|@count neq 0 }}
            <div class="art-img">
            	{{foreach from=$discount.wap_img key=key item=item}}               
                    <img src="{{$item.img_url}}" />
                {{/foreach}}
             </div>
			{{/if}}
        </div>


    </div>

</body>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</html>
