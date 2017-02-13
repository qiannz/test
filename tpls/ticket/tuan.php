<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' ticket=$ticketRow.ticket_title shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
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
		<!-- 团购 -->    
		<div class="group-buying-wrap">
        	<!-- 团购分类 -->
            <div class="group-buying-left">
            	
                <div class="group-buying-class">
						<div class="group-buying-select">
                        	<a class="group-buying-select-btn">全部分类</a>
							<s></s>
						</div>
						<div class="group-buying-select-list">
                        	{{foreach from=$storeArray key=key item=item}}
                        	<p><a data-sid="{{$key}}">{{$item}}</a></p>
                            {{/foreach}}
                        </div>
						<div class="group-buying-leftCol">
							{{foreach from=$storeAppArray key=key item=item}}
                            <a data-sid="{{$key}}">{{$item}}</a>
                            {{/foreach}}
						</div>
						
						
						<div class="group-buying-rightCol">
							<a class="on" data-type="time">默认</a>
                            <a data-type="sales">按销量</a>
                            <a data-type="price">按价格</a>
						</div>
                </div>
				
				<div class="group-buying-list" id="group-buying-list"></div>
            	<div class="group-buying-more"><a id="look-more">显示更多</a></div>
            
            </div>
            <!--右边-->
            <div class="group-buying-right">
            	<div class="group-buying-right-top">
                	<img src="/images/group-buying-right-top.png"  width="346" height="134">
                </div>
                <!---热门推荐 -->
                {{if $tuanRecommend}}
                <div class="hot-recommend">
                	<h3 class="hot-recommend-title">热门推荐</h3>
                    {{foreach from=$tuanRecommend key=key item=item}}
                    <div class="hot-recommend-col">
                    	<a class="hot-recommend-pic" href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank"><img src="{{$item.imgUrl}}" /></a>
                        <p class="hot-recommend-col-name"><a href="/home/ticket/show/tid/{{$item.ticket_id}}" target="_blank">{{$item.title}}</a></p>
                        <p class="hot-recommend-col-info"><span class="hot-recommend-col-prize">¥<font>{{$item.selling_price}}</font></span><span class="hot-recommend-col-number"><font id="had_recommend_{{$item.ticket_uuid}}" class="hot-recommend-had" data-uuid="{{$item.ticket_uuid}}"></font>人已买</span></p>
                    </div>
                    {{/foreach}}      
                </div>
                {{/if}}
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
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
	//焦点图
	Mp.Focus({
		ele:'focus-list',
		pre:'pre-img',
		next:'next-img',
		msec:6000 //毫秒
	})
	var data_sid = 0;
	$('.group-buying-select-btn').add($('.group-buying-select-list')).mouseover(function(){
		$('.group-buying-select-list').stop().slideDown(300);	
		$('.group-buying-select s').addClass('on');
	});
	
	$('.group-buying-select-btn').add($('.group-buying-select-list')).mouseout(function(){
		$('.group-buying-select-list').slideUp(100);	
		$('.group-buying-select s').removeClass('on');
	});
	
	$('.group-buying-select-list a').click(function(){
		$('.group-buying-select-btn').text($(this).text());
		$('.group-buying-select-list').slideUp(100);
		$("div.group-buying-leftCol a").removeClass("on");
		$('#group-buying-list').html('<p style="text-align:center;">加载中</p>');
		data_sid = $(this).attr("data-sid");
		Loadinglist(1,data_sid, $("div.group-buying-rightCol a.on").attr("data-type"),$("div.group-buying-rightCol a.on").attr("data-sort"), 1);
	});
	
	$('.group-buying-select-btn').click(function(){
		$('.group-buying-select-btn').text('全部分类');
		$("div.group-buying-leftCol a").removeClass("on");
		Loadinglist(1,0, $("div.group-buying-rightCol a.on").attr("data-type"),$("div.group-buying-rightCol a.on").attr("data-sort"), 1);
	});
	
	$(".hot-recommend-had").each(function(index, element) {
		var tuuid = $(this).attr("data-uuid");
 		$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
		{
			action: 'GetOneProduct',
			activityid: tuuid
		},
		function (result) {
			var jsonList = eval(result.data);
			var activities = eval(result.data.Avtivities);
			$('#had_recommend_' + tuuid).html(parseInt(activities[0]["ProductDisplaySale"]));
		});       
    });	
	//loading img
	var imglist=document.getElementsByTagName("img"),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute("data-lazyload"));relist.push(imglist[i])}}function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"))};if(ie.test(browser)){ele.src=Img.src}}for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};
	
	var couponList = document.getElementById('group-buying-list'), iCur = 1;

	$("div.group-buying-leftCol a").bind("click", function(){
		$('.group-buying-select-btn').text('全部分类');
		$("div.group-buying-leftCol a").removeClass("on");
		$(this).attr('class', 'on');
		data_sid = $(this).attr("data-sid");
		Loadinglist(1, data_sid , $("div.group-buying-rightCol a.on").attr("data-type"),$("div.group-buying-rightCol a.on").attr("data-sort"), 1);
	});
	$("div.group-buying-rightCol a").bind("click", function(){
		$("div.group-buying-rightCol a").removeClass("on")
		$(this).attr('class', 'on');
		var now_data_type = $(this).attr("data-sort");
		now_data_type == 'desc' ? $(this).attr("data-sort", "asc") : $(this).attr("data-sort", "desc");
		Loadinglist(1,$("div.group-buying-leftCol a.on").attr("data-sid"), $(this).attr("data-type"), $(this).attr("data-sort"), 1);
	});
  
  
  
  $('#look-more').click(function(){
	  	iCur++;
		Loadinglist(iCur, data_sid, $("div.group-buying-rightCol a.on").attr("data-type"),$("div.group-buying-rightCol a.on").attr("data-sort"), 0)
	});
  
	function Loadinglist(page, sid, dtype, dsort, cover){
	
	if(cover == 1) {
		$('#group-buying-list').html('<p style="text-align:center;">加载中</p>');	
	}
	
	$.ajax({
			type:'POST',
			url:"/home/buygood/over-data",
			dataType:"json",
			data:{"page":page,"sid" : sid, "dtype" : dtype, "dsort" : dsort},
			success:function(data){
			  var _len = data.length;
			  if(cover == 0) {
				  if(iCur > 1 && !_len) {
					$.dialog.alert('没有更多啦');
					return false;
				  }
			  }
			  _frag = document.createDocumentFragment();
				
			  for(var i = 0 ;i< _len; i++){
				  if(data[i]){
					var _html = '',
						_ele = document.createElement('div');
						if(i % 2 == 0) {
							_ele.className = 'group-buying-col';
						} else {
							_ele.className = 'group-buying-col group-buying-col-right';
						}
						_html += '<a href="/home/ticket/show/tid/'+data[i]['CommonID']+'" target="_blank"><img src="'+data[i]['img_url']+'" width="380" height="240"></a>';
						_html += '<a href="/home/ticket/show/tid/'+data[i]['CommonID']+'" target="_blank" class="group-buying-name">'+ data[i]['ProductName']+'</a>';
						_html += '<a href="/home/ticket/show/tid/'+data[i]['CommonID']+'" target="_blank" class="group-buying-txt">'+ data[i]['Remark'] +'</a>';
						_html += '<div class="group-buying-info">';
						_html += '<span class="group-buying-now">¥<font>'+ data[i]['ProductPrice'] + '</font></span>';
						_html += '<span class="group-buying-old">零售价：<font>￥'+ data[i]['ProductOriginalPrice'] +'</font></span>';
						_html += '<p class="group-buying-number"><font id="hadSold_'+data[i]['ProductID']+'"></font>人已买</p>';
                        _html += '</div>';
						_ele.innerHTML = _html;
						_frag.appendChild(_ele);
						remainingTickets(data[i]['ProductID']);
				  }
			  }
			  if(cover == 1) {
				couponList.innerHTML = '';
			  }
			  couponList.appendChild(_frag);
			},
			error:function(){
			}
		});
	}

	function remainingTickets(uuid) {
		$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
		{
			action: 'GetOneProduct',
			activityid: uuid
		},
		function (result) {
			var jsonList = eval(result.data);
			var activities = eval(result.data.Avtivities);
			$('#hadSold_' + uuid).html(parseInt(activities[0]["ProductDisplaySale"]));
		});		
	}
		
	Loadinglist(1, $("div.group-buying-leftCol a.on").attr("data-sid"), $("div.group-buying-rightCol a.on").attr("data-type"),$("div.group-buying-rightCol a.on").attr("data-sort"));
});

</script>
{{include file='footer.php'}}