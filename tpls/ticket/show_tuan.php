<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' ticket=$ticketRow.ticket_title shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/ny.css?t={{$_CONF.WEB_VERSION}}1" rel="stylesheet" type="text/css" />
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
      <!--内页-->
      <div class="w1210">
      <div class="nyWaper">
      <!--左-->
      		<div class="nyLeft clearfix">
            
          
            	<div class="item_info_wrap">
                		<!--分享功能模块-->
                     <div class="shareBox">
                     <div class="userMesage">
                     	<a>{{$ticketRow.user_name}} </a>/<span> {{$ticketRow.created|date_format:"%Y-%m-%d %H:%M:%S"}}</span>
                     </div>
                    <a class="shareBtn" id="shareBtn">一键分享<s></s></a>
                    <div class="bd_share">
 						{{include file='share.php'}}
                    </div>
                    </div>
                    
            <!--产品信息-->
     		<div class="item_info">
            	<div class="item_info_left">
                	<div class="item_info_img">
                        <img src="{{$ticketRow.url_small}}" width="240" height="240" />
                    </div>
                </div>
            	<div class="item_info_right">
                
                	<h3 class="item_info_title">{{$ticketRow.ticket_title}}</h3>
                    <div class="item_info_price">
                    	<p class="item_info_price_wrap">
                        	<span class="original-price-type">原价:</span><em class="price-rmb">¥</em><em class="original-price-num">{{$ticketRow.par_value}}</em>
                        	 <span class="present-price-wrap"><em class="present-price-rmb">¥</em><em class="present-price-num">{{$ticketRow.selling_price}}</em></span> 
                        </p>
                    </div>
                    <div class="item_info_bottom clearfix">
                    	<div class="item_info_bottom_left">
                    		<p>总计：<span class="t_num" id="surplusTotal"></span>张</p>
                            <p>已售：<span class="t_num" id="surplusHadSold"></span>张</p>
                       </div> 
                      <div class="item_info_bottom_right">有效期：<span class="item_info_bottom_data">{{$ticketRow.valid_stime|date_format:"%Y.%m.%d"}}日-{{$ticketRow.valid_etime|date_format:"%Y.%m.%d"}}日</span></div>
                    </div>
                </div>
            </div>		
                    
            <!--选中功能模块-->  
              <div class="choice_box">
              		{{foreach from=$ticketRow.sku_choose key=key item=item}}
              		<dl class="choice_type_wrap clearfix" data-element="sku">
                    	<dt class="choice_type"><em>{{$item.PropName}}</em>：</dt>
                        <dd class="clearfix">
                        	<ul class="choice_list_ul clearfix">
                            	{{foreach from=$item.child key=skey item=sitem}}
                            	<li class="choice_list_type" data-props="{{$item.PropId}}:{{$sitem.PropValueId}}">{{$sitem.PropValueName}}</li>
                                {{/foreach}}
                            </ul>
                        </dd>
                    </dl>
                    {{/foreach}}
                    
                    <dl class="choice_type_wrap clearfix">
                    	<dt class="choice_type">数量：</dt>
                        <dd class="clearfix">
                        	<div class="choice_num_box clearfix">
                            	<a class="choice_reduce" id="plus">-</a>
                               <input class="choice_num" type="text" title="请输入购买量" id="num" maxlength="2" value="1">
                                <a  class="choice_Increase" id="add">+</a>
                            </div>
                            <em class="choice_num_box_tip"></em>
                        </dd>
                    </dl>
                    
                    <input class="choice_buy_btn" type="button" value="立即购买"  title="点击此按钮，到下一步确认购买信息" /> 
              </div>
            </div>
    
             <!--券的使用说明--> 
             <div class="instructions_use_wrap">
             	<div class="com_top">
                	<h3>本券使用说明：</h3>
                 </div>
                 <div class="instructions_use_rule clearfix">
                 	{{$ticketRow.content}}
                 </div>
                 
                 
             </div>
           
                        
             <!--使用店铺-->
             <div class="shop_use_wrap">
             	<div class="com_top">
                	<h3>适用店铺：</h3>
                 </div>
                <div class="tb">
                	<table width="700" border="0" align="center">
                      {{foreach from=$shopList key=key item=item}}
                      <tr>
                        <td class="td_left"><a href="/home/shop/show/sid/{{$item.shop_id}}" target="_blank">{{$item.shop_name}}</a></td>
                        <td class="td_center">{{$item.shop_address}}</td>
                        <td class="td_right">{{$item.phone}}</td>
                      </tr>
                      {{/foreach}}
                    </table>
                </div>
             </div>
             
             
             
               <!--消费提示-->
             <!--<div class="consumer_tips_wrap">
             	<div class="com_top">
                	<h3>消费提示：</h3>
                 </div>
                <div class="consumer_tips_list"> 
                 <ul>
                 	<li>有效日期：2014-03-21至2014-11-30  除外日期   国庆节不可用</li>
                    <li>预约信息：无需预约，如遇消费高峰时段您可能需要排队堂食外带</li>
                    <li>本单只适用于堂食，只适用于大厅使用，敬请谅解</li>
                    <li>本单不提供外送外卖服务</li>
                    <li>规则提醒</li>
                 </ul>
                 </div>
                 
             </div>-->
             
             
             <!--推荐商品-->
             {{if $tuanRecommend}}
             <div class="recommended_product_wrap">
             	<div class="com_top">
                	<h3>推荐商品：</h3>
                 </div>
                <div class="rp_list_box"> 
                 <ul class="clearfix">
                 	{{foreach from=$tuanRecommend key=key item=item}}
                 	<li class="rp_list">
                    	<div class="rp_imgw"><a href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank"><img src="{{$item.imgUrl}}" width="160" height="160" /></a></div>
                        <div class="rp_name"><a href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank">{{$item.title}}</a></div>
                    </li>
                    {{/foreach}}
                </ul>
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
<input type="hidden" id="climit" value="{{$ticketRow.climit}}" />
<input type="hidden" id="tuid" value="{{$ticketRow.ticket_uuid}}" />
<input type="hidden" id="userid" value="{{$user.user_id}}" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=BC3eb870cf1e6cca1d46ccab6baad228"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
var skuPrice = {{$ticketRow.skuPrice}};
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
	//fnBaiduMap({x:{{$shopInfo.lng}},y:{{$shopInfo.lat}}});
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
	
	var climit = $("#climit").val();
	$("#add").click(function(){
		var n=$("#num").val();
		var num=parseInt(n)+1;
		if(!/^[1-9][0-9]*$/.test(num)) {
		  $(".choice_num_box_tip").html('请填写正确的数量'); 
		  return;
		};
		
		if(climit && num > climit) {
			ret = false;
			$("#num").val(climit);
			$(".choice_num_box_tip").html('本商品限购：' +  climit + ' 件'); 
			return;
		}
		
		$("#num").val(num);
	});
	
	$("#plus").click(function(){
	  var n=$("#num").val();
	  var num=parseInt(n)-1;
	 	if(!/^[1-9][0-9]*$/.test(num)){$(".choice_num_box_tip").html('请填写正确的数量'); return}
	   $("#num").val(num);
	});
	
	$("#num").keyup(function(){
		if(!/^[1-9][0-9]*$/.test($(this).val())) {
			$(this).val(1);
			$(".choice_num_box_tip").html('请填写正确的数量');;
		}
	});

	function changeTwoDecimal_f(x)  
	{  
		var f_x = parseFloat(x);    
		var f_x = Math.round(x*100)/100;  
		var s_x = f_x.toString();  
		var pos_decimal = s_x.indexOf('.');  
		if (pos_decimal < 0)  
		{  
			pos_decimal = s_x.length;  
			s_x += '.';  
		}  
		while (s_x.length <= pos_decimal + 2)  
		{  
			s_x += '0';  
		}  
		return s_x;  
	}  
	
	$("li.choice_list_type").click(function() {
		$(this).parent().find("li").removeClass("on");
		$(this).addClass("on");
		
		var len = $(".choice_box dl[data-element=sku]").length;
		var i = 0;
		var skuStrCk = '';
		$(".choice_box dl[data-element=sku]").each(function(index, element) {
            var sortName = $(this).find('dt>em').html();
			var skuCk = $(this).find('dd li[class="choice_list_type on"]').length;
			if(skuCk != 0) {
				skuStrCk += $(this).find('dd li[class="choice_list_type on"]').attr("data-props") + ";";
				i++;
			}
        });
		
		if(len == i) {
			skuStrCk = skuStrCk.substr(0, skuStrCk.length - 1);
			$(".present-price-num").html(changeTwoDecimal_f(skuPrice[skuStrCk]['web']));
		}
	});
	
	$(".choice_buy_btn").attr("disabled", false);
	$(".choice_buy_btn").click(function(){
		if(!$("#userid").val()) {
			$("#popupLogin").show();
			return false;
		}
		
		var skuStrCk = "";
		var subSku = true;
		$(".choice_box dl[data-element=sku]").each(function(index, element) {
            var sortName = $(this).find('dt>em').html();
			var skuCk = $(this).find('dd li[class="choice_list_type on"]').length;
			if(skuCk == 0) {
				$.dialog.alert("请选择" +  sortName);
				subSku = false;
			}
			
			skuStrCk += $(this).find('dd li[class="choice_list_type on"]').attr("data-props") + ";";
        });
		
		if(subSku && skuStrCk != "") {
			$(this).attr("disabled", true).val("提交中...");
			var tuid = $("#tuid").val();
			window.location = "http://superbuy.mplife.com/Pay/Order.aspx?ActivityID="+tuid+"&Amount="+$("#num").val()+"&skuid=&sku=" + skuStrCk;
		}
	});
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

fnBaiduMap({x:{{$shopInfo.lng}},y:{{$shopInfo.lat}}});
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