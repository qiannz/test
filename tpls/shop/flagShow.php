<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' shop=$coupon.shop_info.shop_name region=$region circle=$circle brand=$coupon.shop_info.brand store=$store}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/list.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="{{$_CONF.SITE_URL}}/favicon.ico" type="image/x-icon" />
</head>
<body>
	<div class="w1210">
    	<!--top-->
    	{{include file='top.php'}}
    </div>
      <!--nav-->
     {{include file='nav.php'}}
       <!--品牌名称-->
      <div class="brand-wrap">
      		<div class="brand-con">
            	{{if $flagRow.brand_logo}}
            	<div class="brand-logo">
                	<img src="{{$flagRow.brand_logo}}"  width="200" height="80">
                </div>
                {{/if}}
                <div class="brand-follow">
                	<p>
                    <a class="{{if $user.user_id && $flagRow.is_favorite}}follow-btn-on{{else}}follow-btn{{/if}}" href="javascript:ShopConcern({{$shop_id}})"></a>
                    <span class="follow-num"><q>{{$flagRow.favorite_num}}</q> 关注</span></p>
                    <p><p class="flagship-font">品牌旗舰店</p></p>
                </div>
                {{if $f eq 1}}
                <a class="brand-btn" href="javascript:flagOpen({{$shop_id}})" id="brand-btn">启 用</a>
                {{/if}}
            </div>
            {{if $flagRow.flagPicRecommend.shopBackground}}
             <div class="brand-bg">
             	<!--<a href="{{$flagRow.flagPicRecommend.shopBackground.detail_url}}" target="_blank" title="{{$flagRow.flagPicRecommend.shopBackground.detail_title}}">-->
            	<img src="{{$_CONF.IMG_URL}}/buy/shop/{{$flagRow.flagPicRecommend.shopBackground.detail_img}}" width="1200" height="120" alt="{{$flagRow.flagPicRecommend.shopBackground.detail_title}}">
                <!--</a>-->
            </div>
            {{/if}}
      </div>
        <!--焦点图-->
      <div class="focus" style="margin-top:1px;">
    	<div class="focus-pic">
        	<ul style="width:999999px" id="focus-list">
            	{{foreach from=$flagRow.flagPicRecommend.shopFigure key=key item=item}}
                <li>
                <a href="{{$item.detail_url}}" target="_blank" title="{{$item.detail_title}}">
                <img src="{{$_CONF.IMG_URL}}/buy/shop/{{$item.detail_img}}" width="1200" height="300" alt="{{$item.detail_title}}">
                </a>
                </li>
                {{/foreach}}
            </ul>
            <div class="pre-img" id="pre-img"></div>
            <div class="next-img" id="next-img"></div>
        </div>
 	</div>
    <!--内容-->
    <div class="w1210">
  	<div class="shop-list-pic">
   		{{foreach from=$flagRow.flagPicRecommend.shopActive key=key item=item}}       
    	<div class="list-pic-box">
        	<a href="{{$item.detail_url}}" target="_blank" title="{{$item.detail_title}}">
        	<img src="{{$_CONF.IMG_URL}}/buy/shop/{{$item.detail_img}}" width="296" height="296" alt="{{$item.detail_title}}">
            </a>
        </div>
        {{/foreach}}
    </div>

    <div class="ad-pic-box">
        <a href="{{$flagRow.flagPicRecommend.shopBar.detail_url}}" target="_blank" title="{{$flagRow.flagPicRecommend.shopBar.detail_title}}">
        <img src="{{$_CONF.IMG_URL}}/buy/shop/{{$flagRow.flagPicRecommend.shopBar.detail_img}}" width="1200" alt="{{$flagRow.flagPicRecommend.shopBar.detail_title}}">
        </a>
    </div>

             
  	<div class="shop-list-pic">
   		{{foreach from=$flagRow.flagPicRecommend.shopGood key=key item=item}}
        
    	<div class="list-pic-box">
        	<a href="{{$item.detail_url}}" target="_blank" title="{{$item.detail_title}}">
        	<img src="{{$_CONF.IMG_URL}}/buy/shop/{{$item.detail_img}}" width="296" height="296" alt="{{$item.detail_title}}">
            </a>
        </div>

        {{/foreach}}
    </div>    

   
    <!--瀑布流-->
    <div class="listWaper">
    <!--瀑布流-->
    <div class="allGoods">
    	 <div class="listTit">
         	<span>{{$coupon.shop_info.shop_name}}(<font id="total_num">6</font>)</span>
         </div>
         <div class="picList waterfallList">
             <ul id="waterfall">                
             </ul>
         </div>
    </div> 
      </div>
     <!--关于超级购-->
    {{include file='bottom.php'}}
  </div>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/focus.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
var sid = {{$shop_id}};
var order = {{$order}};
var site_url = '{{$_CONF.SITE_URL}}';

$(function(){
	FnHover('allBtn','allBox');
	Mp.Focus({
		ele:'focus-list',
		pre:'pre-img',
		next:'next-img',
		msec:3000 //毫秒
	})
});

function Waterfall(json)
{
	if(!(this instanceof Waterfall))
		return new Waterfall(json);
	this.oWaterfall = getId(json.id);
	this.aLiWidth = json.listwidth;
	this.Len = json.colnum;
	this.iMargin = json.m;
	this.widthteam = [];
	this.aLi = [];
	this.bAjax = this.bStop = true;
	this.iNow = this.sumTop = this.i = this.j = this.WaterfallHeight =0;
	this.oP = null;
	this.pageNum = 1;
	this.init();
	this.Scroll();
}	

Waterfall.prototype = {
	init:function()
	{
			for(var i=0;i<this.Len;i++){this.widthteam.push((this.aLiWidth+this.iMargin)*i)}
	},
	del:function(){
		if(this.oP)
			{
				this.oWaterfall.removeChild(this.oP);
				delete this.oP;
			}
	},
	ajax:function()
	{
		var _this = this;
		$.ajax({
		url:"/home/shop/ajax",
		dataType:"json",
		data:{sid:sid,"page":_this.pageNum, "order":order},
		cache: false,
		success:function(data){			
			$('#total_num').html(data['totalNum']);
			if(data['data'].length == 0 && _this.pageNum == 1) {
				_this.oP.innerHTML = '抱歉，当前刷选商品为空！';
			}else{
				_this.del();
			}	
			if(data['totalPage'] < _this.pageNum) {
				return;
			}							
			for(var i=0;i<data['data'].length;i++)
			{
				_this.sumTop = 0;
				Li = document.createElement('li');
				var _html = '';
				_html+= '<div class="pic">';
				_html+= '<a href="/home/good/show/gid/'+ data['data'][i].good_id +'" target="_blank"><img src="' + data['data'][i].img_url + '" width="'+data['data'][i].width+'" height="'+data['data'][i].height+'"></a>';
				_html+= '</div>';
				_html+= '<div class="txt">';
				_html+= ' <p class="l1"><span class="name"><a href="/home/shop/show/sid/'+data['data'][i].shop_id+'" target="_blank">'+data['data'][i].shop_name+'</a></span><span class="price">¥<font>'+data['data'][i].dis_price+'</font></span></p>';
				_html+= ' <p class="l2"><a href="/home/good/show/gid/'+data['data'][i].good_id+'" target="_blank">'+data['data'][i].good_name+'</a></p>';
				_html+= '</div>';
				_html+= '<div class="vote"><a class="vote-l" href="javascript:Concern('+ data['data'][i].good_id  +', \'sort_concern\')" id="sort_concern_'+data['data'][i].good_id+'"><s class="vote-l-icon"></s><q>'+data['data'][i].concerned_number+'</q></a>';
				_html+= '<a class="vote-r" href="javascript:Favorite('+ data['data'][i].good_id  +', \'sort_favorite\')" id="sort_favorite_'+data['data'][i].good_id+'"><s class="vote-r-icon"></s><q>'+data['data'][i].favorite_number+'</q></a>';
				_html+= '</div>';
				Li.innerHTML = _html;
				_this.aLi.push(Li);
				_this.oWaterfall.appendChild(_this.aLi[_this.i]);
				//计算top值
				_this.aLi[_this.i].style.top = (_this.i>_this.Len-1)?(_this.aLi[_this.i-_this.Len].offsetTop+_this.aLi[_this.i-_this.Len].offsetHeight+_this.iMargin+'px'):0;
				//计算left值
				_this.aLi[_this.i].style.left = _this.widthteam[_this.j%_this.Len]+'px';
				_this.sumTop = _this.aLi[_this.i].offsetTop;
				//计算父元素的高度
				if((_this.aLi[_this.i].offsetTop+_this.aLi[_this.i].offsetHeight)>_this.WaterfallHeight)
				{
					_this.WaterfallHeight=_this.aLi[_this.i].offsetTop+_this.aLi[_this.i].offsetHeight;
				}
				if(i==data['data'].length-1)
				{
					_this.pageNum++;
				}
				_this.i++;
				_this.j++;
				}
				_this.oWaterfall.parentNode.style.height = _this.WaterfallHeight+_this.iMargin*2+'px';
				_this.bAjax = true;
			}
		})
	},
	error:function(){
	},
	Scroll:function()
	{
		 var _this = this;
		 this.oP = document.createElement('p');
		 this.oP.className = 'loading';
		 this.oP.innerHTML = '加载中，请稍等';
		 this.oWaterfall.appendChild(this.oP);
		 window.onload = window.onscroll = function(){
				var iClientHeight = document.documentElement.clientHeight;
				var iScroll = document.documentElement.scrollTop || document.body.scrollTop || 0;
				if(_this.sumTop<(iClientHeight+iScroll) && _this.bAjax)
				{	
					_this.bAjax = false;		
					_this.ajax();
				}
		}
	}
}

Waterfall({id:'waterfall',listwidth:220,colnum:5,m:22});

var site_url = '{{$_CONF.SITE_URL}}';

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

function ShopConcern(sid) {
	$.getJSON(site_url + '/home/shop/favorite/sid/' + sid, {}, function(json){
		if(json.Code == 100) {
			$('div.brand-follow a').removeClass('').addClass('follow-btn-on');
			$('div.brand-follow span q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function getTypeId(gid, type) { return type + '_' + gid;}

function ShowPopup(type) {
	$.getJSON('/home/shop/is-veriy', {ty:type, sid:sid}, function(json){
		if(json.status == 100) {
			$('body').append('<div id="popup_bus"></div>');
			$('#popup_bus').load('/home/shop/add-veriy/type/' + type +'/sid/' + sid);
		} else {
			alert(json.msg);
		}
	});
}

function flagOpen(sid) {
	$.dialog(
		{
			title:'提示',
			content:'确定要启用旗舰店模板？',
			follow : document.getElementById('brand-btn'),
			okValue : '确定',
			ok:function(){
				$.ajax({
					url : '/home/shop/flag-open',
					type : 'get',
					data : {sid:sid},
					dataType : 'json',
					success : function(obj) {
						if(obj.res == 100) {
							var linkHref = window.location.href;
							window.location  = linkHref.substring(0, linkHref.length - 4);
						} else {
							$.dialog.alert(obj.msg);
						}
					},
					error : function() {
					}
				});	
			},
			cancelValue : '取消'
		}
	);
}
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