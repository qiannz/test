<?php /* Smarty version 2.6.27, created on 2016-12-01 16:09:39
         compiled from home/index20141201.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'siteMeta', 'home/index20141201.php', 5, false),array('function', 'counter', 'home/index20141201.php', 52, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'siteMeta')), $this); ?>

<link href="/css/reset.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<link href="/css/common.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<link href="/css/index.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/index.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/focus.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
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
        <!--焦点图-->
      
    <div class="focus">
    	<div class="focus-pic">
        	<ul style="width:999999px" id="focus-list">
            	<?php $_from = $this->_tpl_vars['imgLargeList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img width="1200" height="300" src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
            <div class="pre-img" id="pre-img"></div>
            <div class="next-img" id="next-img"></div>
        </div>
 	</div>
    <div class="w1210">
    <div class="hotShop">
    	<h2 class="hot-title">热门店铺</h2>
        <ul>
        	<?php $_from = $this->_tpl_vars['topShopList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            <li>
                <p class="<?php if ($this->_tpl_vars['key'] % 2 == 1): ?>brand<?php else: ?>brand-on<?php endif; ?>"><a target="_blank" href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></p>
                <p class="shopName"><a target="_blank" href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
"><?php echo $this->_tpl_vars['item']['summary']; ?>
</a></p>
            </li>
            <?php endforeach; endif; unset($_from); ?>

        </ul>
    </div>
    <!--品牌-->
    <div class="content-list">
    	  <h2 class="titlePic">品牌<a href="/home/brand/all" target="_blank" class="titlePic-more">品牌大全</a></h2>
          <!--left-list-->
          <div class="left-list">
          	<ul>
            	<?php echo smarty_function_counter(array('start' => 0,'skip' => 1,'print' => false), $this);?>

                <?php $_from = $this->_tpl_vars['brandStore']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                	
                	<li><a href="/home/brand/list#<?php echo $this->_tpl_vars['key']; ?>
" id="left-list-btn_<?php echo smarty_function_counter(array(), $this);?>
"><?php echo $this->_tpl_vars['item']; ?>
</a></li>
                <?php endforeach; endif; unset($_from); ?>
                
            </ul>    
          </div>
          <!--small focus-->
          <div class="small-focus">
          		<ul class="small-focus-list" style="width:999999px" id="small-focus-list_01">
          			<?php $_from = $this->_tpl_vars['indexBrandList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                		<li><a href="<?php if ($this->_tpl_vars['item']['come_from_id'] != 0): ?>/home/brand/show/bid/<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['www_url']; ?>
<?php endif; ?>"  target="_blank"><img width="640" height="300" src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></a></li>
                    <?php endforeach; endif; unset($_from); ?> 
                </ul>
                <span class="small-focus-left-btn" id="small-focus-left-btn_01"></span>
                <span class="small-focus-right-btn" id="small-focus-right-btn_01"></span>
          </div>
          <!--right-list-->
          <div class="right-list" id="right-list_01">
          	<ul>
          		<?php $_from = $this->_tpl_vars['indexBrandLogoList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php if ($this->_tpl_vars['item']['come_from_id'] != 0): ?>/home/brand/show/bid/<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['www_url']; ?>
<?php endif; ?>" target="_blank"><img width="110" height="73"  src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
            <span class="hover-link"></span>
          </div>
    </div>
    <!--上传-->
    <div class="content-list">
    	  <h2 class="titlePic titlePic_02">商场<a class="titlePic-more" target="_blank" href="/home/market/list">商场大全</a></h2>
          <!--left-list-->
          <div class="left-list_02">
          	<ul>
          	<?php $_from = $this->_tpl_vars['region']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="/home/market/list#<?php echo $this->_tpl_vars['key']; ?>
" ><?php echo $this->_tpl_vars['item']; ?>
</a></li>
			<?php endforeach; endif; unset($_from); ?>
            </ul>    
          </div>
          <!--small focus-->
          <div class="small-focus">
          		<ul class="small-focus-list" style="width:999999px" id="small-focus-list_02">
          		<?php $_from = $this->_tpl_vars['indexMarketList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                	<li><a href="<?php if ($this->_tpl_vars['item']['come_from_id'] != 0): ?>/home/market/show/mid/<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['www_url']; ?>
<?php endif; ?>" target="_blank"><img width="640" height="300" src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></a></li>
				<?php endforeach; endif; unset($_from); ?>
                </ul>
                <span class="small-focus-left-btn" id="small-focus-left-btn_02"></span>
                <span class="small-focus-right-btn" id="small-focus-right-btn_02"></span>
          </div>
          <!--right-list-->
          <div class="right-list" id="right-list_02">
          	<ul>
          		<?php $_from = $this->_tpl_vars['indexMarketLogoList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php if ($this->_tpl_vars['item']['come_from_id'] != 0): ?>/home/market/show/mid/<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['www_url']; ?>
<?php endif; ?>"  target="_blank"><img width="110" height="73"  src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></a></li>
            	<?php endforeach; endif; unset($_from); ?>
            </ul>
            <span class="hover-link"></span>
          </div>
    </div>
    <!--超值精选-->
  <div class="picList">
       <h2 class="titlePic titlePic_03">超值精选</h2>
      <ul>
     	 <?php $_from = $this->_tpl_vars['valuePickList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
     	 <?php if ($this->_tpl_vars['item']['come_from_type'] == 1): ?>
       	  <li>
            <div class="pic">
            	<?php if ($this->_tpl_vars['item']['is_auth'] == 1): ?><div class="accr">认证</div><?php endif; ?>
                <a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img width="220" height="300" src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></a>
             </div>
            <div class="txt">
                <p class="l1"><span class="name"><a href="/home/shop/show/sid/<?php echo $this->_tpl_vars['item']['shop_id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</a></span><span class="price">¥<font><?php echo $this->_tpl_vars['item']['dis_price']; ?>
</font></span></p>
                <p class="l2"><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></p>
            </div>
            <div class="vote">
            	 <a class="vote-l" href="javascript:Concern(<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
, 'chosen_concern')" id="chosen_concern_<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
"><s class="vote-l-icon"></s><q><?php echo $this->_tpl_vars['item']['concerned_number']; ?>
</q></a>
                 <a class="vote-r" href="javascript:Favorite(<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
, 'chosen_favorite')" id="chosen_favorite_<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
"><s class="vote-r-icon"></s><q><?php echo $this->_tpl_vars['item']['favorite_number']; ?>
</q></a>
            </div>
          </li>
          <?php elseif ($this->_tpl_vars['item']['come_from_type'] == 2): ?>
          <li>
            <a class="pic" href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank">
            <div class="mpCoupons">
            		<div class="mpCouponsTit"><span class="t1">MP</span><span  class="t2"><?php echo $this->_tpl_vars['item']['sort_name']; ?>
</span></div>
                    <div class="mpCouponsTxt">
                        <p>使用说明：<?php echo $this->_tpl_vars['item']['summary']; ?>
</p>
                        <p>有效期：<?php echo $this->_tpl_vars['item']['valid_time']; ?>
</p> 
                    </div>
            </div>
            </a>
            <div class="txt">
                <p class="l1">
                	<span class="name"><a href="/home/shop/show/sid/<?php echo $this->_tpl_vars['item']['shop_id']; ?>
" target="_blank" title="<?php echo $this->_tpl_vars['item']['shop_name']; ?>
"><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</a></span>
                    <span class="price">¥<font><?php echo $this->_tpl_vars['item']['dis_price']; ?>
</font></span>
                </p>
                <p class="l2"><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank" title="<?php echo $this->_tpl_vars['item']['title']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></p>
            </div>
          </li>          
          <?php endif; ?>
          <?php endforeach; endif; unset($_from); ?>
      </ul>
    </div>
    <!--女装热门商品-->
   <?php $_from = $this->_tpl_vars['recommendClassificationGoodList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <div class="picList">
     <h2 class="title titleLine"><?php echo $this->_tpl_vars['item']['pos_name']; ?>
</h2>
     <?php if ($this->_tpl_vars['item']['child']): ?>
      <ul>
	      <?php $_from = $this->_tpl_vars['item']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
	      <?php if ($this->_tpl_vars['sitem']['come_from_type'] == 1): ?>
	       	<li>
	            <div class="pic">
	                <a href="<?php echo $this->_tpl_vars['sitem']['www_url']; ?>
" target="_blank"><img width="220" height="300" src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['sitem']['img_url']; ?>
"></a>
	             </div>
	            <div class="txt">
	                <p class="l1"><span class="name"><a href="/home/shop/show/sid/<?php echo $this->_tpl_vars['sitem']['shop_id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['sitem']['shop_name']; ?>
</a></span><span class="price">¥<font><?php echo $this->_tpl_vars['sitem']['dis_price']; ?>
</font></span></p>
	                <p class="l2"><a href="<?php echo $this->_tpl_vars['sitem']['www_url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['sitem']['title']; ?>
</a></p>
	            </div>
	            <div class="vote">
	            	 <a class="vote-l" href="javascript:Concern(<?php echo $this->_tpl_vars['sitem']['come_from_id']; ?>
, 'sort_concern')" id="sort_concern_<?php echo $this->_tpl_vars['sitem']['come_from_id']; ?>
"><s class="vote-l-icon"></s><q><?php echo $this->_tpl_vars['sitem']['concerned_number']; ?>
</q></a>
	                 <a class="vote-r" href="javascript:Favorite(<?php echo $this->_tpl_vars['sitem']['come_from_id']; ?>
, 'sort_favorite')" id="sort_favorite_<?php echo $this->_tpl_vars['sitem']['come_from_id']; ?>
"><s class="vote-r-icon"></s><q><?php echo $this->_tpl_vars['sitem']['favorite_number']; ?>
</q></a>
	            </div>
	          </li>
	      <?php elseif ($this->_tpl_vars['sitem']['come_from_type'] == 2): ?>
		  <li>
      		<a class="pic" href="<?php echo $this->_tpl_vars['sitem']['www_url']; ?>
" target="_blank">
            <div class="mpCoupons">
            		<div class="mpCouponsTit"><span class="t1">MP</span><span  class="t2"><?php echo $this->_tpl_vars['sitem']['sort_name']; ?>
</span></div>
                    <div class="mpCouponsTxt">
                        <p>使用说明：<?php echo $this->_tpl_vars['sitem']['summary']; ?>
</p>
                        <p>有效期：<?php echo $this->_tpl_vars['sitem']['valid_time']; ?>
</p>
                    </div>
            </div>
            </a>
            <div class="txt">
                <p class="l1"><span class="name"><?php echo $this->_tpl_vars['sitem']['shop_name']; ?>
</span><span class="price">¥<font><?php echo $this->_tpl_vars['sitem']['dis_price']; ?>
</font></span></p>
                <p class="l2"><a href="<?php echo $this->_tpl_vars['sitem']['www_url']; ?>
" target="_blank" title="<?php echo $this->_tpl_vars['sitem']['title']; ?>
"><?php echo $this->_tpl_vars['sitem']['title']; ?>
</a></p>
            </div>          
          </li>          
          <?php endif; ?>
          <?php endforeach; endif; unset($_from); ?>
      </ul>
      <?php endif; ?>
    </div>
    <?php endforeach; endif; unset($_from); ?>
    
	<div id="brandLogo" class="brandLogo">
	  <div class="brandLogoBox">
        	<h2><span>女装</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-01.png"></p>
            <ul class="first">
            	<?php $_from = $this->_tpl_vars['recommendBrandsWomList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
       <div class="brandLogoBox">
        	<h2><span>女鞋</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-02.png"></p>
            <ul>
            	<?php $_from = $this->_tpl_vars['recommendBrandsShoesList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>内衣</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-03.png"></p>
            <ul>
            	<?php $_from = $this->_tpl_vars['recommendBrandsUnderwearList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>男装</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-04.png"></p>
            <ul>
            	<?php $_from = $this->_tpl_vars['recommendBrandsMenList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>配饰</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-05.png"></p>
            <ul>
            	<?php $_from = $this->_tpl_vars['recommendBrandsAccessoriesList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>母婴</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-06.png"></p>
            <ul>
            	<?php $_from = $this->_tpl_vars['recommendBrandsMaternalChildList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
      <div class="brandLogoBox">
        	<h2><span>床品</span></h2>
        	<p class="center"><img width="41" height="57" src="/images/bl-07.png"></p>
            <ul>
            	<?php $_from = $this->_tpl_vars['recommendBrandsBeddingList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            	<li><a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="100" height="50"></a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
      </div>
    </div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

 <!--登陆注册-->
 <div id="popupLogin" style="display:none">
	<div class="getBtnPopup">
    	<h3>登录提示<a class="close" onClick="document.getElementById('popupLogin').style.display = 'none'">&times;</a></h3>
        <p class="loginTxt">名品会员请点击<a href="http://passport.mplife.com/login.aspx?sourceurl=<?php echo $this->_tpl_vars['http_uri']; ?>
" target="_blank">登录</a>，没有账号，<a href="http://passport.mplife.com/register.aspx?sourceurl=<?php echo $this->_tpl_vars['http_uri']; ?>
" target="_blank">马上注册</a></p>
    </div>
    <div class="shade"></div>
</div>

<script type="text/javascript">
var site_url = '<?php echo $this->_tpl_vars['_CONF']['SITE_URL']; ?>
';

$(function(){
		//loading img
	var imglist=document.getElementsByTagName("img"),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute("data-lazyload"));relist.push(imglist[i])}}function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"))};if(ie.test(browser)){ele.src=Img.src}}for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};
	
	FnHover('allBtn','allBox');
	//焦点图
	Mp.Focus({
		ele:'focus-list',
		pre:'pre-img',
		next:'next-img',
		msec:6000 //毫秒
	})
	//小焦点图一
	Mp.Focus({
		ele:'small-focus-list_01',
		pre:'small-focus-left-btn_01',
		next:'small-focus-right-btn_01',
		msec:4000 //毫秒
	})
		//小焦点图二
	Mp.Focus({
		ele:'small-focus-list_02',
		pre:'small-focus-left-btn_02',
		next:'small-focus-right-btn_02',
		msec:3500 //毫秒
	})
	Mp.LinkHover({
		id:'right-list_01'
	})
	Mp.LinkHover({
		id:'right-list_02'
	})
	
	Broaden({id:'brandLogo',HoverWidth:386})
			

	});
	
	function Concern(gid, type) {
		$.getJSON(site_url + '/home/index/concern/gid/' + gid, {}, function(json){
			if(json.Code == 100) {
				$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
				$('#' + getTypeId(gid, type) + ' q').html(json.Num);
			} else if(json.Code == 200) {
				$('#popupLogin').show();
			}
		});
	}
	
	function Favorite(gid, type) {
		$.getJSON(site_url + '/home/index/favorite/gid/' + gid, {}, function(json){
			if(json.Code == 100) {
				$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
				$('#' + getTypeId(gid, type) + ' q').html(json.Num);
			} else if(json.Code == 200) {
				$('#popupLogin').show();
			}
		});
	}
	
	function getTypeId(gid, type) { return type + '_' + gid;}
</script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>