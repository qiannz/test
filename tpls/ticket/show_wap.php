<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta' ticket=$ticketRow.ticket_title shop=$shopInfo.shop_name brand=$shopInfo.brand_name}}
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<link href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<link href="/css/wap.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript">
$(function(){
var imglist=document.getElementsByTagName('img'),relist=[],arrsrc=[],iCur=0;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute('data-lazyload'));relist.push(imglist[i])}}
function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"));}
ele.src=Img.src;};for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};

})
</script>
</head>
<body>
	
    <div class="top-con">
    	<div class="wrap">
        	{{if $come_from neq 'ios'}}
			<a href="http://mp.weixin.qq.com/mp/redirect?url=https://itunes.apple.com/cn/app/ming-pin-jie-cheng-shi-gou/id667263278?mt=8" target="_blank"><img data-lazyload='/images/top-downloading.jpg' src="/images/blank.png"></a>
            {{/if}}
			<img data-lazyload="{{if $ticketRow.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$ticketRow.cover_img}}{{else}}/images/default_wap.png{{/if}}" src="{{if $ticketRow.cover_img}}{{$_CONF.IMG_URL}}/buy/cover/{{$ticketRow.cover_img}}{{else}}/images/default_wap.png{{/if}}">
		</div>
	</div>
	
	 <div class="wrap">
		<div class="section {{if $come_from eq 'ios'}}crowdfunding{{/if}}" >
			<!--价格-->
			<div class="top-price">
				<div class="price-txt">
					<div class="time">{{$ticketRow.valid_stime}}-{{$ticketRow.valid_etime}} </div>
                    <div class="now-price"><div><span id="nowPrice">{{$ticketRow.selling_price}}</span></div></div>
                    {{if $ticketRow.ticket_mark eq 'voucher'}}
                        <div class="num">已售/总数<br /><font id="surplusHadSold"></font>/<font id="surplusTotal"></font></div>
                    {{else}}
                        <div class="num">领取/总数<br /><font id="surplusTicket"></font>/<font>{{$ticketRow.total}}</font></div>
                    {{/if}}
					<div class="old-price">¥{{$ticketRow.par_value}}</div>
				</div>
                <script>
                    var _strObj = document.getElementById('nowPrice');
                    var _strLen = _strObj.innerHTML.length;
                    switch (_strLen){
                        case 4:
                            _strObj.style.fontSize = '0.75em';
                            break;
                        case 5:
                            _strObj.style.fontSize = '0.6em';
                            break;
                        case 6:
                            _strObj.style.fontSize = '0.5em';
                            break;
                        case 7:
                            _strObj.style.fontSize = '0.4em';
                            break;
                        default:
                            _strObj.style.fontSize = '0.3em';
                    }

                </script>
			</div>
			<!--内容-->
			<div class="content">
				<!--券名-->
				<div class="coupons-title">
					{{$ticketRow.ticket_title}}
				</div>
				<div class="coupons-rule">
					<div class="coupons-text">
						<p>{{$ticketRow.ticket_summary}}</p>
					</div>
					<img data-lazyload='/images/coupons-text-bg.png' src="/images/blank.png" class="coupons-rule-img">
				</div>
				<a class="look-more" href="/home/ticket/wap-show/tid/{{$tid}}">查看详情<img src="/images/look-more-img.png"></a>
				<!--地址-->
				<div class="address-box">
					<p class="address-title">{{$shopInfo.shop_name}}
					<p class="address-text">{{$shopInfo.shop_address}}</p>
<!--					<p class="address-bottom"><img src="/images/c.png" class="c-img"><img src="/images/a.png" class="a-img"></p>-->
				</div>
			</div>
			<!--购买-->
			<div class="buy-wrap">
				<div class="buy-price"><span>单价</span><font>{{$ticketRow.selling_price}}</font></div>
				<input type="button" class="buy-btn" {{if $ticketRow.ticket_mark eq 'voucher'}}id="buy-btn" value="立即购买"{{else}}value="立即领取"{{/if}}>
			</div>
		</div>
    </div>
</body>
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
$('#buy-btn').bind('click',function(){
	applyVoucher({{$tid}});
})

$(function(){
    {{if $ticketRow.ticket_mark eq 'voucher'}}
    $.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
        {
            action: 'GetOneProduct',
            activityid: '{{$ticketRow.ticket_uuid}}'
        },
        function (result) {
            var jsonList = eval(result.data);
            var activities = eval(result.data.Avtivities);
            $('#surplusTotal').html(parseInt(activities[0]["ProductNum"]));
            $('#surplusHadSold').html(parseInt(activities[0]["ProductDisplaySale"]));
        });
    {{else}}
    $.getJSON('/home/ticket/get-ticket-plus/tid/' + {{$tid}}, {}, function(json){
        if(json.res == 100) {
//            var num = {{$ticketRow.total}} - json.extra;
            $('#surplusTicket').html({{$ticketRow.total}} - json.extra);
        }
    });
    {{/if}}

});
function applyVoucher(tid) {
	var phone = $('#phone').val();
	var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;

	/*	if(!phoneReg.test(phone)) {
	 $.dialog.alert('请输入正确的手机号码');
	 } else {
	 */
	$.getJSON('/home/ticket/apply-ticket-voucher', { tid:tid ,wap:true /*, phone:phone*/}, function(json){
		switch(json.res) {
			case 100:
				$('#voucher').removeClass().addClass('loading').attr('value', '提交中...');
				window.location.href = 'http://superbuy.mplife.com/Wap/Pay/Buy.aspx?i=' + json.extra.guid;
				break;
			case 105:
				$('#surplus').html(json.extra.lave);
				$.dialog.alert(json.msg);
				break;
			default:
				$.dialog.alert(json.msg);
				break;
		}
	});
	/*	}*/
}

setTimeout(function(){
	var cmslog = document.createElement("script");
	cmslog.src = "http://www.mplife.com/tools/cmslog/log.js";
	document.body.appendChild(cmslog);
}, 1000);
</script>
</html>
