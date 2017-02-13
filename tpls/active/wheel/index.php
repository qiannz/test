<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>欢乐大转盘-名品导购网</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
</head>
<body>
	<div class="loading"></div>
	 <div class="wrap" style="display:none">
		
		<div class="top-banner">
			<img data-lazyload="/images/act/wheel/top-banner.jpg" src="/images/act/wheel/blank.png">
		</div>
		<div class="game-box">
        	<a class="look-more" style="font-size:14px;color:#feee00; position:absolute;right:11%;bottom:0; z-index:3" href="/active/wheel/my-wheel" target="_blank">查看明细</a>
			<div class="game-con">
				<span class="start"></span>	
				<span class="arrow"></span>
				<img data-lazyload="/images/act/wheel/wheel-03.png" src="/images/act/wheel/blank.png" class="wheel-03" >
				<img data-lazyload="/images/act/wheel/wheel-02.gif" src="/images/act/wheel/blank.png" class="wheel-02" >
					
			</div>
			<div class="game-bg">
				<img data-lazyload="/images/act/wheel/wheel-01.jpg" src="/images/act/wheel/blank.png">	
			</div>
			
		</div>
	
			<div class="star-con">		
				 <div class="star-box">
					 <div class="star-top">
						<div class="star-top-name">您的幸运星是：</div>
						<div class="star-top-num"><span class="star-pic"></span>+<span class="star-num"></span></div>
					</div>
					<a class="get-star" href="http://m.mplife.com/help/street/140928/212441401201.shtml" target="_blank">获取更多幸运星</a> 
				</div> 
				<img data-lazyload="/images/act/wheel/star-img.png" src="/images/act/wheel/blank.png" class="star-bg">
		</div>
		<div class="bottom-img">
			<img data-lazyload="/images/act/wheel/rule-img-01.png" src="/images/act/wheel/blank.png">
			<img data-lazyload="/images/act/wheel/rule-img-02.png" src="/images/act/wheel/blank.png">
		</div>
		
    </div>
	<!--中奖弹窗-->
        
		<div class="lucky-popup" style="display:none" id="link-login">
			<div class="popup-box">
				 <p class="lucky-txt" style="text-align:left" ><label>手　机：</label><input type="text" name="mobile" id="mobile" placeholder="输入手机号码" /></p>
                 <p style="font-size:12px;color:#000; text-align:center;height:24px;padding-top:5px;" id="mobile-error"></p>
     			 <p class="lucky-txt" style="text-align:left" ><label>验证码：</label><input type="text" style="width:6em" name="code" id="code" maxlength="4" placeholder="输入验证码" />
                 <input type="button" class="btn-on" value="获取验证码" id="getCode" style="font-size:0.75em"></p>
                  <p style="font-size:12px;color:#000;text-align:center;height:24px;padding-top:5px;" id="code-error"></p>
				<p class="popup-btn">
					<input type="button" class="btn-on" value="绑定手机" id="bind" />
				</p>
			</div>
		</div>
                        
	<!--中奖弹窗-->
		<div class="lucky-popup" style="display:none" id="winning-tips">
			<div class="popup-box">
                <p class="lucky-txt" id="msg"></p>
				<p class="popup-btn">
					<input type="button" class="btn-on" value="确定" onclick="winningTips()">
				</p>
			</div>
		</div>
	<!--消耗星星-->
	<div class="lucky-popup" style="display:none" id="scratch-card-tips">
			<div class="popup-box">
				<p class="lucky-txt">需消耗1个<img data-lazyload="/images/act/wheel/star2.gif">是否确认抽奖？</p>
				<p class="popup-btn">
					<input type="button" class="btn-on" value="确定" onClick="beginToScratch()" id="beginToScratch">
					<input type="button" class="btn-off" value="取消" onclick="$('#scratch-card-tips').css('display','none')">
				</p>
			</div>
		</div>
		
<script type="text/javascript">
document.domain = 'mplife.com';
//系统判断
var _w = window.navigator.userAgent;
var is_login  = {{$is_login}};
var is_bind  = {{$is_bind}};
var mp_app_user_id = $.cookie('MP_APP_USER_ID');
/*大转盘*/
var	_iNum = 0;
var	_sum = 360*2;
var	_deg = 25;
var	timer = null;
var	_bStop = true;
var bStop = true;
//初始化
var _start = $('.start');
var	_arrow = $('.arrow');
			
$(function(){
	if(!is_login || !mp_app_user_id) {
		$("body").append('<div style="background:rgba(0,0,0,0.8);position:fixed;top:0;	left:0;width:100%;height:150%;z-index:999;"></div>');
	}
	
	if(!is_bind) {
		$('#link-login').show();
	}
	
	starDis({{$user.star}});
	
	setDegree(_arrow,0);
	
	//事件
	_start.click(function(e){
		if(!is_login) {
			//e.preventDefault();
			return false;
		}
		
		if(!is_bind) {
			$('#link-login').show();
			return false;
		}
					
		if(_bStop){			
			//重置手机框
			$("#winning-tips-phone").hide();
			//重置刮奖动作
			$("#beginToScratch").attr('value', '确定').attr('disabled', false);
			
			$.ajax({
				url:"/active/wheel/is-can-scratch",
				dataType:"json",
				data:{},
				success:function(obj){
					if(obj.res == 100){
						$("#scratch-card-tips").show();
					} else {
						alert("抱歉，幸运星不够啦");
					}
				},
				error:function(){
					
				}
			});
		}
	});
	
	if((/android/i).test(_w)){
			loadcss('/css/wheel/android.css');
		}else{
			loadcss('/css/wheel/wap.css');
		}
	/*load*/	
	var imglist = document.getElementsByTagName('img'),
		relist = [],
		arrsrc = [],
	    iCur = 0,
		img = new Image();
	//重置
	for(var i=0;i<imglist.length;i++){
		if(imglist[i].getAttributeNode("data-lazyload")){
			arrsrc.push(imglist[i].getAttribute('data-lazyload'));
			relist.push(imglist[i])
		}
	}	
	relist[iCur].src= img.src = arrsrc[iCur];
	img.onload = function(){
			iCur++;
			if(iCur<arrsrc.length){
				img.src = arrsrc[iCur];
				relist[iCur].src = img.src;
			}else if(iCur >=arrsrc.length){
				$('.loading').css({'display':'none'});
				$('.wrap').css({'display':'block'});
			}
	}
	
	$("#getCode").click(function(){
		var mobile = $.trim($("#mobile").val());
		sendCode(mobile);
	});
	
	$("#bind").click(function(){
		var mobile = $.trim($("#mobile").val());
		var code   = $.trim($("#code").val());
		verifyMobileCode(mobile,code);
	});	
});

function starDis(n) {
	$('.star-pic').html('');
	var _len = n, j = 0;
	$('.star-num').text(_len);
	while(j<_len && j<5){
		_html = $('<img src="/images/act/wheel/star.png" >');
		$('.star-pic').append(_html);
		j++;
	}
}

function isBindMobile(uname) {
	$.ajax({
			url:"/active/wheel/is-bind-mobile",
			dataType:"json",
			data:{"uname":uname},
			success:function(obj){
				if(obj.res == 100){
					starDis(obj.extra);
				} else {
					is_bind = false;
					$("#mobile-bind").show();
				}
			},
			error:function(){
				
			}
		});		
}

//css
function setDegree(obj,deg){  
	obj.css({  
		'transform':     'rotate('+deg+'deg)',  
		'-moz-transform':'rotate('+deg+'deg)',  
		'-o-transform':  'rotate('+deg+'deg)',
		'-webkit-transform':  'rotate('+deg+'deg)'  	
	});  
} 

// 大转盘
function beginGame(n,star,msg){
		var i = 0;
		switch(n){
			case 1: iNum = _sum + 15;
					break;
			case 2:	iNum = _sum + (n*30-15);
					break;
			case 3:	iNum = _sum + (n*30-15);
					break;
			case 4:	iNum = _sum + (n*30-15);
					break;
			case 5:	iNum = _sum + (n*30-15);
					break;
			case 6:	iNum = _sum + (n*30-15);
					break;
			case 7:	iNum = _sum + (n*30-15);
					break;
			case 8:	iNum = _sum + (n*30-15);
					break;
			case 9:	iNum = _sum + (n*30-15);
					break;
			case 10:iNum = _sum + (n*30-15);
					break;
			case 11:iNum = _sum + (n*30-15);
					break;
			case 12:iNum = _sum + (n*30-15);
					break;
			default:iNum =0;
		}			

		timer = setInterval(function(){
			if(i<=iNum)
			{
				setDegree(_arrow,i);
				i+=15;
			}else{
				clearInterval(timer);
				_bStop = true;
				i=0;
				//幸运星显示
				starDis(star);				
				//中奖提示
				setTimeout(function(){
				$("#winning-tips #msg").html(msg);
				$("#winning-tips").show();
				}, 500);
			}
		},5);
		
	
}
	
function loadcss(filename){
	 var fileref = document.createElement('link');
			fileref.setAttribute("rel","stylesheet");
			fileref.setAttribute("type","text/css");
			fileref.setAttribute("href",filename);
			document.getElementsByTagName('head')[0].appendChild(fileref);
}
	
function beginToScratch() {	
	$("#beginToScratch").attr('value', '刮奖中...').attr('disabled', true);
	$.ajax({
		url:"/active/wheel/start-scratch",
		dataType:"json",
		data:{},
		success:function(obj){
			if(obj.res == 100){
				$("#scratch-card-tips").hide();
				//开始转
				beginGame(obj.extra.win, obj.extra.star, obj.extra.msg);
				_bStop = false;
				
			} else {
				alert("抱歉，刮奖功能暂停");
			}
		},
		error:function(){
			
		}
	});			
}

function winningTips() {
	$("#winning-tips").hide();
}


function CountDown(json)
{
	if(!(this instanceof CountDown))
		return new CountDown(json)
	this.btn = document.getElementById(json.id);
	this.setFn = json.setFn;
	this.timer = null;
	this.oEvent();
}
CountDown.prototype = {
	oEvent:function(){
		var _this = this;
		if(bStop)
		{
			//_this.setFn();
			bStop = false;
			_this.btn.value = '30秒后可重新获取';
			_this.btn.className = 'yzm-off';
			_this.btn.disabled = true;
			_this.Time();
		}

	},
	Time:function(){
		var _this = this;
		var iTime = 0;
		clearInterval(this.timer)
		this.timer = setInterval(function(){
			_this.btn.value = (29-iTime)+'秒后可重新获取';
			iTime++;
			if(iTime>30)
			{
				clearInterval(_this.timer);
				_this.btn.value = '获取验证码';
				_this.btn.className = 'yzm-on';
				_this.btn.disabled = false;
				bStop = true;
			}
		},1000)
	}
}

function fnEvent(id){
	var obj = document.getElementById(id),
		ClickEvent= new CountDown({id:id});

	obj.onclick = function(){

		ClickEvent.oEvent()
	}
}

//手机正则
function checkMobile(mobile){
	var reg = /^1[2-9][0-9]{9}$/;
	if(reg.test(mobile)){
		return true;
	}else{
		return false;
	}
}

//发送验证码
function sendCode(mobile){
	if(!checkMobile(mobile)){
		$('#mobile-error').html('请输入正确的手机号码');
		return false;
	}else{
		fnEvent('getCode');
		$.ajax({
			type:'POST',
			url:'/active/wheel/send-code',
			data:{mobile:mobile},
			dataType:'json',
			success:function(data){
				if(data.res == 100){
					$("#mobile-error").html('验证发送成功');
				} else if(data.res == 200){
					alert('你已经参加过第一次活动，不能继续参加第二次活动');
				} else{
					$("#mobile-error").html('验证发送失败');
				}
			}
		});
	}
}

//验证登录
function verifyMobileCode(mobile,code){
	if(!checkMobile(mobile)){
		$('#mobile-error').html('请输入正确的手机号码');
		return false;
	}
	if(!code){
		$("#code-error").html('验证码不能为空');
		return false ;
	}else{
		$.ajax({
			type:'POST',
			url:'/active/wheel/verify-mobile-code',
			data:{mobile:mobile , code:code},
			dataType:'json',
			success:function(data){
				if(data.res == 101){
					$('#mobile-error').html('请输入正确的手机号码');
				}else if(data.res == 102 || data.res == 104){
					$("#code-error").html('验证码错误');
				}else if(data.res == 105){
					$("#code-error").html(data.msg);
				}else if(data.res == 100){
					$("#link-login").hide();
					alert('绑定成功');
				}
			}
		})
	}
}
</script>	
</body>
</html>