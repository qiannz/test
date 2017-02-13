<div class="site">
    <div class="site-box">
        <h1 class="logo">
            <a href="http://www.mplife.com" target="_blank"><img src="/images/user/20131220-logo.png" width="121" height="55"></a>
        </h1>
        <!--导航-->
        <ul class="nav">
            <li><a href="http://passport.mplife.com/infocenter/infocenter.aspx" target="_blank">我的首页</a></li>
            <li><a href="http://passport.mplife.com/O2oMyOrder/MyOrder.aspx" target="_blank">订单中心</a></li>
            <li id="navIndex" class="hover"><a href="{{$_CONF.SITE_URL}}" target="_blank">名品街</a></li>
        </ul>
        <!--导航end-->
        <div class="pernav">
            <ul>
                <li><a href="http://passport.mplife.com/card/NewCardApply.aspx" target="_blank">会员卡</a></li>
                <li><a href="http://www.mplife.com/help/" target="_blank">帮助</a><span>|</span></li>
                <li id="mlMange" class="per-manage"><a href="http://passport.mplife.com/settings/perManage.aspx" class="manage" target="_blank">账号<b></b></a>
                    <ul class="list">
                        <li><a href="http://passport.mplife.com/settings/perManage.aspx"  target="_blank">资料修改<b class="icon1"></b></a></li>
                        <li><a href="http://passport.mplife.com/settings/perBlacklist.aspx"  target="_blank">隐私设置<b class="icon2"></b></a></li>
                        <li><a href="http://passport.mplife.com/settings/perAttention.aspx"  target="_blank">关注管理<b class="icon4"></b></a></li>
                        <li><a href="http://passport.mplife.com/settings/perShare.aspx"  target="_blank">授权管理<b class="icon5"></b></a></li>
                        <li class="exit"><a href="http://passport.mplife.com/logout.aspx?sourceurl={{$http_uri}}">退出<b class="icon6"></b></a></li>
                    </ul>
                </li>
                <li><a href="http://passport.mplife.com/userinfo/perProfile.aspx" target="_blank">{{$user.user_name}}</a><!--<a href="#">名字需要8个字啊</a>--></li>
            </ul>
        </div>
    </div>
</div>