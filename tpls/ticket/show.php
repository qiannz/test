<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' ticket=$ticketRow.ticket_title shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/ny.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
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
            	<h2>{{$ticketRow.ticket_title}}</h2>
                	<div class="priceBox">
                    	{{if $ticketRow.ticket_mark eq 'coupon'}}
                        <p class="new">券面值：<font>￥{{$ticketRow.par_value}}</font></p>
                        {{elseif $ticketRow.ticket_mark eq 'voucher'}}
                    	<p class="new">￥<font>{{$ticketRow.selling_price}}</font></p>
                        <p class="old">原价：<font>￥{{$ticketRow.par_value}}</font></p>
                        {{/if}}
                     </div>
                     <div class="shareBox">
                     <div class="userMesage">
                     	{{$ticketRow.user_name}} / <span>{{$ticketRow.created|date_format:"%Y-%m-%d %H:%M:%S"}}</span>
                     </div>
                    <a class="shareBtn" id="shareBtn">一键分享<s></s></a>
                    <div class="bd_share">{{include file='share.php'}}</div>
                    </div>
                    <div class="messageBox">
                    	<p>用券店铺：<font>{{$ticketRow.shop_name}}</font>（店铺地址和地图请见右侧）</p>
                        <p>券有效期：<font>{{$ticketRow.valid_stime|date_format:"%m.%d"}}日-{{$ticketRow.valid_etime|date_format:"%m.%d"}}日</font></p>
                        
                        {{if $ticketRow.ticket_mark eq 'coupon'}}
                        	<p>剩余数量：<font id="surplusTicket">{{math equation="x - y" x=$ticketRow.total y=$ticketRow.has_led}}</font>张</p>
                        {{elseif $ticketRow.ticket_mark eq 'voucher'}}
                        	<p>券总计<font id="surplusTotal"></font>张，已出售<font id="surplusHadSold"></font>张</p>
                        {{/if}}
                        
                        {{if $ticketRow.ticket_mark eq 'coupon'}}
                        <input type="text" class="txt" name="phone" id="phone" value="" placeholder="输入手机号码获取优惠券" />
                        <input type="button" class="btn" value="立即申领" onClick="apply({{$tid}})" id="apply" />
                        {{elseif $ticketRow.ticket_mark eq 'voucher'}}
                        <input type="button" class="btn" value="立即购买" onClick="applyVoucher({{$tid}})" id="voucher" />
                        {{/if}}
                        </p>
                    </div>
                    <h3 class="nyTitle">本券使用说明：</h3>
     				<div class="conTxt">{{$ticketRow.content}}</div>
                    {{if $goodTicketList.data}}
                    <h3 class="nyTitle">本券适用商品：<a href="/home/good/more/tid/{{$tid}}" target="_blank">更多&gt;&gt;</a></h3>
                    <div class="picList">
                        <ul>
                        	{{foreach from=$goodTicketList.data key=key item=item}}
                            <li>
                            	<div class="pic">
                                <div class="nyListPic">
                                	<a href="/home/good/show/gid/{{$item.good_id}}" target="_blank"><img src="{{$item.img_url}}" height="{{$item.height}}" /></a>
                                </div>
                                <a class="m1" href="javascript:Concern({{$item.good_id}}, 'chosen_concern')" id="chosen_concern_{{$item.good_id}}"><s></s><q>{{$item.concerned_number}}</q></a>
                                <a class="m2" href="javascript:Favorite({{$item.good_id}}, 'chosen_favorite')" id="chosen_favorite_{{$item.good_id}}"><s></s><q>{{$item.favorite_number}}</q></a>
                                </div>
                                <div class="txt">
                                    <p class="l1"><span class="name"><a href="/home/shop/show/sid/{{$item.shop_id}}" target="_blank">{{$item.shop_name}}</a></span><span class="price">¥<font>{{$item.dis_price|string_format:"%d"}}</font></span></p>
                                    <p class="l2"><a href="/home/good/show/gid/{{$item.good_id}}" target="_blank">{{$item.good_name}}</a></p>
                                </div>
                            </li>
                            {{/foreach}}                      
                        </ul>
                        <div class="moreLink">
                        	<a href="/home/good/more/tid/{{$tid}}" target="_blank">更多适用商品<font>&gt;&gt;</font></a>
                        </div>
                    </div>
                    {{/if}}
            </div>
            <!--右侧-->
            {{include file='ticket/right.php'}}
      </div>
          <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=BC3eb870cf1e6cca1d46ccab6baad228"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
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
	});
	
	{{if $ticketRow.ticket_mark eq 'voucher'}}
	$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
	{
		action: 'GetOneProduct',
		activityid: '{{$ticketRow.ticket_uuid}}'
	},
	function (result) {
		if(result.status == 0) {
			return false;
		}
		var jsonList = eval(result.data);
		var activities = eval(result.data.Avtivities);
		$('#surplusTotal').html(parseInt(activities[0]["ProductNum"]));
		$('#surplusHadSold').html(parseInt(activities[0]["ProductDisplaySale"]));
    });	
	{{else}}
	$.getJSON('/home/ticket/get-ticket-plus/tid/' + {{$tid}}, {}, function(json){
		if(json.res == 100) {
			$('#surplusTicket').html(json.extra);
		}
	});	
	{{/if}}
});

function Concern(gid, type) {
	$.getJSON('/home/index/concern/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function Favorite(gid, type) {
	$.getJSON('/home/index/favorite/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function getTypeId(gid, type) { return type + '_' + gid;}

function apply(tid) {
	var phone = $('#phone').val();
	var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
	
	if(!phoneReg.test(phone)) {
		$.dialog.alert('请输入正确的手机号码');
	} else {
		$('#apply').removeClass().addClass('loading').attr('value', '申请中...');
		$.getJSON('/home/ticket/apply-ticket', { tid:tid, phone:phone}, function(json){
			switch(json.res) {
				case 100:
				case 105:
					$('#surplus').html(json.extra.lave);
					$.dialog.alert(json.msg);
					break;
				default:
					$.dialog.alert(json.msg);
					break;
			}
			setTimeout(function(){$('#apply').removeClass().addClass('btn').attr('value', '立即申领');} , 3000);
		});
	}
}

function applyVoucher(tid) {
	var phone = $('#phone').val();
	var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
	
/*	if(!phoneReg.test(phone)) {
		$.dialog.alert('请输入正确的手机号码');
	} else {
	*/	
		$.getJSON('/home/ticket/apply-ticket-voucher', { tid:tid/*, phone:phone*/}, function(json){
			switch(json.res) {
				case 100:
					$('#voucher').removeClass().addClass('loading').attr('value', '提交中...');
					window.location.href = 'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=' + json.extra.guid;
					break;
				case 105:
					$('#surplus').html(json.extra.lave);
					$.dialog.alert(json.msg);
					break;
				case 99:
					$("#popupLogin").show();
					break;
				default:
					$.dialog.alert(json.msg);
					break;
			}
		});
/*	}*/	
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