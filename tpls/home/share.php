<!DOCTYPE html>
<html>
<head>
<title>名品街-名品导购网</title>
<meta charset="utf-8">
<meta name="keywords" content="">
<meta name="description" content="">
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body class="like-share-bg">
    	<div class="like-share-wrap-bg">
        	<div class="like-share-wrap">
            	<div class="like-share-img">
                	<span class="like-share-reward">{{$amountAwards}}</span>
                	<img src="/images/like-share-banner-01.jpg"  width="1000" height="240" border="0" usemap="#Map">
                    <map name="Map">
                      <area shape="rect" coords="33,21,311,91" href="http://buy.mplife.com/" target="_blank">
                    </map>
                    <img src="/images/like-share-banner-02.jpg" width="1000" height="240" border="0" usemap="#Map2">
                    <map name="Map2">
                      <area shape="rect" coords="669,184,816,217" href="http://www.mplife.com/help/street/140513/212440209201.shtml" target="_blank">
                    </map>
              </div>
                <div class="like-share-img">
                		<a class="upload-link" href="/home/good/add" target="_blank"></a>
                		<img src="/images/like-share-banner-03.jpg" width="1000" height="145">
                        <img src="/images/like-share-banner-04.jpg" width="1000" height="145">
                        <img src="/images/like-share-banner-05.jpg" width="1000" height="145" border="0" usemap="#Map3">
                        <map name="Map3">
                          <area shape="rect" coords="668,70,810,107" href="http://www.mplife.com/help/street/140513/212440209201.shtml" target="_blank">
                        </map>
              </div>
                <div class="like-share-img">
                	<a class="draw-link" href="/home/user/my-task" target="_blank"></a>
                	<img src="images/like-share-banner-06.jpg" width="1000" height="137" border="0" usemap="#Map4">
                    <map name="Map4">
                      <area shape="rect" coords="242,52,378,77" href="http://www.mplife.com/help/street/140513/212440209201.shtml" target="_blank">
                    </map>
                    <img src="/images/like-share-banner-07.jpg" width="1000" height="137">
                    <img src="/images/like-share-banner-08.jpg" width="1000" height="137">
              </div>
                <div class="like-share-img">
                	<img src="/images/like-share-banner-09.jpg" width="1000" height="129">
                    <img src="/images/like-share-banner-10.jpg" width="1000" height="129">
                    <img src="/images/like-share-banner-11.jpg" width="1000" height="130">
                    <img src="/images/like-share-banner-12.jpg" width="1000" height="129">
                    <img src="/images/like-share-banner-13.jpg" width="1000" height="129">
                </div>
               <!--banner-->
               <div class="like-share-banner">
               		<a href="{{$imageRow.www_url}}" target="_blank"><img src="{{$imageRow.img_url}}" width="1000" height="250"></a>
               </div>
                <!--banner over-->
               <!--图片列表-->
               <div class="like-share-list-wrap">
               		<div class="picList like-share-list">
                    <ul>
                        {{foreach from=$good key=key item=item}}
                        {{if $item.come_from_type eq 1}}
                        <li>
                        	<div class="pic">
                                {{if $item.is_auth eq 1}}<div class="accr">认证</div>{{/if}}
                                <a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="220" height="300"></a>
                            </div>
                            <div class="txt">
                                <p class="l1"><span class="name"><a href="/home/shop/show/sid/{{$item.shop_id}}" title="{{$item.shop_name}}" target="_blank">{{$item.shop_name}}</a></span><span class="price">¥<font>{{$item.dis_price}}</font></span></p>
                                <p class="l2"><a href="{{$item.www_url}}" title="{{$item.title}}" target="_blank">{{$item.title}}</a></p>
                            </div>
                        </li>
                        {{elseif $item.come_from_type eq 2}}
                        <li>
                            <a class="pic" href="{{$item.www_url}}" target="_blank">
                            <div class="mpCoupons">
                                    <div class="mpCouponsTit"><span class="t1">MP</span><span  class="t2">{{$item.sort_name}}</span></div>
                                    <div class="mpCouponsTxt">
                                        <p>使用说明：{{$item.summary}}</p>
                                        <p>有效期：{{$item.valid_time}}</p> 
                                    </div>
                            </div>
                            </a>
                            <div class="txt">
                                <p class="l1"><span class="name">{{$item.shop_name}}</span><span class="price">¥<font>{{$item.dis_price}}</font></span></p>
                                <p class="l2"><a href="{{$item.www_url}}" target="_blank">{{$item.title}}</a></p>
                            </div>
                           </li>
                        {{/if}}
                        {{/foreach}}
                    </ul>
                </div>
               </div>
                <!--图片列表结束-->
                 <div class="like-share-bottom">
                 		<a href="/home/member/join"  target="_blank">商户入驻申请请点击</a>
                        <span>客服电话：021-52519666-8050（9:00-18:00）</span>
                 </div>
            </div>
        </div>
</body>
</html>
