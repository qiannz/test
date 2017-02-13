<div class="persub">
    <!--个人信息-->
    <div class="perinfo">
        <b class="title"></b>
        <div class="perbox">
            <div class="info"><div class="imgbox">
            <a href="http://passport.mplife.com/settings/perAvatar.aspx" title="修改头像" target="_blank">
            <img src="{{$user.Avatar50}}" alt="" width="60" height="60"></a></div>
            <div class="text">
            <p><a href="http://passport.mplife.com/settings/perManage.aspx" title="资料修改" target="_blank">{{$user.user_name}}</a></p>
            <p> {{$user.GroupTitle}}</p>
            <p>
            <a href="http://passport.mplife.com/settings/perManage.aspx" title="修改性别" target="_blank"><img src="{{if $user.UserSex eq 1}}/images/user/male.png{{else}}/images/user/female.png{{/if}}" alt=""></a>
            <a href="http://passport.mplife.com/settings/perManage.aspx" title="{{$user.CityTitle}}" target="_blank">{{$user.CityTitle}}</a>
            </p>
            </div>
            </div>
            <div class="hot-line">会员热线：021-52519666</div><div class="pertask"></div>
        </div>
    </div>
    <!--个人信息end-->
    <!--后台管理列表-->
    <div class="back-sidebar">
        <div class="sub-nav">
            <ul>
                <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=1" target="_blank">我上传的商品</a></li>
                <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=2" target="_blank">我收藏的商品</a></li>
                <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=3" target="_blank">我喜欢的商品</a></li>
                <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/bclist.aspx" target="_blank">我的商圈</a></li>
                <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oMyOrder/MyMpTicket.aspx" target="_blank">我的优惠卷</a></li>
                <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oMyOrder/MyOrder.aspx" target="_blank">我的订单</a></li>
                <li><a href="/home/user/my-task">街友会</a></li>
            </ul>
        </div>
        <h3 class="link-shopTit">我的店铺</h3>
        <div class="link-shop">
            	<ul>
                	{{foreach from=$shopList key=key item=item}}
                	<li>
                    	<s class="option-style">+</s><a class="show-option-list" data-sid="{{$item.shop_id}}">{{$item.shop_name}}</a>
                        <div class="option-list">
                        	{{if $sid eq $item.shop_id && ($_CONF._A eq 'add' || $_CONF._A eq 'my-good' || $_CONF._A eq 'good-edit')}}
                            <p class="on"><a href="/home/suser/my-good/sid/{{$item.shop_id}}">商品管理</a></p>
                            {{else}}
                            <p><a href="/home/suser/my-good/sid/{{$item.shop_id}}">商品管理</a></p>
                            {{/if}}
                            
<!--                            {{if $user.user_type eq 2}}
                                {{if $sid eq $item.shop_id && ($_CONF._A eq 'coupon-list' || $_CONF._A eq 'add-coupon' || $_CONF._A eq 'coupon-edit' || $_CONF._A eq 'valid')}}
                                <p class="on"><a href="/home/suser/coupon-list/sid/{{$item.shop_id}}">券管理</a></p>
                                {{else}}
                                <p><a href="/home/suser/coupon-list/sid/{{$item.shop_id}}">券管理</a></p>
                                {{/if}}   
                            {{elseif $user.user_type eq 3 && $item.competence && in_array(4, $item.competence)}}
                                {{if $sid eq $item.shop_id && ($_CONF._A eq 'coupon-list' || $_CONF._A eq 'add-coupon' || $_CONF._A eq 'coupon-edit' || $_CONF._A eq 'valid')}}
                                <p class="on"><a href="/home/suser/coupon-list/sid/{{$item.shop_id}}">券管理</a></p>
                                {{else}}
                                <p><a href="/home/suser/coupon-list/sid/{{$item.shop_id}}">券管理</a></p>
                                {{/if}}
                            {{/if}}-->
                            
                            {{if $user.user_type eq 2}}
                            	{{if $sid eq $item.shop_id && ($_CONF._A eq 'buy-good' || $_CONF._A eq 'sold-orders' || $_CONF._A eq 'buy-release' || $_CONF._A eq 'veriy')}}
                            	<p class="on"><a href="/home/suser/buy-good/sid/{{$item.shop_id}}">团购管理</a></p>
                                {{else}}
                                <p><a href="/home/suser/buy-good/sid/{{$item.shop_id}}">团购管理</a></p>
                                {{/if}}
                            {{/if}}
                            
                            
                             {{if $user.user_type eq 2}}
                                {{if $sid eq $item.shop_id && ($_CONF._A eq 'shop-edit' || $_CONF._A eq 'shop-decoration' || $_CONF._A eq 'shop-decoration-add')}}
                                <p class="on"><a href="/home/suser/shop-edit/sid/{{$item.shop_id}}">店铺资料</a></p>
                                {{else}}
                                 <p><a href="/home/suser/shop-edit/sid/{{$item.shop_id}}">店铺资料</a></p>
                                {{/if}}
                            {{elseif $user.user_type eq 3 && $item.competence && in_array(5, $item.competence)}}
                                {{if $sid eq $item.shop_id && ($_CONF._A eq 'shop-edit' || $_CONF._A eq 'shop-decoration' || $_CONF._A eq 'shop-decoration-add')}}
                                <p class="on"><a href="/home/suser/shop-edit/sid/{{$item.shop_id}}">店铺资料</a></p>
                                {{else}}
                                 <p><a href="/home/suser/shop-edit/sid/{{$item.shop_id}}">店铺资料</a></p>
                                {{/if}}
                            {{/if}}
                            
                            {{if $user.user_type eq 2}}
                                {{if $sid eq $item.shop_id && ($_CONF._A eq 'valid-record' || $_CONF._A eq 'my-account')}}
                                <p class="on"><a href="/home/suser/valid-record/sid/{{$item.shop_id}}">店铺账号</a></p>
                                {{else}}
                                <p><a href="/home/suser/valid-record/sid/{{$item.shop_id}}">店铺账号</a></p>
                                {{/if}}
                            {{/if}}
                        </div>
                    </li>
                    {{/foreach}}
                </ul>
            </div>
    </div>       
</div>
<script type="text/javascript">
$(function(){
	
	$('.option-style').next().click(function(){
		$('.option-list').stop().hide();
		$(this).next().stop().show(300);
		$('.option-style').next().removeClass('on');
		$(this).addClass('on');
		$('.option-style').text('+');
		$(this).prev().text('-');
		
	});
		
	(function(a,b){
		var _id = a,
			icount = b;
			$('.show-option-list').each(function(){
				if(Number($(this).attr(_id)) == b){
					$(this).next().show();
					$(this).prev().text('-');
					$(this).addClass('on');
				}
			})
	})('data-sid',{{$sid}})	
});


</script>