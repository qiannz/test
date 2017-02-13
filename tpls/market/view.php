<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' market=$marketRow.market_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
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
})
</script>
</head>
<body>
	<div class="w1210">
		{{include file='top.php'}}
    </div>
      <!--nav-->
		{{include file='nav.php'}}
      
     <div class="w1210">
        <!--商场详情-->
         <div class="brand-details-row">
         	<div class="store-top-l">
            	<a class="store-top-l-logo"><img src="{{$marketRow.logo_img}}" width="120"  height="120"></a>
            	<p class="store-top-l-title">{{$marketRow.market_name}}</p>
                <p class="store-top-l-follow">
                    <a href="javascript:FavoriteMarket({{$market_id}})" class="{{if $marketRow.follow}}brand-details-top-follow-btn-off{{else}}brand-details-top-follow-btn{{/if}}">关注</a>
                    <span class="brand-details-top-follow-number"><q id="num">{{$marketRow.favorite_num}}</q>人关注</span>
                </p>
                <div class="store-top-l-txt">
                    <p>联系电话：{{$marketRow.tel}}</p>
                    <p>地址：{{$marketRow.market_address}}</p> 
                    <p>交通：{{$marketRow.trafficInfo}}</p>
                    <p>介绍：{{$marketRow.intro}}</p>
                    <a href="/home/market/list" class="store-top-l-more">更多>></a>
                </div>
                
			</div>	
             <div class="store-top-r">
             	<img src="{{$marketRow.head_img}}" width="640"  height="400">
             </div>   
         </div>
         
  		<!--优惠-->
         {{if $coupon.ticket_id}}
         <div class="brand-details-row">
            <h3><a class="t_5">商场优惠券</a></h3>
            <div class="brand-ticket-l">
            	<div class="brand-ticket-l-buy">
                	<p class="brand-ticket-prize">￥<span>{{$coupon.selling_price}}</span></p>
                    <p class="brand-ticket-old-prize">￥<span>{{$coupon.par_value}}</span></p>
                    <p class="sell-number">已售：<font id="surplusHadSold"></font>张</p>
                    <p class="surplus-number">剩余：<font id="surplusHadLeft"></font>张</p>
                    <a class="brand-ticket-l-buy-btn b-coupon" data-tid="{{$coupon.ticket_id}}" target="_blank">立即抢购</a>
                </div>
            	<a href="/home/ticket/show/tid/{{$coupon.ticket_id}}" class="brand-ticket-l-pic"><img height="300" width="640" src="{{$coupon.cover_img}}"></a>
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
         <!--商场店铺-->
         {{if $shop}}
          <div class="brand-details-row">
            <h3><a class="t_6">商场店铺</a></h3>
            <div class="market-list">
            	{{foreach from=$shop key=key item=item}}
                <div class="market-col">
					<p class="market-name">{{$item.shop_name}}</p>                
					<p class="market-brand">所属品牌：{{$item.brand_name}}</p>
                    <p class="market-logo"><img height="100" width="100" src="{{$item.brand_icon}}"></p>
                    <a href="/home/shop/show/sid/{{$item.shop_id}}" class="go-market" target="_blank">进入店铺</a>                		
                </div>
                {{/foreach}}
            </div>
          </div>
         {{/if}}
{{include file='bottom.php'}}  
 <!--登陆注册-->
 <div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
<script type="text/javascript">
$(function(){
	var site_url = '{{$_CONF.SITE_URL}}';
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


function FavoriteMarket(mid){
$.ajax({
	url:'/home/market/favorite',
	type:'post',
	dataType:'json',
	data:{mid:mid},
	success:function(data){
		if(data.Code == 100){
			$('p.store-top-l-follow a').removeClass('').addClass('brand-details-top-follow-btn-off');
			$('#num').html(data.Num);
		}else if(data.Code == 200){
			$('#popupLogin').show();
		}
	}
});
}
  
</script>

{{include file='footer.php'}}
