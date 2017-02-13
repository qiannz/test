<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' good=$goods.good_name shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/ny.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="{{$_CONF.SITE_URL}}/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=BC3eb870cf1e6cca1d46ccab6baad228"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/tabPic.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
	   <div class="w1210">
    	<!--top-->
		{{include file='top.php'}}
       </div>
       <!--nav-->
		{{include file='nav.php'}}
     <!--内页-->
	  <div class="w1210">
      <div class="nyWaper">
      <!--左-->
      		<div class="nyLeft">
            	<h2>{{$goods.good_name}}</h2>
                	<div class="priceBox">
                    	<p class="new">￥<font>{{$goods.dis_price}}</font></p>
                        {{if $goods.org_price gt 0}}<p class="old">原价：<font>￥{{$goods.org_price}}</font></p>{{/if}}
                     </div>
                     <div class="shareBox">
                     <div class="userMesage">
                     	{{$goods.user_name}}/<span>{{$goods.created|date_format:'%Y-%m-%d %H:%M:%S'}}</span>
                     </div>
                    <a class="collectBtn" href="javascript:Concern({{$goods.good_id}}, 'sort_concern')" id="sort_concern_{{$goods.good_id}}">喜欢:<s></s><q>{{$goods.concerned_number}}</q></a>
                    <a class="collectBtn" href="javascript:Favorite({{$goods.good_id}}, 'chosen_favorite')" id="chosen_favorite_{{$goods.good_id}}">收藏:<s></s><q>{{$goods.favorite_number}}</q></a>
                   	<a class="shareBtn" id="shareBtn">一键分享<s></s></a>
                   	<div class="bd_share">
					{{include file='share.php'}}
					</div>
                    </div>
                    {{if $goods.ticket}}
	                    {{foreach from=$goods.ticket key=key item=item}}
	                    <div class="messageBox">
	                    	<p>本商品适用：<font class="font18">{{$item.ticket_title}}</font>
	                    	<a href="/home/ticket/show/tid/{{$item.ticket_id}}" class="applyBtn" target="_blank">立即申领/购买</a></p>
	                    </div>
	                    {{/foreach}}
                    {{/if}}
                    <!--图片切换-->
                     <div style="clear:both"> </div>
       				 <div class="ImgTab">
                    	<div class="bigImg" id="bigImg">
                            <img src="">
                            <a class="bigImgPre" id="bigImgPre"></a>
                            <a class="bigImgNext" id="bigImgNext"></a>
                            <div class="bigImgBg"></div>
 						</div>
                    	<div class="scrollPic">
                        	<a class="pre" id="smallPicPre"></a>
                            <a class="next" id="smallPicNext"></a>
                            <div class="smallPic" id="smallPic">
                            	<ul>
                            		{{foreach from=$goods.img key=key item=item}}
                                    <li {{if $key eq 0}}class="selbg"{{/if}}><img src="{{$item.img_url_small}}" width="90" height="90"></li>
                                    {{/foreach}}
                                </ul>
                   		   </div>
                           <div class="scrollBg" id="scrollBg">
                           		<span class="scrollBtn"></span>
                           </div>
                        </div>
                    </div>
            </div>
            <script type="text/javascript">
				TabPic({
					bigImg:'bigImg',
					bigImgPre:'bigImgPre',
					bigImgNext:'bigImgNext',
					pre:'smallPicPre',
					next:'smallPicNext',
					pisList:'smallPic',
					Scroll:'scrollBg',
					preLink:'{{$goods.preUrl}}',
					nextLink:'{{$goods.nextUrl}}'
				})
			</script>
            <!--右侧-->
			{{include file='ticket/right.php'}}
      </div>
      <!--猜你喜欢-->
    <div class="youLike">
        <h3>猜你喜欢</h3>
            <ul>
            {{foreach from=$goodShowGuessList key=key item=item}}
                <li>
               		 <a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="160" height="210"></a>
                     <p><a href="{{$item.www_url}}" target="_blank">{{$item.title}}</a></p>
                </li>
            {{/foreach}}
        </ul>
    </div>
      <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<script type="text/javascript">
function fnBaiduMap(json){
	var map = new BMap.Map("allmap");            // 创建Map实例
	var point = new BMap.Point(json.x,json.y);    // 创建点坐标
	map.centerAndZoom(point,18);                     // 初始化地图,设置中心点坐标和地图级别。
	map.enableScrollWheelZoom();                            //启用滚轮放大缩小
	map.addOverlay(new BMap.Marker(point));
	map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}));  //右上角，仅包含平移和缩放按钮
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT, type: BMAP_NAVIGATION_CONTROL_PAN}));  //左下角，仅包含平移按钮
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, type: BMAP_NAVIGATION_CONTROL_ZOOM}));  //右下角，仅包含缩放按钮
}

$(function(){
	FnHover('allBtn','allBox');
	FnHover('shareBtn','bdshare');
	fnBaiduMap({
		x:{{$shopInfo.lng}},
		y:{{$shopInfo.lat}}
	})
})
var site_url = '{{$_CONF.SITE_URL}}';

function Concern(gid, type) {
	$.getJSON(site_url + '/home/index/concern/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').removeClass().addClass('likeBtn');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function Favorite(gid, type) {
	$.getJSON(site_url + '/home/index/favorite/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').removeClass().addClass('likeBtn');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}
function getTypeId(gid, type) { return type + '_' + gid;}
</script>
<!--登陆注册-->
<div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
{{include file='footer.php'}}
