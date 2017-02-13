<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>{{$title}}</title>
<link href="/css/active/oneyuanpurchase/common.css?t={{$version}}" rel="stylesheet" type="text/css">
</head>
<body>

    <div class="viewport">
        <!--header-->
        <div class="header">
        <a href="/active/oneyuanpurchase">
            <p class="header-title">{{$title}}订单</p></a>
        </div>

        <!--order list-->

        <div class="order-list">

            <!--正在进行-->
            {{foreach from=$lottery_list item=lottery_row}}
                <div class="order-box">
                        <div class="order-top">
                            <div class="order-pic">
                                <span class="{{$lottery_row.class}}">{{$lottery_row.status}}</span>
                                <a href="/home/ticket/wap/tid/{{$lottery_row.ticket_id}}"><img src="{{$lottery_row.cover_img}}" /></a>
                            </div>
                           <div class="order-data">
                                <h3> {{$lottery_row.ProductName}}</h3>
                                <p> 订单号：{{$lottery_row.OrderNo}}</p>
                                <p>订单日期：{{$lottery_row.OrderTime}}</p>
                                <p>购买人数：<span class="color_01">{{$lottery_row.OrderCount}}</span>人次</p>
                           </div>
                       </div>
						{{if $lottery_row.OrderCodes|@count gt 0}}
                        <div class="yzm-box">
                        	{{if $lottery_row.OrderCodes|@count gt 1}}
							<span class="down-btn ">展开</span>
							{{/if}}
                            <div class="yzm-list">
                            	{{foreach from=$lottery_row.OrderCodes item=order_row}}
                                <p>验证码：<span class="color_01">{{$order_row.Code}}</span></p>
                                {{/foreach}}
                            </div>
                        </div>
                        {{/if}}
                        {{if $lottery_row.WinningCodes|@count gt 0}}
						<div class="win-number">
                    		中奖号码为：<span class="color_01">{{$lottery_row.WinningCodes[0].Code}}</span>
                		</div>
						{{/if}}
                 </div>
			{{/foreach}}

       </div>



    </div>
    <script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
	<script>
	    $(function(){
	
	      !(function(id){
	
	          var id = id;
	
	          id.each(function(){
	
	             $(this).click(function(){
	                var list = $(this).next();
	
	                 if(list.css('height') == '36px'){
	                     $(this).text('收起');
						 $(this).next().css('height','auto');
	
	                 }else{
	
	                     $(this).text(' 展开');
						  $(this).next().css('height','36px');
	
	                 }
	             })
	
	          });
	
	
	      })($('.down-btn'));
	    })
	 </script>
</body>
</html>
