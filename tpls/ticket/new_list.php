<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
{{insert name='siteMeta'}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
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
	<!--优惠券-->
	<div class="coupon-con">
		 <div class="titlePic titlePic_08">优惠券
			<div class="coupon-list-btn">
				<a href="/home/ticket/list/type/1">商场/品牌</a>
				<a href="/home/ticket/list/type/2">特卖</a>
				<a href="/home/ticket/list/type/3">已结束</a>
				<span class="coupon-list-btn-bg" id="coupon-list-btn-bg"></span>
			</div>
		 </div>
		 <div class="coupon-left-list">
            <div id="coupon-left-list_03">
            </div>          
		 	<a class="look-more">点击查看更多</a>
         </div>
         
         
         <!--右边-->
         <div class="coupon-list-right">
            <div class="coupon-right-ad">
          	  <img width="308" height="162" src="/images/blank.png"  data-lazyload="/images/coupon-left-ad.jpg">
            </div>
            <div class="tm-right">
         		
           		<!--MP特卖服务承诺-->
                <div class="tm-service">
                	<h2 class="tm-service-tit">MP特卖服务承诺</h2>
                    <div class="tm-service-list">
                    	<p><span class="tm-service-top-txt">MP特卖只和品牌方合作，保证货品来源正规</span></p>
                        <p><span class="tm-service-txt">更好地服务名品导购网用户，"无条件退款服务"的消费者保障计划</span></p>
                        <p><span class="tm-service-txt">更好地服务名品导购网用户，"无条件退款服务"的消费者保障计划</span></p>
                        <p><span class="tm-service-txt">更好地服务名品导购网用户，"无条件退款服务"的消费者保障计划</span></p>
                    </div>
                </div>	
                <div class="tm-contact">
                <!--MP特卖意见QQ群-->
                	<div class="tm-contact-box"><s class="tm-qq-img"></s>MP特卖意见QQ群<span class="tm-contact-font">210896242</span></div>
                 <!--MP特卖热线-->
                    <div class="tm-contact-box"><s class="tm-tel-img"></s>MP特卖热线<span class="tm-contact-font">52519666-8034</span></div>
                </div>
                <!--节日预告-->
                
           </div>
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
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/focus.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
var type = {{$type}};
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

	//loading img
	var imglist=document.getElementsByTagName("img"),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute("data-lazyload"));relist.push(imglist[i])}}function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"))};if(ie.test(browser)){ele.src=Img.src}}for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};
	
});

	(function(){
			var speed=0,left=0,timer=null;function tMove(obj,iTarget){clearInterval(obj.timer);obj.timer=setInterval(function(){speed+=(iTarget-obj.offsetLeft)/5;speed*=0.7;left+=speed;if(Math.abs(speed)<1&&Math.abs(iTarget-obj.offsetLeft)<1){clearInterval(obj.timer);obj.style.left=iTarget+"px"}else{obj.style.left=left+"px"}},30)};
		var _n = {{$type}} - 1;	
		tMove($('.coupon-list-btn-bg')[0],_n*89);
		$('.coupon-list-btn a').click(function(){
			var _l = $(this).offset().left - $('.coupon-list-btn').offset().left;
			tMove($('.coupon-list-btn-bg')[0],_l);
		})
	})()
			
	var speed=0,left=0,timer=null;function tMove(obj,iTarget){clearInterval(obj.timer);obj.timer=setInterval(function(){speed+=(iTarget-obj.offsetLeft)/5;speed*=0.7;left+=speed;if(Math.abs(speed)<1&&Math.abs(iTarget-obj.offsetLeft)<1){clearInterval(obj.timer);obj.style.left=iTarget+"px"}else{obj.style.left=left+"px"}},30)};


	$('.coupon-list-btn a').click(function(){
		var _l = $(this).offset().left - $('.coupon-list-btn').offset().left;
		tMove($('.coupon-list-btn-bg')[0],_l);
	});

  var bstop = true;
  var couponList = document.getElementById('coupon-left-list_03'), iCur = 1;
  
  $('.look-more').click(function(){
	  	iCur++;
		Loadinglist(iCur, type)
	});
  
	function Loadinglist(page, type){
	$.ajax({
			type:'GET',
			url:"/home/ticket/over-data",
			dataType:"json",
			data:{"type" : type, "page":page},
			success:function(data){
			  var _len = data.length;
			  if(iCur > 1 && !_len) {
			  	$.dialog.alert('没有更多啦');
				return false;
			  }
			  _frag = document.createDocumentFragment();
				
			  for(var i = 0 ;i< _len; i++){
				  if(data[i]){
					var _html = '',
						_ele = document.createElement('div');
						_ele.className = 'coupon-box';   
						_html += '<div class="coupon-box-l">';
						_html += '<a class="coupon-box-logo"><img width="200" height="80" src="'+ data[i].brand_logo +'"></a>';
						_html += '<div class="buy-coupon-box">';
						_html += '<div class="buy-coupon-price">';
						_html += '<div class="buy-coupon-price-l">'+ data[i].dis_price +'</div>';
						_html += '<div class="buy-coupon-price-r">';
						_html += '<p class="buy-coupon-pic">现金券</p>';
						_html += '<p class="old-price">';
						_html += '<img src="/images/buy-coupon-price_03.png" width="21" height="18">';
						_html += '<span>¥'+data[i].par_value+'</span>';
						_html += '</p>';
						_html += '</div>';
						_html += '<a class="buy-coupon-btn" data-tid="'+data[i].ticket_id+'" target="_blank">立即抢购</a>';
						_html += ' <p class="buy-coupon-num">已售出：<span id="hadSold_'+data[i].ticket_id+'">0</span>张&nbsp;&nbsp;&nbsp;&nbsp;剩余：<span id="surplus_'+data[i].ticket_id+'">0</span>张</p> ';
						_html += '</div>';
						_html += '</div>';
						_html += ' <p class="buy-coupon-bottom">'+ data[i].ticket_title +'</p>';
						_html += '</div>';
						_html += ' <div class="buy-coupon-ad">';
						_html += '<p class="buy-coupon-ad-txt">';
						_html += '<span class="buy-coupon-ad-txt-l">有效期：'+ data[i].valid_time +'</span>';
						_html += '<span class="buy-coupon-ad-txt-r"> '+ data[i].shop_name +' </span>';
						_html += '</p>';
						_html += '<p class="buy-coupon-ad-txt-bg"></p>';
						_html += '<a href="/home/ticket/show/tid/'+data[i].ticket_id+'" target="_blank"><img width="640" height="300" src="'+ data[i].cover_img +'"></a>';
						_html += '</div>';
						if(type == 3) {
							_html += '<a class="over_bargain_item" href="/home/ticket/show/tid/'+data[i].ticket_id+'" target="_blank"><b class="gray_layer"></b><span class="icon_over"></span></a>';
						}
						_ele.innerHTML = _html;
						_frag.appendChild(_ele);
						remainingTickets(data[i].ticket_id, data[i].ticket_uuid);
				  }
			  }
			   
			   couponList.appendChild(_frag); 
			},
			error:function(){
			}
		});
	}
  
  $(function(){
  
  	$(".buy-coupon-btn").live('click', function() {
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
					$.dialog.alert(json.msg);
					break;					
			}
		});		
	});
  });
	function applyVoucher(tid) {	
		$.getJSON(site_url + '/home/ticket/apply-ticket-voucher', { tid:tid }, function(json){
			switch(json.res) {
				case 100:
					window.open('http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=' + json.extra.guid, "_blank");
					break;
				case 105:
				default:
					$.dialog.alert(json.msg);
					break;
			}
		});	
	}  
	
	function remainingTickets(tid, tuid) {
		$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
		{
			action: 'GetOneProduct',
			activityid: tuid
		},
		function (result) {
			var jsonList = eval(result.data);
			var activities = eval(result.data.Avtivities);
			$('#surplus_' + tid).html(parseInt(activities[0]["ProductNum"]) - parseInt(activities[0]["ProductDisplaySale"]));
			$('#hadSold_' + tid).html(parseInt(activities[0]["ProductDisplaySale"]));
		});		
	}
	Loadinglist(iCur, type);
</script>
{{include file='footer.php'}}
