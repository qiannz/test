<?php /* Smarty version 2.6.27, created on 2016-12-01 16:09:39
         compiled from nav.php */ ?>
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
				_html += '<li><a href="http://passport.mplife.com/login.aspx?sourceurl=<?php echo $this->_tpl_vars['http_uri']; ?>
">登陆</a>　/　<a href="http://passport.mplife.com/register.aspx?sourceurl=<?php echo $this->_tpl_vars['http_uri']; ?>
">注册</a></li>';
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
		url:"<?php echo $this->_tpl_vars['_CONF']['MPSHOP_SITE_URL']; ?>
/home/user/login-out",
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
        <?php $_from = $this->_tpl_vars['navList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['nav'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
        $this->_foreach['nav']['iteration']++;
?>
        <div class="listBox<?php if (($this->_foreach['nav']['iteration'] == $this->_foreach['nav']['total'])): ?> last<?php endif; ?>">
            <div class="listborder">
                <h3><?php echo $this->_tpl_vars['item']['pos_name']; ?>
</h3>
                <ul>
                <?php $_from = $this->_tpl_vars['item']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sitem']):
?>
                    <?php if ($this->_tpl_vars['sitem']['child']): ?>
                    <li onMouseOver="ShowDis(this).show()" onMouseOut="ShowDis(this).hide()">
                        <a href="<?php echo $this->_tpl_vars['sitem']['nav_url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['sitem']['nav_name']; ?>
</a>
                        <div class="shopBox">
                            <h3><?php echo $this->_tpl_vars['sitem']['nav_name']; ?>
</h3>
                            <ul>
                                <?php $_from = $this->_tpl_vars['sitem']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['citem']):
?>
                                <li><a href="<?php echo $this->_tpl_vars['citem']['nav_url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['citem']['nav_name']; ?>
</a></li>
                                <?php endforeach; endif; unset($_from); ?>
                            </ul>
                        </div>
                    </li>
                    <?php else: ?>
                    <li><a href="<?php echo $this->_tpl_vars['sitem']['nav_url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['sitem']['nav_name']; ?>
</a></li>
                    <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>          
            </ul>
            </div>
        </div>    
        <?php endforeach; endif; unset($_from); ?>         	
     </div>
     <!--导航-->
    <div class="menu">
        <ul>
            <li <?php if ($this->_tpl_vars['_CONF']['_C'] == 'brand'): ?> class="on" <?php endif; ?> ><a href="/home/brand/list">品牌</a></li> 
            <li <?php if ($this->_tpl_vars['_CONF']['_C'] == 'market'): ?> class="on" <?php endif; ?> ><a href="/home/market/list">商场</a></li> 
            <li <?php if ($this->_tpl_vars['_CONF']['_C'] == 'ticket'): ?> class="on" <?php endif; ?> ><a href="/home/ticket/list">优惠券</a><s class="hot"></s></li>
            <!--<li <?php if ($this->_tpl_vars['_CONF']['_C'] == 'buygood'): ?> class="on" <?php endif; ?> ><a href="/home/buygood/list">团购</a><s class="hot"></s></li>-->
        </ul>
    </div>
    <div class="login">
    <?php if ($this->_tpl_vars['user']['user_id']): ?>
        
        <ul >
            <li><?php echo $this->_tpl_vars['user']['user_name']; ?>
，<a href="javascript:loginEx()">退出</a></li>
            <li><a href="<?php echo $this->_tpl_vars['_CONF']['MAIN_SITE_URL']; ?>
/O2oNewbuy/commoditylist.aspx" target="_blank">我的名品街</a></li>
            <li><a href="/home/circle/show">我的商圈</a></li>
            <?php if ($this->_tpl_vars['user']['user_type'] == 1): ?>
            <!--<li><a id="getBtn" href="/home/member/join">商户入驻</a></li>-->
            <?php else: ?>
                <li>
                    <?php if ($this->_tpl_vars['user']['shopNum']): ?>
                    <a href="/home/suser/my-good" target="_blank">管理店铺</a>
                    <?php else: ?>
                    <!--<a href="/home/member/join">商户入驻</a>-->
                    <?php endif; ?>
                </li>
            <?php endif; ?>
            <li class="upfile"><a href="/home/good/add">上传商品</a></li>
            <li class="app"><a href="http://m.mplife.com/" target="_blank">手机APP</a></li>
        </ul>
    <?php else: ?>
        
        <ul>
            <li>
                <a href="http://passport.mplife.com/login.aspx?sourceurl=<?php echo $this->_tpl_vars['http_uri']; ?>
">登陆</a>　/　
                <a href="http://passport.mplife.com/register.aspx?sourceurl=<?php echo $this->_tpl_vars['http_uri']; ?>
">注册</a>
            </li>
            <!--<li class="upfile"><a href="/home/member/join">商户入驻</a></li>-->
            <li class="app"><a href="http://m.mplife.com" target="_blank">手机APP</a></li>
        </ul>
    <?php endif; ?>
    </div>
  </div>
</div>