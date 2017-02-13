<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
{{insert name='siteMeta'}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="{{$_CONF.SITE_URL}}/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/focus.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
	<div class="w1210">
    	<!--top-->
    	{{include file='top.php'}}
    </div>
      <!--nav-->
     	{{include file='nav.php'}}
        <!--焦点图-->
      <div class="focus">
    	<div class="focus-pic">
        	<ul style="width:999999px" id="focus-list">
            	{{foreach from=$imgLargeList key=key item=item}}
                <li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="1200" height="300"></a></li>
                {{/foreach}}
            </ul>
            <div class="pre-img" id="pre-img"></div>
            <div class="next-img" id="next-img"></div>
        </div>
 	</div>
    
      <div class="w1210">
    <!--热门店铺-->
    <div class="hotShop">
    	<h2 class="hot-title">热门店铺</h2>
        <ul>
        	{{foreach from=$topShopList key=key item=item}}
        	<li>
                <p class="{{if $key mod 2 eq 1}}brand{{else}}brand-on{{/if}}"><a href="{{$item.www_url}}" target="_blank">{{$item.title}}</a></p>
                <p class="shopName"><a href="{{$item.www_url}}" target="_blank">{{$item.summary}}</a></p>
            </li>
            {{/foreach}}
        </ul>
    </div>
    <!--超值精选-->
  <div class="picList">
      <h2 class="title titleLine">超值精选</h2>
      <ul>
      	  {{foreach from=$valuePickList key=key item=item}}
      	  {{if $item.come_from_type eq 1}}
       	  <li>
            <div class="pic">
            	{{if $item.is_auth eq 1}}<div class="accr">认证</div>{{/if}}
                <a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="220" height="300"></a>
             </div>
            <div class="txt">
                <p class="l1">
                    <span class="name"><a href="/home/shop/show/sid/{{$item.shop_id}}" target="_blank" title="{{$item.shop_name}}">{{$item.shop_name}}</a></span>
                    <span class="price">¥<font>{{$item.dis_price}}</font></span>
                </p>
                <p class="l2"><a href="{{$item.www_url}}" target="_blank" title="{{$item.title}}">{{$item.title}}</a></p>
            </div>
            <div class="vote">
            	 <a class="vote-l" href="javascript:Concern({{$item.come_from_id}}, 'chosen_concern')" id="chosen_concern_{{$item.come_from_id}}"><s class="vote-l-icon"></s><q>{{$item.concerned_number}}</q></a>
                 <a class="vote-r" href="javascript:Favorite({{$item.come_from_id}}, 'chosen_favorite')" id="chosen_favorite_{{$item.come_from_id}}"><s class="vote-r-icon"></s><q>{{$item.favorite_number}}</q></a>
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
                <p class="l1">
                	<span class="name"><a href="/home/shop/show/sid/{{$item.shop_id}}" target="_blank" title="{{$item.shop_name}}">{{$item.shop_name}}</a></span>
                    <span class="price">¥<font>{{$item.dis_price}}</font></span>
                </p>
                <p class="l2"><a href="{{$item.www_url}}" target="_blank" title="{{$item.title}}">{{$item.title}}</a></p>
            </div>
          </li>          
          {{/if}}
          {{/foreach}}
      </ul>
    </div>
    <!--分类推荐-->
	{{foreach from=$recommendClassificationGoodList key=key item=item}}
    <!--鞋包.配饰-->
    <div class="picList">
      <h2 class="title titleLine">{{$item.pos_name}}<a href="{{$item.pos_url}}" target="_blank"  class="more">更多>></a></h2>
      {{if $item.child}}
      <ul>
      	   {{foreach from=$item.child key=skey item=sitem}}
      	   {{if $sitem.come_from_type eq 1}}
       	   <li>
            <div class="pic">
            	{{if $sitem.is_auth eq 1}}<div class="accr">认证</div>{{/if}}
                <a href="{{$sitem.www_url}}" target="_blank"><img src="{{$sitem.img_url}}" width="220" height="300"></a>
             </div>
            <div class="txt">
                <p class="l1">
                    <span class="name"><a href="/home/shop/show/sid/{{$sitem.shop_id}}" target="_blank" title="{{$sitem.shop_name}}">{{$sitem.shop_name}}</a></span>
                    <span class="price">¥<font>{{$sitem.dis_price}}</font></span>
                </p>
                <p class="l2"><a href="{{$sitem.www_url}}" target="_blank" title="{{$sitem.title}}">{{$sitem.title}}</a></p>
            </div>
            <div class="vote">
            	 <a class="vote-l" href="javascript:Concern({{$sitem.come_from_id}}, 'sort_concern')" id="sort_concern_{{$sitem.come_from_id}}"><s class="vote-l-icon"></s><q>{{$sitem.concerned_number}}</q></a>
                 <a class="vote-r" href="javascript:Favorite({{$sitem.come_from_id}}, 'sort_favorite')" id="sort_favorite_{{$sitem.come_from_id}}"><s class="vote-r-icon"></s><q>{{$sitem.favorite_number}}</q></a>
            </div>
          </li>
          {{elseif $sitem.come_from_type eq 2}}
		  <li>
      		<a class="pic" href="{{$sitem.www_url}}" target="_blank">
            <div class="mpCoupons">
            		<div class="mpCouponsTit"><span class="t1">MP</span><span  class="t2">{{$sitem.sort_name}}</span></div>
                    <div class="mpCouponsTxt">
                        <p>使用说明：{{$sitem.summary}}</p>
                        <p>有效期：{{$sitem.valid_time}}</p>
                    </div>
            </div>
            </a>
            <div class="txt">
                <p class="l1"><span class="name">{{$sitem.shop_name}}</span><span class="price">¥<font>{{$sitem.dis_price}}</font></span></p>
                <p class="l2"><a href="{{$sitem.www_url}}" target="_blank" title="{{$sitem.title}}">{{$sitem.title}}</a></p>
            </div>          
          </li>          
          {{/if}}
          {{/foreach}}
      </ul>
      {{/if}}
    </div>
    {{/foreach}}
    <!--品牌-->
    <div class="brandLogo" id="brandLogo">
	  <div class="brandLogoBox">
        	<h2><span>女装</span></h2>
        	<p class="center"><img src="images/bl-01.png" width="41" height="57"></p>
            <ul class="first">
            	{{foreach from=$recommendBrandsWomList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
       <div class="brandLogoBox">
        	<h2><span>女鞋</span></h2>
        	<p class="center"><img src="images/bl-02.png" width="41" height="57"></p>
            <ul>
            	{{foreach from=$recommendBrandsShoesList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>内衣</span></h2>
        	<p class="center"><img src="images/bl-03.png" width="41" height="57"></p>
            <ul>
            	{{foreach from=$recommendBrandsUnderwearList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>男装</span></h2>
        	<p class="center"><img src="images/bl-04.png" width="41" height="57"></p>
            <ul>
            	{{foreach from=$recommendBrandsMenList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>配饰</span></h2>
        	<p class="center"><img src="images/bl-05.png" width="41" height="57"></p>
            <ul>
            	{{foreach from=$recommendBrandsAccessoriesList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>母婴</span></h2>
        	<p class="center"><img src="images/bl-06.png" width="41" height="57"></p>
            <ul>
            	{{foreach from=$recommendBrandsMaternalChildList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>床品</span></h2>
        	<p class="center"><img src="images/bl-07.png" width="41" height="57"></p>
            <ul>
            	{{foreach from=$recommendBrandsBeddingList key=key item=item}}
            	<li><a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="100" height="50"></a></li>
                {{/foreach}}
            </ul>
      </div>
    </div>
    <!--关于超级购-->
    {{include file='bottom.php'}}
  </div>
 <!--登陆注册-->
<div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
$(function(){
	FnHover('allBtn','allBox');
	//焦点图
	Mp.Focus({
		ele:'focus-list',
		pre:'pre-img',
		next:'next-img',
		msec:6000 //毫秒
	});
	Broaden({id:'brandLogo',HoverWidth:386});
})

function Concern(gid, type) {
	$.getJSON(site_url + '/home/index/concern/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function Favorite(gid, type) {
	$.getJSON(site_url + '/home/index/favorite/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function getTypeId(gid, type) { return type + '_' + gid;}
</script>
{{include file='footer.php'}}