<?php /* Smarty version 2.6.27, created on 2016-02-22 13:11:13
         compiled from active/oneyuanpurchase/index.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'active/oneyuanpurchase/index.php', 34, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link href="/css/active/oneyuanpurchase/common.css?t=<?php echo $this->_tpl_vars['version']; ?>
" rel="stylesheet" type="text/css">
</head>
<body>

    <div class="viewport">
        <div class="header">
            <span class="header-left-txt">1元赢壕礼</span>
            <a class="header-link-rule" href="http://promo.mplife.com/other/20151103/"></a>
            <a class="header-link-order" href="/active/oneyuanpurchase/order-list"></a>
        </div>

        <div class="focus">
        	<?php $_from = $this->_tpl_vars['recommend_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['recommend_row']):
?>
            	<?php if ($this->_tpl_vars['recommend_row']['www_url']): ?>
                <a class="focus-pic" href="<?php echo $this->_tpl_vars['recommend_row']['www_url']; ?>
"><img src="<?php echo $this->_tpl_vars['recommend_row']['img_url']; ?>
" /></a>
                <?php else: ?>
                <img src="<?php echo $this->_tpl_vars['recommend_row']['img_url']; ?>
" />
                <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
        </div>

        <div class="list">

			<?php if (count($this->_tpl_vars['activities_in']) != 0): ?>
            <div class="box">
                <h3 class="box-title">一元夺宝</h3>
                <?php $_from = $this->_tpl_vars['activities_in']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acty_row']):
?>
                <div class="activity-box">
	                <div class="box-pic">
                    	
	                    <p class="over-time" data-status="in" data-value="<?php echo $this->_tpl_vars['acty_row']['timeout']; ?>
"></p>
	                    <a href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
">
                        <div class="share-bg" style="display:none" id="display<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
"><span class="share-txt_01"></span></div>
                        <img src="<?php echo $this->_tpl_vars['acty_row']['cover_img']; ?>
"></a>
	                </div>
	                <div class="box-bottom">
	
	                        <div class="begin begin-<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" data-tid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" data-tuuid="<?php echo $this->_tpl_vars['acty_row']['ticket_uuid']; ?>
">
	
	                                <h4 class="box-bottom-title"><?php echo $this->_tpl_vars['acty_row']['ticket_title']; ?>
</h4>
	
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
	
	                                <a class="btn_01" href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
"><?php echo $this->_tpl_vars['acty_row']['selling_price']; ?>
元夺宝</a>
	
	                         </div>
	                 </div>
                 </div>
                 <?php endforeach; endif; unset($_from); ?>
           </div>
           <?php endif; ?>

			<?php if (count($this->_tpl_vars['unbegin_activities']) != 0): ?>
            <div class="box">
                <h3 class="box-title">新品预告</h3>
                <?php $_from = $this->_tpl_vars['unbegin_activities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acty_row']):
?>
                <div class="activity-box">
	                <div class="box-pic">
	                    <p class="over-time" data-status="new" data-value="<?php echo $this->_tpl_vars['acty_row']['timeout']; ?>
"></p>
	                    <a href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
"><img src="<?php echo $this->_tpl_vars['acty_row']['cover_img']; ?>
"></a>
	                </div>
	                <div class="box-bottom">
	
	                    <div class="not-begin">
	
	                        <h4 class="box-bottom-title"><?php echo $this->_tpl_vars['acty_row']['ticket_title']; ?>
</h4>
	
<!--	                        <div class="bar">
	                            未开始
	                        </div>-->
	
	                        <div class="number">
	
	                            <div class="number-l">
	                                <p>已关注人数<span class="color_01"><?php echo $this->_tpl_vars['acty_row']['prompted_num']; ?>
</span></p>
	
	                            </div>
	
	                        </div>
							<?php if ($this->_tpl_vars['acty_row']['is_notice'] == 1): ?>
                            <a data-ticketid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="btn_03">已提醒</a>
                            <?php else: ?>
	                        <a data-isnotice="<?php echo $this->_tpl_vars['acty_row']['is_notice']; ?>
" data-ticketid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" class="btn_02">提醒我</a>
							<?php endif; ?>
	
	                    </div>
	
	                </div>
                </div>
               <?php endforeach; endif; unset($_from); ?>
            </div>
            <?php endif; ?>
        </div>
		

        <div class="list">
			<?php if (count($this->_tpl_vars['ended_activities']) != 0): ?>
            <div id="previousWorks" class="box">
                <h3 class="box-title">往期揭晓 </h3>
                <?php $_from = $this->_tpl_vars['ended_activities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acty_row']):
?>
                <div class="activity-box">
	                <div class="box-pic">
	                    <a href="/home/ticket/wap/tid/<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
"><div class="share-bg"><span class="share-txt_02"></span></div></a>
	                    <p class="over-time" data-datetime="<?php echo $this->_tpl_vars['acty_row']['lottery_date']; ?>
" data-status="expire" data-value="<?php echo $this->_tpl_vars['acty_row']['timeout']; ?>
"></p>
	                    <a><img src="<?php echo $this->_tpl_vars['acty_row']['cover_img']; ?>
"></a>
	                </div>
	                <div class="box-bottom">
	
	                    <div class="begin begin-<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" data-tid="<?php echo $this->_tpl_vars['acty_row']['ticket_id']; ?>
" data-tuuid="<?php echo $this->_tpl_vars['acty_row']['ticket_uuid']; ?>
">
	
	                        <h4 class="box-bottom-title"><?php echo $this->_tpl_vars['acty_row']['ticket_title']; ?>
</h4>
	
	                        <div class="bar">
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
	
	                        <a class="btn_03">已结束</a>
	
	
	                    </div>
	
	                </div>
	                <?php if ($this->_tpl_vars['acty_row']['lottery_action'] == 1): ?>
	                <div class="win-data">
	                    <p class="font_16">恭喜<span class="color_01"><?php echo $this->_tpl_vars['acty_row']['lottery_user']['user_name']; ?>
</span> 手机号<?php echo $this->_tpl_vars['acty_row']['lottery_user']['mobile']; ?>
</p>
	                    <p class="font_18">以 <span class="color_01"><?php echo $this->_tpl_vars['acty_row']['selling_price']; ?>
元</span> 购得现金券！抽奖号码为 <span class="color_01"><?php echo $this->_tpl_vars['acty_row']['lottery_user']['order_no']; ?>
</span></p>
	                </div>
	                <?php endif; ?>
	            </div>
                <?php endforeach; endif; unset($_from); ?>
            </div>
			<?php endif; ?>
       </div>
		
        <p class="loading" <?php if (count($this->_tpl_vars['ended_activities']) != 3): ?>style="display:none;"<?php endif; ?>>
            <span class="loading-gif"></span>点击加载更多
        </p>
		


    </div>
    <script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
	<script type="text/javascript" src="/js/active/oneyuanpurchase.js?t=<?php echo $this->_tpl_vars['version']; ?>
"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
    	$(".loading-gif").hide();
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
							window.location.href = "/active/oneyuanpurchase/login";
			            }else if( data.res == 103 ){
			            	alert(data.msg);
				        }else if( data.res == 100 ){
				        	alert(data.msg);
				            var num = $(id).parent(".not-begin").find(".color_01").html();
				            num = parseInt(num);
				            num++
				            $(id).parent(".not-begin").find(".color_01").html( num );
				            $(id).attr('class', 'btn_03').html('已提醒');
			            }
	                }
	            });
	            e.stopPropagation();
			});
			var curPage = 1;
			$(".loading").click(function(){
				$(".loading-gif").show();
				curPage++;
				loadingList( curPage );
			});
			$(".begin").each(function(){
				remainingTickets($(this).data("tid"),$(this).data("tuuid"));
			});
		});
		var activityList = document.getElementById("previousWorks");
		function loadingList( page ){
			$.ajax({
                type:'POST',
                url:'/active/oneyuanpurchase/activity-more',
                data:{page:page},
                dataType:'json',
                success:function(data){
                	$(".loading-gif").hide();
	                var _len = data.length;
		  		    if(page > 1 && !_len) {
		  			  	$(".loading").hide();
		  				return false;
		  			}
			  		_frag = document.createDocumentFragment();
						
					for(var i = 0 ;i< _len; i++){
						  if(data[i]){
							var _html = '',
								_ele = document.createElement('div');
								_ele.className = 'activity-box page-'+page;  
				                _html += '<div class="box-pic">';
				                _html += '<a href="/home/ticket/wap/tid/'+data[i]["ticket_id"]+'"><div class="share-bg"><span class="share-txt_02"></span></div></a>';
				                _html += '<p class="over-time" data-datetime="'+data[i]["lottery_date"]+'" data-status="expire" data-value="'+data[i]["timeout"]+'"></p>'
				                _html += '<a><img src="'+data[i]["cover_img"]+'"></a>';
				                _html += '</div>';
				                _html += '<div class="box-bottom">';
								_html += '<div class="begin begin-'+data[i]["ticket_id"]+'">';
								_html += '<h4 class="box-bottom-title">'+data[i]["ticket_title"]+'</h4>';
								_html += '<div class="bar">';
				                _html += '<p class="bar-color" style="width: 0%">0%</p>';
				                _html += '</div>';
				                _html += '<div class="number">';       
								_html += '<div class="number-l">';
				                _html += '<p>已发放</p>';
				                _html += '<p id="hadsold'+data[i]["ticket_id"]+'" class="clear">0名额</p>';
				                _html += '</div>';                
				                _html += '<div class="number-r">'; 
				                _html += '<p>剩余</p>';  
				                _html += '<p id="surplus'+data[i]["ticket_id"]+'" class="stock">'+data[i]["total"]+'名额</p>';       
				                _html += '</div>';
				                _html += '</div>';
				                _html += '<a class="btn_03">已结束</a>';         
				                _html += '</div>';
								if( data[i]["timeout"] == 0 && ("mobile" in data[i]["lottery_user"]) ){
									_html += '<div class="win-data">';
					                _html += '<p class="font_16">恭喜<span class="color_01">'+data[i]["lottery_user"]["user_name"]+'</span> 手机号'+data[i]["lottery_user"]["mobile"]+'</p>';
					                _html += '<p class="font_18">以 <span class="color_01">'+data[i]["selling_price"]+'元</span> 购得现金券！抽奖号码为 <span class="color_01">'+data[i]["lottery_user"]["order_no"]+'</span></p>';
					                _html += '</div>';
								}
								_ele.innerHTML = _html;
								_frag.appendChild(_ele);
								remainingTickets(data[i].ticket_id, data[i].ticket_uuid);
						  }
					  }
					  activityList.appendChild(_frag); 
					  timeFn($('.page-'+page+' .over-time'));
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
					if(data["surplus"] == 0) {
						$("#display"+ticket_id).show();
					}
                    processFn($('.begin-'+ticket_id)); 
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
			var buyUrl = '<?php echo $this->_tpl_vars['site_url']; ?>
/active/oneyuanpurchase';
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