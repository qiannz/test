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
                {{if $_CONF._A eq 'good-edit'}}
                <li><a href="/home/suser/my-good/sid/{{$item.shop_id}}" {{if $sid eq $item.shop_id}}class="on"{{/if}}>{{$item.shop_name}}</a></li>
                {{elseif $_CONF._A eq 'coupon-edit'}}
                <li><a href="/home/suser/coupon-list/sid/{{$item.shop_id}}" {{if $sid eq $item.shop_id}}class="on"{{/if}}>{{$item.shop_name}}</a></li>
                {{else}}
                <li><a href="{{$_CONF.FORM_ACTION}}/sid/{{$item.shop_id}}" {{if $sid eq $item.shop_id}}class="on"{{/if}}>{{$item.shop_name}}</a></li>
                {{/if}}
                
                {{/foreach}}
            </ul>
            {{if $_CONF._A eq 'my-good'}}
             <!--<p class="addShop"><a href="javascript:void(0)" id="addNewShop">新增店铺</a></p>-->
             {{/if}}
        </div>
    </div>       
</div>