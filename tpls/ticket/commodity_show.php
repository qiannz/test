<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>名品导购网</title>
<style type="text/css">
html,body,div,span,h1,h2,h3,h4,h5,h6,p,a,em,img,q,s,small,strong,button,input,select,textarea,b,u,i,dl,dt,dd,ol,ul,li,fieldset,form,label,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,embed,figure,figcaption,footer,header,menu,nav,output,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;}html{font-size:100%;overflow-y:scroll;-webkit-text-size-adjust:none;}ol,ul{list-style:none;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}table{border-collapse:collapse;border-spacing:0;}/* ==|== Clearing Float ====================================================== */.clearfix:before,.clearfix:after{content:"";display:table;}.clearfix:after{clear:both;overflow:hidden;}.clearfix{zoom:1;}/* ==|== Public Style ====================================================== */a,a:visited{text-decoration:none;outline:none;}a:hover,a:active{text-decoration:none;}body,button,input,select,textarea{background:#fff;-webkit-font-smoothing:antialiased;}article,aside,footer,header,menu,nav,section{display:block;}img{max-width:100%;height:auto;vertical-align:top;}.video embed,.video object,.video iframe{width:100%;height:auto;}input[type="button"],input[type="submit"],input[type="radio"]{-webkit-appearance:none;outline:none}
body{
	font-family:"Microsoft YaHei";
	font-size:12px;
	background:#fff;
}

img{ 
	vertical-align:top;

  }
html,body{
   height: 100%;
}



.viewport{
    max-width: 640px;
    min-width: 320px;
    width:100%;
    margin: 0 auto;
    min-height: 100%;
    background: #eee;
}
.login-wrap{
    height:100%;
    background: #3b3663;
}

.login-box{
    padding-top: 65px;
    width:81.25%;
    margin: 0 auto;
}

.login-col{
    display: -webkit-flex;
    display: flex;
    -webkit-align-item:center;
    align-item:center;
    -webkit-box-pack:  justify;
    box-pack:  justify;

    display: -webkit-box;
    display: box;
    box-align:center;
    -webkit-box-align:center;
    -webkit-justify-content:space-between;
    justify-content:space-between;

    border: 1px solid #b6b6b6;

    height:35px;
    border-radius: 18px;
}

.input_50{
    display: block;
    width:50%;
    height: 30px;
    background: none;

    color:#b6b6b6;
    margin-left: 16px;
    outline: none;
    font-size: 14px;
}
.input_100{
    display: block;
    width:100%;
    height: 30px;
    background: none;

    color:#b6b6b6;
    margin-left: 16px;
    outline: none;
    font-size: 14px;
}
.yzm-on,.yzm-off{
    display: block;
    width:40%;
    height: 35px;
    border-radius: 18px;

    text-align: center;
    position: relative;
    top:0;
    right:-1px;

}
.yzm-on{
    background: #b6b6b6;
    color:#3f3c51;
}
.yzm-off{
    background: #ccc;
    color:#808080;
}
.login-error{
    height:30px;
    padding-left: 16px;
    color:#fff;
    font-size: 12px;
}


.login-btn{
    display: block;
    width:100%;

    height:35px;
    border-radius: 18px;

    color: #fff;
    background: #ff4062;
    font-size: 16px;
}

/*商品详情*/
.shop-wrap{

    border:1px solid  #eee;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    padding-bottom: 40px;
}
.shop-top{
    background: #fff;
    position: relative;
    z-index: 1;
    top:0;
    left:0;
    margin-bottom: 35px;
}


.shop-img{
    padding:12px 0 0;
    position: relative;
    z-index: 1;
    text-align: center;
}

.shop-info{
    line-height: 20px;
    color:#333;
    padding: 5px 0;
    word-break:break-all;
    text-align: left;
}

.shop-tag{
    width:45px;
    height: 20px;
    display: inline-block;
    background: #23cdb7;
    color:#fff;
    text-align: center;
    line-height:20px;
    border-radius: 3px;
    vertical-align:top;
    font-size: 12px;

}

.return,.share{
    width:40px;
    height:40px;
    position: absolute;
    top:20px;
    z-index: 10;
}

.return{
    background: url(/images/commodity/return-icon.png) no-repeat;
    background-size: 100%  auto;
    left:5%;
}

.share{
    background: url(/images/commodity/share-icon.png) no-repeat;
    background-size: 100%  auto;
    right:5%;
}

.price-wrap{
    background: #fff;
    border-top:1px solid #efefef ;
    height:40px;

    display: -webkit-flex;
    display: flex;
    -webkit-align-item:center;
    align-item:center;


    display: -webkit-box;
    display: box;
    box-align:center;
    -webkit-box-align:center;

}

.discount{
    display: block;
    width:66px;
    height: 22px;
    background: url(/images/commodity/discount-icon.jpg) no-repeat;
    background-size:contain ;
    color:#fff;
    line-height: 22px;
    font-size: 14px;
    padding-left:10px;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
}
.now-price{
    font-size: 22px;
    color: #ff435e;
    display: block;
    margin-left: 10px;
}

.now-price:before{
    display: inline-block;
    content: '¥';
    font-size: 16px;
}

.old-price{
    font-size: 14px;
    color: #666;
    display: block;
    margin-left: 10px;
    position : relative;
    left: 0;
    top:2px;
    text-decoration: line-through;
}


.old-price:before{
    display: inline-block;
    content: '¥';
}

.address-wrap{
    background: #fff;
    margin-bottom: 5px;
}
.address-top{
    position: relative;
    z-index: 1;
    border-bottom: 1px solid #efefef;
    height:60px;
}

.logo-img{
    width:70px;
    height:70px;
    position: absolute;
    top:-25px;
    left: 10px;
    border-radius: 35px;
    border: 1px solid #eee;
    background-color: #fff;
    background-size:contain ;
}


.shop-name{
    padding-left: 90px;
    color:#333;
    font-size: 18px;
    line-height: 32px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.star-box{
    overflow: hidden;
}

.star-list{
    float: left;
    font-size: 0px;
    padding-left: 90px;
}

.star-list span{
    display: inline-block;
    width:18px;
    height: 18px;
    margin-right:4px;
}
.star-style_01{
    background: url(/images/commodity/star_1.png) no-repeat;
    background-size: contain;
}


.star-style_02{
    background: url(/images/commodity/star_2.png) no-repeat;
    background-size: contain;
}
.star-style_03{
    background: url(/images/commodity/star_3.png) no-repeat;
    background-size: contain;
}

.follow{
    float: left;
    margin-left: 10px;
    border-left: 1px solid #bfbfbf;
    height:18px;
    color: #a8a8a8;
    font-size: 12px;
    line-height: 18px;
    overflow: hidden;
}

.follow:before{
    display: inline-block;
    background: url('/images/commodity/follow-icon.jpg') no-repeat;
    height: 18px;
    width: 18px;
    content: ' ';
    background-size:contain ;
    margin-left: 10px;
    vertical-align: top;
}

.address-col,.tel-col{
    font-size: 14px;
    color:#333;
    line-height: 32px;

}

.address-col:before{
    background: url('/images/commodity/address-icon.png') no-repeat;
    height: 23px;
    width: 23px;
    content: ' ';
    background-size:contain ;
    margin-left: 10px;
    margin-right:5px;
    vertical-align: middle;
    display: inline-block;
}

.tel-col:before{
    background: url('/images/commodity/tel-icon.png') no-repeat;
    height: 23px;
    width: 23px;
    content: ' ';
    background-size:contain ;
    margin-left: 10px;
    margin-right:5px;
    vertical-align: middle;
    display: inline-block;
}

.data-wrap{
    background: #fff;
    margin-bottom: 5px;
}
.data-title{
    font-size: 16px;
    color:#333;
    font-weight: bold;
    padding-top: 5px;
}
.data-title,.data-col{
    padding:0 10px;

}

.data-col{
    font-size: 14px;
    border-bottom: 1px solid #efefef;
    color:#333;
    line-height: 32px;
}
.data-col span{
    color:#666;
    padding-left:0.5em;
}
.data-col:last-child{
    border-bottom: none;
}

.img-list{
    background: #fff;
    padding: 10px;
}
.img-wrap{
    border:1px solid #d2d2d2;
    margin-bottom: 10px;
    padding: 5px;
}
.float-wrap{
    position: fixed;
    width:100%;
    z-index: 999;
    bottom: 0;
    left:0;
}

.float{
    width:100%;
    max-width: 640px;
    min-width: 320px;
    margin: 0 auto;
    background: #fff;
    height: 40px;
    border-top:1px solid #ededed;
    position: relative;
    z-index: 1;
}

.float-l-btn,.float-r-btn{
    position: absolute;
    top:50%;
    width:78px;
    height: 22px;
    margin-top: -11px;
    z-index: 1;
}
.float-l-btn{
    background: url(/images/commodity/float-btn_01.png) no-repeat;
    background-size: contain;
    left:10px;
}
.float-r-btn{
    background: url(/images/commodity/float-btn_03.png) no-repeat;
    background-size: contain;
    right:10px;
}

.float-c-btn{
    width:130px;
    height: 45px;
    display: block;
    margin: 0 auto;
    position: relative;
    top:-10px;
    left:0;
    background: url(/images/commodity/float-btn_02.png) no-repeat;
    background-size: contain;
}
.img-height{
    position: relative;
    z-index: 1;
    width:100%;
}

.img-box{
    background-color: #fff;
    background-position: center center;
    background-repeat: no-repeat;
    height: 100%;
    width: 100%;
    position: absolute;
    top:0;
    left:0;
    z-index: 2;
}
.big-pic{
    position: fixed;
    top:0;
    left:0;
    width:100%;
    height: 100%;
    background: #fff;
    z-index: 99999999;
    padding: 0 10px;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    overflow : hidden;
}
.show-pic{

    height: 100%;
    background-position: center center;
    background-repeat: no-repeat;
	background-size: contain;
}

.btn_01,.btn_02,.btn_03{

    width:95px;
    height:30px;
    position: absolute;
    top:50%;
    right:5px;
    border-radius: 18px;
    margin-top: -18px;
    line-height: 30px;

    font-size:16px;
}


.btn_01{
    background: -moz-linear-gradient(left,  #f82646 0%,  #fc6d41 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left center, right center, color-stop(0%,#f82646), color-stop(100%,#fc6d41)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(left, #f82646 0%,#fc6d41 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(left,  #f82646 0%,#fc6d41 100%); /* Opera 11.10+ */
    background: linear-gradient(to right, #f82646 0%,#fc6d41 100%); /* W3C */
    color:#fff;
    text-align: center;
}



.btn_02{
    background: url('/images/active/oneyuanpurchase/btn_02.png') no-repeat;
    background-size: contain;
    color:#fff;
    padding-left: 32px;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
}

.btn_03{
    background: #929292;
    color:#fff;
    text-align: center;
}

.float-c-btn_01{
	width: 130px;
	height: 45px;
	display: block;
	margin: 0 auto;
	position: relative;
	top: -10px;
	left: 0;
	background: url('/images/active/oneyuanpurchase/btn_02.png') no-repeat;
	background-size: contain;
	color:#fff;
	padding-left: 32px;
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	font-size:16px;
	border-radius:23px;
	border:3px solid #fff;
}

.float-c-btn_02{
	width: 130px;
	height: 45px;
	display: block;
	margin: 0 auto;
	position: relative;
	top: -10px;
	left: 0;
	background: #929292;
	color:#fff;
	font-size:16px;
	border-radius:23px;
	border:3px solid #fff;
	text-align: center;
}
</style>
<link href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css" />
<script src="http://mpimg.cn/jquery-1.10.2.min.js" ></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<body>
    <div class="viewport">
        <div class="shop-wrap">
        <!---->
        <div class="shop-top">
                <div class="shop-img">
                        {{if $mark eq 'commodity'}}
                        <div class="img-height">
                            <div class="img-box" style="background-image: url({{$data.detail.firstImg.img_url}})"></div>
                              <img src="/images/commodity/blank.gif" />
                        </div>
                        {{else}}
                        <div class="img-height">
                              <img src="{{$data.detail.firstImg.img_url}}" />
                        </div>
                        {{/if}}
                        <div class="shop-info">
                            {{if $data.detail.free_shipping eq 1}}<span class="shop-tag">包邮</span>{{/if}}
                            <span>{{$data.detail.ticket_summary}}</span>
                        </div>
                </div>
                <!--标价--->
                <div class="price-wrap">
                    {{if $mark eq 'commodity' || $mark eq 'spike'}}<span class="discount">{{$data.detail.discount}}折</span>{{/if}}
                    <span class="now-price">{{$data.detail.selling_price}}</span>
                    <span class="old-price">{{$data.detail.par_value}}</span>
                </div>
                
                {{if $mark eq 'crowdfunding'}}
                	<div class="shop-info"><a href="http://promo.mplife.com/other/20151103/" style="font-size:15px;color:#ff435e;padding-left:10px; text-decoration: underline;">查看活动规则</a></div>
                {{/if}}
        </div>
        <!----->
        {{if $mark eq 'commodity'}}
        <div class="address-wrap">
            <!---评星级-->
            <div class="address-top">
                    <a class="logo-img" style="background-image: url({{$data.brand.brand_icon}})"></a>
                    <div class="shop-name">{{$data.shop.shop_name}}</div>
                    <div class="star-box">
                        <p class="star-list">
							<span class="star-style_02"></span>
                            <span class="star-style_02"></span>
                            <span class="star-style_02"></span>
                            <span class="star-style_02"></span>
                            <span class="star-style_02"></span>
                        </p>
                        <script type="text/javascript">
						(function(number){
							var star = $('.star-list span');
								star.each(function(i){
									if(i<number){
										$(this).attr('class','star-style_01')
									}
								})
							})({{$data.shop.star}});
							
							$('.img-box').click(function(){
                                var link = $(this).attr('style');
                                var html = '<div class="big-pic"><div class="show-pic" style="'+ link +'"></div></div>';
                              $('body').css({'height':'100%','overflow':'hidden'});
                                $('body').append(html);


                                $('.big-pic').click(function(){
                                    $(this).remove();
                                    $('body').css({'height':'auto','overflow':'auto'});
                                })

                            })
						</script>
                        <a class="follow">
                            {{$data.shop.favorite_number}}
                        </a>
                    </div>
            </div>
            <!---地址电话-->
            <div class="address-col"> {{$data.shop.shop_address}}</div>
            {{if $data.shop.phone}}
            <div class="tel-col"> {{$data.shop.phone}}</div>
            {{/if}}
       </div>
	   {{/if}}
       <!--商品信息-->
       {{if $data.detail.content}}
       <div class="data-wrap">
           <h3 class="data-title">商品信息</h3>
           <div class="data-col">
           		{{$data.detail.content}}
           </div>
        </div>       
       {{else}}
       <div class="data-wrap">
           <h3 class="data-title">商品信息</h3>
           <p class="data-col">{{$data.detail.wap_content}}</p>
        </div>

        <!--图片-->
        <div class="img-list">
        	{{foreach from=$data.detail.imgList item=item}}
            <p class="img-wrap"><img src="{{$item.img_url}}"></p>
            {{/foreach}}
        </div>
		{{/if}}
    </div>
</div>
 <!---浮动层-->
<div class="float-wrap">
    <div class="float">
        <!--<input type="button" class="float-l-btn" value="" onClick="window.location = 'https://itunes.apple.com/cn/app/zui-hua-suan/id667263278?ls=1&mt=8/'">-->
        {{if $data.detail.voucher_status eq 1}}
        	{{if $data.detail.is_notice eq 1}}
            <input type="button" class="float-c-btn_02" value="已提醒" />
            {{else}}
        	<input type="button" data-ticketid="{{$data.detail.ticket_id}}" class="float-c-btn_01" value="提醒我" />
            {{/if}}
        {{else}}
        <input type="button" class="float-c-btn" id="buy-btn" />
        {{/if}}
        <!--<input type="button" class="float-r-btn" value="" onClick="window.location = 'https://itunes.apple.com/cn/app/zui-hua-suan/id667263278?ls=1&mt=8/'">-->
    </div>
 </div>
<script type="text/javascript">
var site_url = '{{$_CONF.SITE_URL}}';
var mark = '{{$mark}}';
$('#buy-btn').bind('click',function(){
	if(mark == 'commodity')  {
		window.location = 'http://superbuy.mplife.com/Wap/Pay/Buy.aspx?i={{$data.detail.ticket_uuid}}';
	} else if( mark == 'crowdfunding' || mark=='spike' ) { //一元购 || 快来抢 
		//applyVoucher({{$tid}});
		window.location = "{{$data.detail.buy_url}}";
	}
})

function applyVoucher(tid) {
	$.getJSON('/home/ticket/apply-ticket-voucher', { tid:tid ,wap:true}, function(json){
		switch(json.res) {
			case 100:
				window.location.href = "{{$data.detail.buy_url}}";
				break;
			case 105:
				$.dialog.alert(json.msg);
				break;
			default:
				$.dialog.alert(json.msg);
				break;
		}
	});
}

$(function(){
	$(".float-c-btn_01").click(function(e){
		var id = this;
		$.ajax({
			type:'POST',
			url:"/active/oneyuanpurchase/notice-me",
			data:{ticket_id:$(this).data("ticketid")},
			dataType:'json',
			success:function(data){
				if( data.res == 101 ){
					//未登录
					window.location.href = "/active/oneyuanpurchase/login?jumpfrom={{$jumpfrom}}";
				}else if( data.res == 103 ){
					alert(data.msg);
				}else if( data.res == 100 ){
					alert(data.msg);
					var num = $(id).parent(".not-begin").find(".color_01").html();
					num = parseInt(num);
					num++
					$(id).parent(".not-begin").find(".color_01").html( num );
					$(id).attr('class', 'float-c-btn_02').val('已提醒');
				}
			}
		});
		e.stopPropagation();
	});
});
</script>
</body>
</html>