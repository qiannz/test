<?php /* Smarty version 2.6.27, created on 2016-02-18 17:03:42
         compiled from active/comeandgrap/index.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'active/comeandgrap/index.php', 30, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link href="/css/active/comeandgrap/common.css?t=<?php echo $this->_tpl_vars['version']; ?>
" rel="stylesheet" type="text/css">
</head>
<body>

    <div class="viewport">

<!--     <div class="top-header"> -->
<!--         <a class="top-header-return"></a> -->
<!--         <p>{$title}</p> -->
<!--      </div> -->
        <!--header-->
    <div class="header">

            <!-- <a class=" return-home"></a> -->

        <span class="header-left-txt"><?php echo $this->_tpl_vars['title']; ?>
</span>

            <a class="header-link-rule" ></a>
			<a class="header-link-order" href="/active/comeandgrap/order-list"></a>
    </div>

    <!--tab title-->
    <?php if (count($this->_tpl_vars['top_banner']) != 0): ?>
    <div class="tab-title">
		<?php $_from = $this->_tpl_vars['top_banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['banner']):
?>
        <div class="tab-btn <?php if ($this->_tpl_vars['k'] == 0): ?>select-on<?php endif; ?>">
            <p class="font_16">周<?php echo $this->_tpl_vars['banner']['week']; ?>
</p>
            <p class="font_14"><?php echo $this->_tpl_vars['banner']['text']; ?>
</p>
        </div>
		<?php endforeach; endif; unset($_from); ?>
    </div>
    <?php endif; ?>




        <!---1-->
        <?php if (count($this->_tpl_vars['activities_in']) != 0): ?>
        <div class="list" style="display: block">
        	<?php $_from = $this->_tpl_vars['activities_in']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acty_row']):
?>
            <div class="box">

                <div class="box-pic">
                    <p class="over-time" data-status="in" data-value="<?php echo $this->_tpl_vars['acty_row']['timeout']; ?>
"></p>
                    <a href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
">
                    	<div class="share-bg" style="display:none" id="display<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
"><span class="share-txt_01"></span></div>
                    	<img src="<?php echo $this->_tpl_vars['acty_row']['cover_img']; ?>
">
                    </a>
                </div>
                <div class="box-bottom">
                    <h4 class="box-bottom-title"><?php echo $this->_tpl_vars['acty_row']['ticket_title']; ?>
</h4>
                    <a class="btn_01" href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
">立刻抢</a>
                    <div class="tag-box">
                        <p class="zhekou-tag"><?php echo $this->_tpl_vars['acty_row']['discount']; ?>
折</p>
                         惊爆价
                        <span class="tag-now"><?php echo $this->_tpl_vars['acty_row']['selling_price']; ?>
</span>
                        <span class="tag-old"><?php echo $this->_tpl_vars['acty_row']['par_value']; ?>
</span>
                    </div>

                </div>

                <div class="bar-box begin begin-<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" data-tid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" data-tuuid="<?php echo $this->_tpl_vars['acty_row']['ticket_uuid']; ?>
">

                    <div class="bar"  >
                        <p class="bar-color" style="width: 0%">0%</p>
                    </div>

                    <div class="number">

                        <div class="number-l">
                            <p>已发放</p>
                            <p id="hadsold<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="clear">0名额</p>
                        </div>
                        <div class="number-r">
                            <p>剩余</p>
                            <p id="surplus<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="stock"><?php echo $this->_tpl_vars['acty_row']['total']; ?>
名额</p>
                        </div>

                    </div>

                </div>

            </div>
			<?php endforeach; endif; unset($_from); ?>
        </div>
        <?php endif; ?>
    	<!------2------>
    	<?php $_from = $this->_tpl_vars['activities_will']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['activities']):
?>
        <div class="list" <?php if (count($this->_tpl_vars['activities_in']) == 0): ?>style="display: block"<?php endif; ?>>
        	<?php $_from = $this->_tpl_vars['activities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acty_row']):
?>
            <div class="box">

                <div class="box-pic">
                    <p class="over-time" data-status="new" data-value="<?php echo $this->_tpl_vars['acty_row']['timeout']; ?>
"></p>
                    <a href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
"><img src="<?php echo $this->_tpl_vars['acty_row']['cover_img']; ?>
"></a>
                </div>
                <div class="box-bottom">
                    <h4 class="box-bottom-title"><?php echo $this->_tpl_vars['acty_row']['ticket_title']; ?>
</h4>
                    <?php if ($this->_tpl_vars['acty_row']['is_notice'] == 1): ?>
                    <a data-ticketid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="btn_03">已提醒</a>
                    <?php else: ?>
	                <a data-isnotice="<?php echo $this->_tpl_vars['acty_row']['is_notice']; ?>
" data-ticketid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="btn_02">提醒我</a>
					<?php endif; ?>
                    <div class="tag-box">
                        <p class="zhekou-tag"><?php echo $this->_tpl_vars['acty_row']['discount']; ?>
折</p>
                        惊爆价
                        <span class="tag-now"><?php echo $this->_tpl_vars['acty_row']['selling_price']; ?>
</span>
                        <span class="tag-old"><?php echo $this->_tpl_vars['acty_row']['par_value']; ?>
</span>
                    </div>

                </div>

                <div class="bar-box">
                    <p class="follow">已关注人数：<span class="color_01"><?php echo $this->_tpl_vars['acty_row']['prompted_num']; ?>
</span></p>

                </div>

            </div>
            <?php endforeach; endif; unset($_from); ?>
        </div>
        <?php endforeach; endif; unset($_from); ?>
    	<!------3------>
    	<?php if (count($this->_tpl_vars['activities_tomorrow']) != 0): ?>
        <div class="list" <?php if (count($this->_tpl_vars['activities_in']) == 0 && count($this->_tpl_vars['activities_will']) == 0): ?>style="display: block"<?php endif; ?>>
        	<?php $_from = $this->_tpl_vars['activities_tomorrow']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acty_row']):
?>
            <div class="box">

                <div class="box-pic">
                    <p class="over-time" data-status="new" data-value="<?php echo $this->_tpl_vars['acty_row']['timeout']; ?>
"></p>
                    <a href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
">
                    	<img src="<?php echo $this->_tpl_vars['acty_row']['cover_img']; ?>
">
                    </a>
                </div>
                <div class="box-bottom">
                    <h4 class="box-bottom-title"><?php echo $this->_tpl_vars['acty_row']['ticket_title']; ?>
</h4>
                    <?php if ($this->_tpl_vars['acty_row']['is_notice'] == 1): ?>
                    <a data-ticketid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="btn_03">已提醒</a>
                    <?php else: ?>
	                <a data-isnotice="<?php echo $this->_tpl_vars['acty_row']['is_notice']; ?>
" data-ticketid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="btn_02">提醒我</a>
					<?php endif; ?>
                    <div class="tag-box">
                        <p class="zhekou-tag"><?php echo $this->_tpl_vars['acty_row']['discount']; ?>
折</p>
                        惊爆价
                        <span class="tag-now"><?php echo $this->_tpl_vars['acty_row']['selling_price']; ?>
</span>
                        <span class="tag-old"><?php echo $this->_tpl_vars['acty_row']['par_value']; ?>
</span>
                    </div>

                </div>

                <div class="bar-box">
                    <p class="follow">已关注人数：<span class="color_01"><?php echo $this->_tpl_vars['acty_row']['prompted_num']; ?>
</span></p>

                </div>

            </div>
			<?php endforeach; endif; unset($_from); ?>




        </div>
		<?php endif; ?>
	


    <div class="u">
        <h3><img class="img_u" src="/images/active/comeandgrap/love.png"></h3>
		<div class="ajax_more"></div>
        <p class="loading load_img"></p>
    </div>
    </div>
    <script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
	<script type="text/javascript" src="/js/active/comeandgrap.js?t=<?php echo $this->_tpl_vars['version']; ?>
"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
	$(function(){
		$(".btn_02").click(function(e){
			var id = this;
			$.ajax({
                type:'POST',
                url:"/active/oneyuanpurchase/notice-me",
                data:{ticket_id:$(this).data("ticketid")},
                dataType:'json',
                success:function(data){
	                if( data.res == 101 ){
						//未登录
						window.location.href = "/active/oneyuanpurchase/login?jumpfrom=/active/comeandgrap";
		            }else if( data.res == 103 ){
		            	alert(data.msg);
			        }else if( data.res == 100 ){
			        	alert(data.msg);
			            var num = $(id).parents(".box").find(".color_01").html();
			            num = parseInt(num);
			            num++;
			            $(id).parents(".box").find(".color_01").html( num );
			            $(id).unbind('click').attr('class', 'btn_03').html('已提醒');
		            }
                }
            });
            e.stopPropagation();
		});
		var curPage = 1;
		moreAjax(curPage);
		$(".loading").click(function(){
			$(".loading").html("");
			$(".loading").addClass("load_img");
			curPage++;
			moreAjax( curPage );
		});
		$(".begin").each(function(){
			remainingTickets($(this).data("tid"),$(this).data("tuuid"));
		});
	});
	
	function moreAjax(page) {
		$.ajax({
			url:"<?php echo $this->_tpl_vars['_CONF']['GLOBAL_CONF']['SITE_URL']; ?>
/home/daren/love-more",
			dataType:"json",
			data:{sid:"<?php echo $this->_tpl_vars['sid']; ?>
","page":page},
			success:function(data){
				var _html = '';
				var _len = data.length;
				if(page > 1 && !_len) {
					alert('没有更多啦');
					$(".loading").hide();
					return false;
				}
				for(var i=0; i< _len; i++ ){
					_html += '<div class="pic1">'
					_html += '<div class="t">'+data[i].title+'</div>'
					_html += '<div class="price_foot">';
					if(data[i].cmark == 'voucher_view') {	
						_html += '<p class="p1">原价<span class="old_p_foot">¥' + data[i].par_value + '</span></p>'
						_html += '<p class="p2">¥<span class="big">'+data[i].selling_price+'</span></p>';
					}
					_html +='</div>'
					_html +='<a href="<?php echo $this->_tpl_vars['_CONF']['GLOBAL_CONF']['SITE_URL']; ?>
/home/ticket/wap/tid/'+data[i].ticket_id+'/from/ios" target="_blank"><img src="' + data[i].img_url + '"></div></a>';
				}
				if(_html != '') {
					//$(_html).appendTo($("div.u").find("img").after());
					$(".ajax_more").append(_html);
					$(".loading").removeClass("load_img");
					$(".loading").html("点击加载更多");
				}
			}
		});	
	}
	
    function remainingTickets( ticket_id , ticket_uuid ){
		$.ajax({
            type:'POST',
            url:'/active/oneyuanpurchase/ticket-sold-num',
            data:{"tuuid":ticket_uuid},
            dataType:'json',
            success:function(data){
                $("#hadsold"+ticket_id).html(data["hadsold"]+"名额");
                $("#surplus"+ticket_id).html(data["surplus"]+"名额");
				if( data["surplus"] <= 0 ){
					$("#display"+ticket_id).show();
				}
				if( data["hadsold"]+data["surplus"] > 0 ){
					processFn($('.begin-'+ticket_id));
				}
				
            }
		});
	}

    $(function(){
    	
		wx.config({
			debug: false,
			appId: '<?php echo $this->_tpl_vars['weixinKeyArr']['Result']['AppId']; ?>
',
			timestamp: <?php echo $this->_tpl_vars['weixinKeyArr']['Result']['TimeStamp']; ?>
, 
			nonceStr: '<?php echo $this->_tpl_vars['weixinKeyArr']['Result']['NonceStr']; ?>
', 
			signature: '<?php echo $this->_tpl_vars['weixinKeyArr']['Result']['Signature']; ?>
',
			jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo']
		});
				 
		wx.ready(function() {
			var title = '<?php echo $this->_tpl_vars['title']; ?>
';
			var desc = '<?php echo $this->_tpl_vars['desc']; ?>
';
			var buyUrl = '<?php echo $this->_tpl_vars['share_url']; ?>
';
			var imgUrl = '<?php echo $this->_tpl_vars['share_img_url']; ?>
';
			//注册分享给朋友
			wx.onMenuShareAppMessage({
				title: title, 
				desc: desc, //描述
				link: buyUrl, //分享地址
				imgUrl: imgUrl, //图片地址
				trigger: function (res) {
					// 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
					//alert('用户点击发送给朋友');
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次介绍给您的朋友哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
		
			//注册朋友圈信息
			wx.onMenuShareTimeline({
				title: title,
				link: buyUrl,
				imgUrl: imgUrl,
				trigger: function (res) {
					// 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
					//alert('用户点击分享到朋友圈');
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次分享到您的朋友圈哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
			//分享到QQ
			wx.onMenuShareQQ({
				title: title,
				desc: desc,
				link: buyUrl,
				imgUrl: imgUrl,
				trigger: function (res) {
					//alert('用户点击分享到QQ');
				},
				complete: function (res) {
					// alert(JSON.stringify(res));
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次介绍给您的朋友哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
		
			//分享到微博
			wx.onMenuShareWeibo({
				title: title,
				desc: desc,
				link: buyUrl,
				imgUrl: imgUrl,
				trigger: function (res) {
					//alert('用户点击分享到微博');
				},
				complete: function (res) {
					//alert(JSON.stringify(res));
				},
				success: function (res) {
					alert('感谢您的分享');
				},
				cancel: function (res) {
					alert('真可惜!还请下次介绍给您的朋友哦!');
				},
				fail: function (res) {
					alert("分享失败了!"+JSON.stringify(res));
				}
			});
		});
	
	});
	
	setTimeout(function(){
		var cmslog = document.createElement("script");
		cmslog.src = "http://buy.mplife.com/js/log.js";
		document.body.appendChild(cmslog);
	}, 1000);		
    </script>
</body>
</html>