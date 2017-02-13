<?php /* Smarty version 2.6.27, created on 2016-02-19 09:38:21
         compiled from market/view.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'siteMeta', 'market/view.php', 5, false),array('modifier', 'date_format', 'market/view.php', 87, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'siteMeta', 'market' => $this->_tpl_vars['marketRow']['market_name'])), $this); ?>

<link href="/css/reset.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/list.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/index.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/focus.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');

	<?php if ($this->_tpl_vars['coupon']['ticket_id']): ?>
	$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
	{
		action: 'GetOneProduct',
		activityid: '<?php echo $this->_tpl_vars['coupon']['ticket_uuid']; ?>
'
	},
	function (result) {
		if(result.status == 0) {
			return false;
		}
		var jsonList = eval(result.data);
		var activities = eval(result.data.Avtivities);
		$('#surplusTotal').html(parseInt(activities[0]["ProductNum"])); // 总数
		$('#surplusHadSold').html(parseInt(activities[0]["ProductDisplaySale"])); // 售出
		$('#surplusHadLeft').html(parseInt(activities[0]["ProductStock"])); // 剩余
    });	
	<?php endif; ?>
})
</script>
</head>
<body>
	<div class="w1210">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>
      <!--nav-->
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'nav.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      
     <div class="w1210">
        <!--商场详情-->
         <div class="brand-details-row">
         	<div class="store-top-l">
            	<a class="store-top-l-logo"><img src="<?php echo $this->_tpl_vars['marketRow']['logo_img']; ?>
" width="120"  height="120"></a>
            	<p class="store-top-l-title"><?php echo $this->_tpl_vars['marketRow']['market_name']; ?>
</p>
                <p class="store-top-l-follow">
                    <a href="javascript:FavoriteMarket(<?php echo $this->_tpl_vars['market_id']; ?>
)" class="<?php if ($this->_tpl_vars['marketRow']['follow']): ?>brand-details-top-follow-btn-off<?php else: ?>brand-details-top-follow-btn<?php endif; ?>">关注</a>
                    <span class="brand-details-top-follow-number"><q id="num"><?php echo $this->_tpl_vars['marketRow']['favorite_num']; ?>
</q>人关注</span>
                </p>
                <div class="store-top-l-txt">
                    <p>联系电话：<?php echo $this->_tpl_vars['marketRow']['tel']; ?>
</p>
                    <p>地址：<?php echo $this->_tpl_vars['marketRow']['market_address']; ?>
</p> 
                    <p>交通：<?php echo $this->_tpl_vars['marketRow']['trafficInfo']; ?>
</p>
                    <p>介绍：<?php echo $this->_tpl_vars['marketRow']['intro']; ?>
</p>
                    <a href="/home/market/list" class="store-top-l-more">更多>></a>
                </div>
                
			</div>	
             <div class="store-top-r">
             	<img src="<?php echo $this->_tpl_vars['marketRow']['head_img']; ?>
" width="640"  height="400">
             </div>   
         </div>
         
  		<!--优惠-->
         <?php if ($this->_tpl_vars['coupon']['ticket_id']): ?>
         <div class="brand-details-row">
            <h3><a class="t_5">商场优惠券</a></h3>
            <div class="brand-ticket-l">
            	<div class="brand-ticket-l-buy">
                	<p class="brand-ticket-prize">￥<span><?php echo $this->_tpl_vars['coupon']['selling_price']; ?>
</span></p>
                    <p class="brand-ticket-old-prize">￥<span><?php echo $this->_tpl_vars['coupon']['par_value']; ?>
</span></p>
                    <p class="sell-number">已售：<font id="surplusHadSold"></font>张</p>
                    <p class="surplus-number">剩余：<font id="surplusHadLeft"></font>张</p>
                    <a class="brand-ticket-l-buy-btn b-coupon" data-tid="<?php echo $this->_tpl_vars['coupon']['ticket_id']; ?>
" target="_blank">立即抢购</a>
                </div>
            	<a href="/home/ticket/show/tid/<?php echo $this->_tpl_vars['coupon']['ticket_id']; ?>
" class="brand-ticket-l-pic"><img height="300" width="640" src="<?php echo $this->_tpl_vars['coupon']['cover_img']; ?>
"></a>
            </div>

			<div class="brand-ticket-r">
            	<h4 class="brand-ticket-r-title"><font><?php echo $this->_tpl_vars['coupon']['ticket_title']; ?>
</font></h4>
               	<div class="brand-ticket-r-shortTxt"><?php echo $this->_tpl_vars['coupon']['ticket_summary']; ?>
</div>
                <div class="brand-ticket-r-row"><span class="brand-ticket-r-row-name">使用时间：</span><p ><?php echo ((is_array($_tmp=$this->_tpl_vars['coupon']['valid_stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y.%m.%d") : smarty_modifier_date_format($_tmp, "%Y.%m.%d")); ?>
-<?php echo ((is_array($_tmp=$this->_tpl_vars['coupon']['valid_etime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%m.%d") : smarty_modifier_date_format($_tmp, "%m.%d")); ?>
</p></div>
                <div class="brand-ticket-r-row"><span class="brand-ticket-r-row-name">使用店铺：</span>                
                <?php if ($this->_tpl_vars['coupon']['used_shop']): ?>
                	<?php $_from = $this->_tpl_vars['coupon']['used_shop']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                		<p ><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</p>
                	<?php endforeach; endif; unset($_from); ?>
                <?php endif; ?>
                </div>
                <a href="/home/ticket/show/tid/<?php echo $this->_tpl_vars['coupon']['ticket_id']; ?>
" class="look-allstore">查看所有使用店铺>></a>
            </div>
         </div> 
         <?php endif; ?>
         <!--商场店铺-->
         <?php if ($this->_tpl_vars['shop']): ?>
          <div class="brand-details-row">
            <h3><a class="t_6">商场店铺</a></h3>
            <div class="market-list">
            	<?php $_from = $this->_tpl_vars['shop']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <div class="market-col">
					<p class="market-name"><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</p>                
					<p class="market-brand">所属品牌：<?php echo $this->_tpl_vars['item']['brand_name']; ?>
</p>
                    <p class="market-logo"><img height="100" width="100" src="<?php echo $this->_tpl_vars['item']['brand_icon']; ?>
"></p>
                    <a href="/home/shop/show/sid/<?php echo $this->_tpl_vars['item']['shop_id']; ?>
" class="go-market" target="_blank">进入店铺</a>                		
                </div>
                <?php endforeach; endif; unset($_from); ?>
            </div>
          </div>
         <?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>  
 <!--登陆注册-->
 <div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>
<script type="text/javascript">
$(function(){
	var site_url = '<?php echo $this->_tpl_vars['_CONF']['SITE_URL']; ?>
';
  	$(".b-coupon").live('click', function() {
		var tid = $(this).attr('data-tid');
		var _this = $(this);		
		$.getJSON(site_url + '/home/ticket/apply-ticket-voucher', { tid:tid }, function(json) {
			switch(json.res) {
				case 100:
					window.location = 'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=' + json.extra.guid;
					break;
				case 99:
					$("#popupLogin").show();
					break;
				default:
					alert(json.msg);
					break;					
			}
		});		
	});
  });


function FavoriteMarket(mid){
$.ajax({
	url:'/home/market/favorite',
	type:'post',
	dataType:'json',
	data:{mid:mid},
	success:function(data){
		if(data.Code == 100){
			$('p.store-top-l-follow a').removeClass('').addClass('brand-details-top-follow-btn-off');
			$('#num').html(data.Num);
		}else if(data.Code == 200){
			$('#popupLogin').show();
		}
	}
});
}
  
</script>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>