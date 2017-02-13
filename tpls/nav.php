<script type="text/javascript">
function loginEx(){
	var _html="" 
	$.ajax({
		url:"http://passport.mplife.com/tools/userlogin.ashx",
		dataType:"jsonp",
		data:{"act":"loginout","cross":1},
		jsonp:"jsoncallback",
		success:function(data){
			var _data=data[0];
			var _html = '';
			if(_data.result==100){
				_html += '<li><a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}">登陆</a>　/　<a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}">注册</a></li>';
				//_html += '<li class="upfile"><a href="/home/member/join" target="_blank">商户入驻</a></li>';
				_html += '<li class="app"><a href="\" target="_blank">手机APP</a></li>';
				$(".login ul").html(_html);
				loginOut();
			}
		},
		error:function(){
		}
	})
}

function loginOut() {				
	$.ajax({
		url:"{{$_CONF.MPSHOP_SITE_URL}}/home/user/login-out",
		dataType:"jsonp",
		data:{},
		jsonp:"jsoncallback",
		success:function(data){},
		error:function(){}
	})
}

function ShowPopup(type) {
	$.getJSON('/home/shop/is-veriy', {ty:type}, function(json){
		if(json.status == 100) {
			$('body').append('<div id="popup_bus"></div>');
			$('#popup_bus').load('/home/shop/add-veriy/type/' + type);	
		} else if(json.status == 200) {
			location.href = '/home/suser/my-good';
		} else {
			alert(json.msg);
		}
	});	
}
</script>
<div class="nav-wrap">
  <div class="nav">
    <h2 class="allShop" id="allBtn"><a href="/home/good/list" target="_blank">全部商品分类</a><s></s></h2>
    <div class="allList" id="allBox">
        {{foreach from=$navList key=key item=item name=nav}}
        <div class="listBox{{if $smarty.foreach.nav.last}} last{{/if}}">
            <div class="listborder">
                <h3>{{$item.pos_name}}</h3>
                <ul>
                {{foreach from=$item.data item=sitem}}
                    {{if $sitem.child}}
                    <li onMouseOver="ShowDis(this).show()" onMouseOut="ShowDis(this).hide()">
                        <a href="{{$sitem.nav_url}}" target="_blank">{{$sitem.nav_name}}</a>
                        <div class="shopBox">
                            <h3>{{$sitem.nav_name}}</h3>
                            <ul>
                                {{foreach from=$sitem.child item=citem}}
                                <li><a href="{{$citem.nav_url}}" target="_blank">{{$citem.nav_name}}</a></li>
                                {{/foreach}}
                            </ul>
                        </div>
                    </li>
                    {{else}}
                    <li><a href="{{$sitem.nav_url}}" target="_blank">{{$sitem.nav_name}}</a></li>
                    {{/if}}
                {{/foreach}}          
            </ul>
            </div>
        </div>    
        {{/foreach}}         	
     </div>
     <!--导航-->
    <div class="menu">
        <ul>
            <li {{if $_CONF._C eq 'brand'}} class="on" {{/if}} ><a href="/home/brand/list">品牌</a></li> 
            <li {{if $_CONF._C eq 'market'}} class="on" {{/if}} ><a href="/home/market/list">商场</a></li> 
            <li {{if $_CONF._C eq 'ticket'}} class="on" {{/if}} ><a href="/home/ticket/list">优惠券</a><s class="hot"></s></li>
            <!--<li {{if $_CONF._C eq 'buygood'}} class="on" {{/if}} ><a href="/home/buygood/list">团购</a><s class="hot"></s></li>-->
        </ul>
    </div>
    <div class="login">
    {{if $user.user_id}}
        
        <ul >
            <li>{{$user.user_name}}，<a href="javascript:loginEx()">退出</a></li>
            <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx" target="_blank">我的名品街</a></li>
            <li><a href="/home/circle/show">我的商圈</a></li>
            {{if $user.user_type eq 1}}
            <!--<li><a id="getBtn" href="/home/member/join">商户入驻</a></li>-->
            {{else}}
                <li>
                    {{if $user.shopNum}}
                    <a href="/home/suser/my-good" target="_blank">管理店铺</a>
                    {{else}}
                    <!--<a href="/home/member/join">商户入驻</a>-->
                    {{/if}}
                </li>
            {{/if}}
            <li class="upfile"><a href="/home/good/add">上传商品</a></li>
            <li class="app"><a href="http://m.mplife.com/" target="_blank">手机APP</a></li>
        </ul>
    {{else}}
        
        <ul>
            <li>
                <a href="http://passport.mplife.com/login.aspx?sourceurl={{$http_uri}}">登陆</a>　/　
                <a href="http://passport.mplife.com/register.aspx?sourceurl={{$http_uri}}">注册</a>
            </li>
            <!--<li class="upfile"><a href="/home/member/join">商户入驻</a></li>-->
            <li class="app"><a href="http://m.mplife.com" target="_blank">手机APP</a></li>
        </ul>
    {{/if}}
    </div>
  </div>
</div>