<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' ticket=$ticketRow.ticket_title shop=$ticketRow.shop_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/list.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
	  <div class="w1210">
      <!--top-->
      {{include file='top.php'}}
      </div>
      <!--nav-->
      {{include file='nav.php'}}
      <!--列表页-->
      <div class="w1210">
      <div class="allGoods">
    	 <div class="listTit">
         	<span><font> {{$ticketRow.ticket_sort}} &ldquo; {{$ticketRow.ticket_title}} &rdquo; </font>共<font id="total_num">  </font>件商品</span>
         </div>
         <div class="picList waterfallList">
             <ul id="waterfall">                
             </ul>
         </div>
    </div>
      <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
var tid = {{$tid}};

$(function(){
	FnHover('allBtn','allBox');
})

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
		url:"/home/good/ajax-by-tid",
		dataType:"json",
		data:{tid:tid,"page":_this.pageNum},
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
<!--登陆注册-->
<div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
{{include file='footer.php'}}