<?php /* Smarty version 2.6.27, created on 2016-02-22 16:32:31
         compiled from ticket/show.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'siteMeta', 'ticket/show.php', 5, false),array('modifier', 'date_format', 'ticket/show.php', 36, false),array('modifier', 'string_format', 'ticket/show.php', 75, false),array('function', 'math', 'ticket/show.php', 46, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'siteMeta', 'ticket' => $this->_tpl_vars['ticketRow']['ticket_title'], 'shop' => $this->_tpl_vars['shopInfo']['shop_name'], 'brand' => $this->_tpl_vars['shopInfo']['brand_name'])), $this); ?>

<link href="/css/reset.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/common.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/css/ny.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link href="/js/artDialog/skins/idialog.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
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
      <!--内页-->
      <div class="w1210">
      <div class="nyWaper">
      <!--左-->
      		<div class="nyLeft">
            	<h2><?php echo $this->_tpl_vars['ticketRow']['ticket_title']; ?>
</h2>
                	<div class="priceBox">
                    	<?php if ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'coupon'): ?>
                        <p class="new">券面值：<font>￥<?php echo $this->_tpl_vars['ticketRow']['par_value']; ?>
</font></p>
                        <?php elseif ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'voucher'): ?>
                    	<p class="new">￥<font><?php echo $this->_tpl_vars['ticketRow']['selling_price']; ?>
</font></p>
                        <p class="old">原价：<font>￥<?php echo $this->_tpl_vars['ticketRow']['par_value']; ?>
</font></p>
                        <?php endif; ?>
                     </div>
                     <div class="shareBox">
                     <div class="userMesage">
                     	<?php echo $this->_tpl_vars['ticketRow']['user_name']; ?>
 / <span><?php echo ((is_array($_tmp=$this->_tpl_vars['ticketRow']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y-%m-%d %H:%M:%S") : smarty_modifier_date_format($_tmp, "%Y-%m-%d %H:%M:%S")); ?>
</span>
                     </div>
                    <a class="shareBtn" id="shareBtn">一键分享<s></s></a>
                    <div class="bd_share"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'share.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
                    </div>
                    <div class="messageBox">
                    	<p>用券店铺：<font><?php echo $this->_tpl_vars['ticketRow']['shop_name']; ?>
</font>（店铺地址和地图请见右侧）</p>
                        <p>券有效期：<font><?php echo ((is_array($_tmp=$this->_tpl_vars['ticketRow']['valid_stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%m.%d") : smarty_modifier_date_format($_tmp, "%m.%d")); ?>
日-<?php echo ((is_array($_tmp=$this->_tpl_vars['ticketRow']['valid_etime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%m.%d") : smarty_modifier_date_format($_tmp, "%m.%d")); ?>
日</font></p>
                        
                        <?php if ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'coupon'): ?>
                        	<p>剩余数量：<font id="surplusTicket"><?php echo smarty_function_math(array('equation' => "x - y",'x' => $this->_tpl_vars['ticketRow']['total'],'y' => $this->_tpl_vars['ticketRow']['has_led']), $this);?>
</font>张</p>
                        <?php elseif ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'voucher'): ?>
                        	<p>券总计<font id="surplusTotal"></font>张，已出售<font id="surplusHadSold"></font>张</p>
                        <?php endif; ?>
                        
                        <?php if ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'coupon'): ?>
                        <input type="text" class="txt" name="phone" id="phone" value="" placeholder="输入手机号码获取优惠券" />
                        <input type="button" class="btn" value="立即申领" onClick="apply(<?php echo $this->_tpl_vars['tid']; ?>
)" id="apply" />
                        <?php elseif ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'voucher'): ?>
                        <input type="button" class="btn" value="立即购买" onClick="applyVoucher(<?php echo $this->_tpl_vars['tid']; ?>
)" id="voucher" />
                        <?php endif; ?>
                        </p>
                    </div>
                    <h3 class="nyTitle">本券使用说明：</h3>
     				<div class="conTxt"><?php echo $this->_tpl_vars['ticketRow']['content']; ?>
</div>
                    <?php if ($this->_tpl_vars['goodTicketList']['data']): ?>
                    <h3 class="nyTitle">本券适用商品：<a href="/home/good/more/tid/<?php echo $this->_tpl_vars['tid']; ?>
" target="_blank">更多&gt;&gt;</a></h3>
                    <div class="picList">
                        <ul>
                        	<?php $_from = $this->_tpl_vars['goodTicketList']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                            <li>
                            	<div class="pic">
                                <div class="nyListPic">
                                	<a href="/home/good/show/gid/<?php echo $this->_tpl_vars['item']['good_id']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" height="<?php echo $this->_tpl_vars['item']['height']; ?>
" /></a>
                                </div>
                                <a class="m1" href="javascript:Concern(<?php echo $this->_tpl_vars['item']['good_id']; ?>
, 'chosen_concern')" id="chosen_concern_<?php echo $this->_tpl_vars['item']['good_id']; ?>
"><s></s><q><?php echo $this->_tpl_vars['item']['concerned_number']; ?>
</q></a>
                                <a class="m2" href="javascript:Favorite(<?php echo $this->_tpl_vars['item']['good_id']; ?>
, 'chosen_favorite')" id="chosen_favorite_<?php echo $this->_tpl_vars['item']['good_id']; ?>
"><s></s><q><?php echo $this->_tpl_vars['item']['favorite_number']; ?>
</q></a>
                                </div>
                                <div class="txt">
                                    <p class="l1"><span class="name"><a href="/home/shop/show/sid/<?php echo $this->_tpl_vars['item']['shop_id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</a></span><span class="price">¥<font><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['dis_price'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%d") : smarty_modifier_string_format($_tmp, "%d")); ?>
</font></span></p>
                                    <p class="l2"><a href="/home/good/show/gid/<?php echo $this->_tpl_vars['item']['good_id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['item']['good_name']; ?>
</a></p>
                                </div>
                            </li>
                            <?php endforeach; endif; unset($_from); ?>                      
                        </ul>
                        <div class="moreLink">
                        	<a href="/home/good/more/tid/<?php echo $this->_tpl_vars['tid']; ?>
" target="_blank">更多适用商品<font>&gt;&gt;</font></a>
                        </div>
                    </div>
                    <?php endif; ?>
            </div>
            <!--右侧-->
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'ticket/right.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>
          <!--关于超级购-->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<script type="text/javascript" src="/js/jquery.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/index.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=BC3eb870cf1e6cca1d46ccab6baad228"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript">
var site_url = '<?php echo $this->_tpl_vars['_CONF']['SITE_URL']; ?>
';
function fnBaiduMap(json){
	var map = new BMap.Map("allmap");            // 创建Map实例
	var point = new BMap.Point(json.x,json.y);    // 创建点坐标
	map.centerAndZoom(point,18);                     // 初始化地图,设置中心点坐标和地图级别。
	map.enableScrollWheelZoom();                            //启用滚轮放大缩小
	map.addOverlay(new BMap.Marker(point));
	map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}));  //右上角，仅包含平移和缩放按钮
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT, type: BMAP_NAVIGATION_CONTROL_PAN}));  //左下角，仅包含平移按钮
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, type: BMAP_NAVIGATION_CONTROL_ZOOM}));  //右下角，仅包含缩放按钮
}

$(function(){
	FnHover('allBtn','allBox');
	FnHover('shareBtn','bdshare');
	fnBaiduMap({
		x:<?php echo $this->_tpl_vars['shopInfo']['lng']; ?>
,
		y:<?php echo $this->_tpl_vars['shopInfo']['lat']; ?>

	});
	
	<?php if ($this->_tpl_vars['ticketRow']['ticket_mark'] == 'voucher'): ?>
	$.getJSON("http://superbuy.mplife.com/interface/SuperBuyHandler.ashx?jsoncallback=?",
	{
		action: 'GetOneProduct',
		activityid: '<?php echo $this->_tpl_vars['ticketRow']['ticket_uuid']; ?>
'
	},
	function (result) {
		if(result.status == 0) {
			return false;
		}
		var jsonList = eval(result.data);
		var activities = eval(result.data.Avtivities);
		$('#surplusTotal').html(parseInt(activities[0]["ProductNum"]));
		$('#surplusHadSold').html(parseInt(activities[0]["ProductDisplaySale"]));
    });	
	<?php else: ?>
	$.getJSON('/home/ticket/get-ticket-plus/tid/' + <?php echo $this->_tpl_vars['tid']; ?>
, {}, function(json){
		if(json.res == 100) {
			$('#surplusTicket').html(json.extra);
		}
	});	
	<?php endif; ?>
});

function Concern(gid, type) {
	$.getJSON('/home/index/concern/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function Favorite(gid, type) {
	$.getJSON('/home/index/favorite/gid/' + gid, {}, function(json){
		if(json.Code == 100) {
			$('#' + getTypeId(gid, type)).attr('href','javascript:void(0)').addClass('off');
			$('#' + getTypeId(gid, type) + ' q').html(json.Num);
		} else if(json.Code == 200) {
			$('#popupLogin').show();
		}
	});
}

function getTypeId(gid, type) { return type + '_' + gid;}

function apply(tid) {
	var phone = $('#phone').val();
	var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
	
	if(!phoneReg.test(phone)) {
		$.dialog.alert('请输入正确的手机号码');
	} else {
		$('#apply').removeClass().addClass('loading').attr('value', '申请中...');
		$.getJSON('/home/ticket/apply-ticket', { tid:tid, phone:phone}, function(json){
			switch(json.res) {
				case 100:
				case 105:
					$('#surplus').html(json.extra.lave);
					$.dialog.alert(json.msg);
					break;
				default:
					$.dialog.alert(json.msg);
					break;
			}
			setTimeout(function(){$('#apply').removeClass().addClass('btn').attr('value', '立即申领');} , 3000);
		});
	}
}

function applyVoucher(tid) {
	var phone = $('#phone').val();
	var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
	
/*	if(!phoneReg.test(phone)) {
		$.dialog.alert('请输入正确的手机号码');
	} else {
	*/	
		$.getJSON('/home/ticket/apply-ticket-voucher', { tid:tid/*, phone:phone*/}, function(json){
			switch(json.res) {
				case 100:
					$('#voucher').removeClass().addClass('loading').attr('value', '提交中...');
					window.location.href = 'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=' + json.extra.guid;
					break;
				case 105:
					$('#surplus').html(json.extra.lave);
					$.dialog.alert(json.msg);
					break;
				case 99:
					$("#popupLogin").show();
					break;
				default:
					$.dialog.alert(json.msg);
					break;
			}
		});
/*	}*/	
}
</script>
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
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>