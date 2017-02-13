<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' brand=$brandRow.brand_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/list.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/focus.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
	{{if $coupon.ticket_id}}
	$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
	{
		action: 'GetOneProduct',
		activityid: '{{$coupon.ticket_uuid}}'
	},
	function (result) {
		if(result.status == 0) {
			return false;
		}
		var jsonList = eval(result.data);
		var activities = eval(result.data.Avtivities);
		$('#surplusTotal').html(parseInt(activities[0]["ProductNum"])); // 总数
		$('#surplusHadSold').html(parseInt(activities[0]["ProductDisplaySale"])); // 售出
		$('#surplusHadLeft').html(parseInt(activities[0]["ProductStock"])); // 剩余
    });	
	{{/if}}	
});
</script>
</head>
<body>
	<div class="w1210">
		{{include file='top.php'}}
    </div>
      <!--nav-->
		{{include file='nav.php'}}

	<!--品牌关注-->
      <div class="brand-details-top w1210">
      		<div class="brand-details-top-logo">
            	<img src="{{$brandRow.brand_logo}}" width="169" height="68">
            </div>
            <div class="brand-details-top-follow">
            	<a href="javascript:FavoriteBrand({{$brand_id}})" class="{{if $brandRow.follow}}brand-details-top-follow-btn-off{{else}}brand-details-top-follow-btn{{/if}}">关注</a>
                <span class="brand-details-top-follow-number"><q id="num">{{$brandRow.favorite_num}}</q>人<font>关注</font></span>
            </div>
			<img src="/images/brand-details-top-pic.jpg"  width="1200" height="96">
       
        <!--品牌故事-->
        <div class="brand-details-row">
            <h3><a class="t_1">品牌故事</a></h3>
            <div class="brand-story-l">
            	<h4 class="brand-story-l-name">品牌名</h4>
                <h5 class="brand-story-l-article-title">{{$brandRow.brand_name}}</h5>
                <p class="brand-story-l-article">{{$brandRow.brand_profile}}</p>
            </div>
             <a class="brand-story-r">
             	<img height="300" width="950" src="{{$brandRow.brand_figure}}">
             </a>
         </div> 
  		<!--品牌优惠-->
  		{{if $coupon.ticket_id}}
         <div class="brand-details-row">
            <h3><a class="t_2">品牌优惠券</a></h3>
            <div class="brand-ticket-l">
            	<div class="brand-ticket-l-buy">
                	<p class="brand-ticket-prize">￥<span>{{$coupon.selling_price}}</span></p>
                    <p class="brand-ticket-old-prize">￥<span>{{$coupon.par_value}}</span></p>
                    <p class="sell-number">已售：<font id="surplusHadSold"></font>张</p>
                    <p class="surplus-number">剩余：<font id="surplusHadLeft"></font>张</p>
                    <a class="brand-ticket-l-buy-btn b-coupon" data-tid="{{$coupon.ticket_id}}" target="_blank">立即抢购</a>
                </div>
            	<a href="/home/ticket/show/tid/{{$coupon.ticket_id}}" class="brand-ticket-l-pic"><img height="300" width="635" src="{{$coupon.cover_img}}"></a>
            </div>

			<div class="brand-ticket-r">
            	<h4 class="brand-ticket-r-title"><font>{{$coupon.ticket_title}}</font></h4>
               	<div class="brand-ticket-r-shortTxt">{{$coupon.ticket_summary}}</div>
                <div class="brand-ticket-r-row"><span class="brand-ticket-r-row-name">使用时间：</span><p >{{$coupon.valid_stime|date_format:"%Y.%m.%d"}}-{{$coupon.valid_etime|date_format:"%m.%d"}}</p></div>
                <div class="brand-ticket-r-row"><span class="brand-ticket-r-row-name">使用店铺：</span>
                {{if $coupon.used_shop}}
                	{{foreach from=$coupon.used_shop key=key item=item}}
                		<p >{{$item.shop_name}}</p>
                	{{/foreach}}
                {{/if}}
                </div>
                <a href="/home/ticket/show/tid/{{$coupon.ticket_id}}" class="look-allstore">查看所有使用店铺>></a>
            </div>

         </div> 
         {{/if}}
         
         <!--旗下店铺-->
         {{if $shop}}
         <div class="brand-details-row">
            <h3><a class="t_3">旗下店铺</a></h3>
            <div class="store-list">
                {{foreach from=$shop key=key item=item}}
                <div class="store-col">
                	<p class="store-col-title">{{$item.shop_name}}</p>
                	<p class="store-col-address">地址：{{$item.shop_address}}</p>
                	
                	<div class="store-col-bottom"><a href="/home/shop/show/sid/{{$item.shop_id}}" class="go-store" target="_blank">进入店铺</a></div>
                </div>
                {{/foreach}}
            </div>
        </div>
        {{/if}}
         <!--品牌新品-->
        <div class="brand-details-row">
            <h3><a class="t_4">品牌新品</a></h3>
            <div class="new-product-list" id="waterfall">
			
            </div>
        </div>
        
        
{{include file='bottom.php'}}   
 <!--登陆注册-->
 <div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
<script type="text/javascript">
var bid = {{$brand_id}};
var order = {{$order}};
var site_url = '{{$_CONF.SITE_URL}}';

$(function(){
  	$(".b-coupon").live('click', function() {
		var tid = $(this).attr('data-tid');
		var _this = $(this);		
		$.getJSON(site_url + '/home/ticket/apply-ticket-voucher', { tid:tid }, function(json) {
			switch(json.res) {
				case 100:
					window.location = 'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=' + json.extra.guid;
					break;
				case 99:
					$("#popupLogin").show();
					break;
				default:
					alert(json.msg);
					break;					
			}
		});		
	});
  });
  
function FavoriteBrand(bid){
$.ajax({
	url:'/home/brand/favorite',
	type:'post',
	dataType:'json',
	data:{bid:bid},
	success:function(data){
		if(data.Code == 100){
			$('div.brand-details-top-follow a').removeClass('').addClass('brand-details-top-follow-btn-off');
			$('#num').html(data.Num);
		}else if(data.Code == 200){
			$('#popupLogin').show();
		}
	}
});
}
  
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
            url:"/home/brand/ajax",
            dataType:"json",
            data:{bid:bid,"page":_this.pageNum, "order":order},
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
                    Li = document.createElement('div');
					Li.className = "new-product-col";
                    var _html = '';
					_html+=	'<div class="new-product-col-pic">';
					_html+=	'<a class="new-product-col-l" href="javascript:Concern('+ data['data'][i].good_id  +', \'sort_concern\')" id="sort_concern_'+data['data'][i].good_id+'">喜欢：'+data['data'][i].concerned_number+'</a>';
					_html+=	'<a class="new-product-col-r" href="javascript:Favorite('+ data['data'][i].good_id  +', \'sort_favorite\')" id="sort_favorite_'+data['data'][i].good_id+'">收藏：'+data['data'][i].favorite_number+'</a>';
					_html+=	'<a href="/home/good/show/gid/'+ data['data'][i].good_id +'" target="_blank"><img height="'+data['data'][i].height+'" width="'+data['data'][i].width+'" src="' + data['data'][i].img_url + '"></a>';
					_html+=	'</div>';
					_html+=	'<div class="new-product-col-info">';
					_html+=	'<p><span class="l"><a href="/home/shop/show/sid/'+data['data'][i].shop_id+'" target="_blank">'+data['data'][i].shop_name+'</span> <span class="r">¥ '+data['data'][i].dis_price+'</a></span></p>';
					_html+=	'<p class="last"><a href="/home/good/show/gid/'+ data['data'][i].good_id +'" target="_blank">'+data['data'][i].good_name+'</a></p>';
					_html+=	'</div>';
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

</script>
{{include file='footer.php'}}
