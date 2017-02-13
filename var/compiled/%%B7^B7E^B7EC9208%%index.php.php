<?php /* Smarty version 2.6.27, created on 2016-02-19 09:38:16
         compiled from market/index.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'siteMeta', 'market/index.php', 5, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'siteMeta')), $this); ?>

<link href="/css/reset.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/index.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
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
	//loading img
	var imglist=document.getElementsByTagName("img"),relist=[],arrsrc=[],iCur=0,browser=window.navigator.userAgent.toLowerCase(),ie=/(msie\s7\.0)|(msie\s8\.0)/;for(var i=0;i<imglist.length;i++){if(imglist[i].getAttributeNode("data-lazyload")){arrsrc.push(imglist[i].getAttribute("data-lazyload"));relist.push(imglist[i])}}function LoadImg(ele,url){var Img=new Image();Img.src=url;Img.onload=function(){ele.src=Img.src;ele.removeAttributeNode(ele.getAttributeNode("data-lazyload"))};if(ie.test(browser)){ele.src=Img.src}}for(var i=0;i<arrsrc.length;i++){LoadImg(relist[i],arrsrc[i])};
	
	getMarket(<?php echo $this->_tpl_vars['firstCid']; ?>
);
})
 
function getMarket(cid){
var data = <?php echo $this->_tpl_vars['marketbycircle']; ?>
;
var market = data[cid].market; 

var _html = "";
	$.each(market, function(k, v){
		_html += '<ul style="display: block;">';
		_html += '<li>';
		_html += '<a href="/home/market/show/mid/' + v.market_id + '" class="shop-center-list-pic"><img src="'+ v.logo_img +'"  width="125" height="125"></a>';
		_html += '<a href="/home/market/show/mid/' + v.market_id + '" class= "shop-center-list-txt">' + v.market_name + '</a>';
		_html += '</li>';
		_html += '</ul>';

	});
	$('.shop-center-list').html(_html);	
}

</script>
</head>
<body>
	<div class="w1210">
    	<!--top-->
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
      <!--------------------------商场-------------------------->
	  <div class="w1210">
       <div class="shop-wrap">
       		<!---导航--->
            <div class="shop-wrap-nav">
            	<div class="shop-wrap-nav-list">
                	<ul>
                    	<?php $_from = $this->_tpl_vars['hotCircle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                    	<li><a onmouseover="getMarket(<?php echo $this->_tpl_vars['item']['id']; ?>
)" ><?php echo $this->_tpl_vars['item']['name']; ?>
</a><s class="list-type-on"></s></li>
                        <?php endforeach; endif; unset($_from); ?>
                    </ul>
                </div>
            </div>
            <!--center list-->
            <div class="shop-center-list">
            </div>
            <!-----right list------>
            <div class="shop-right-list">
            	<h2 class="shop-right-list-title">推荐商场</h2>
                <?php $_from = $this->_tpl_vars['recommMarket']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <div class="shop-right-list-col">
                <a href="<?php if ($this->_tpl_vars['item']['come_from_id'] != 0): ?>/home/market/show/mid/<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['www_url']; ?>
<?php endif; ?>" target="_blank" class="shop-right-list-col-pic"><img src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="186" height="196"></a>
                <a href="<?php if ($this->_tpl_vars['item']['come_from_id'] != 0): ?>/home/market/show/mid/<?php echo $this->_tpl_vars['item']['come_from_id']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['www_url']; ?>
<?php endif; ?>" target="_blank" class="shop-right-list-col-txt"><?php echo $this->_tpl_vars['item']['title']; ?>
</a>
                </div>
                <?php endforeach; endif; unset($_from); ?>
            </div>
            <?php $_from = $this->_tpl_vars['regionMarket']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
           	<div class="shop-area-col">
            	<h3 class="shop-area-col-title" id="<?php echo $this->_tpl_vars['item']['region_id']; ?>
"><?php echo $this->_tpl_vars['item']['region_name']; ?>
</h3>
                <div class="shop-area-list-pic">
                	<ul>
                    	<?php $_from = $this->_tpl_vars['item']['market_img']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key_img'] => $this->_tpl_vars['item_img']):
?>
                    	<li><a href="/home/market/show/mid/<?php echo $this->_tpl_vars['item_img']['market_id']; ?>
"><img src="/images/blank.png"  data-lazyload="<?php echo $this->_tpl_vars['item_img']['logo_img']; ?>
" width="125" height="125"></a></li>
         				<?php endforeach; endif; unset($_from); ?>
                    </ul>
                </div>
                <div class="shop-area-list-txt">
                	<ul>
                    	<?php $_from = $this->_tpl_vars['item']['market_no_img']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key_no_img'] => $this->_tpl_vars['item_mo_img']):
?>
                    	<li>
                        	<a href="/home/market/show/mid/<?php echo $this->_tpl_vars['item_mo_img']['market_id']; ?>
"><?php echo $this->_tpl_vars['item_mo_img']['market_name']; ?>
</a>
                        </li>
                        <?php endforeach; endif; unset($_from); ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; endif; unset($_from); ?>
            
       </div> 
       <!------------------------------>
       
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
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>