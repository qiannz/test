<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<meta name="description" content="">
<meta name="keywords" content="">
<title>名品导购会员中心</title>
<link href="/css/active/oneyuanpurchase/common.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="viewport bg_05">
        <!--返回首页-->
        <div class="login-box">
            <h3 class="login-title">登录</h3>
            <div class="login-body">
                <p class="login-row">
                    <input type="text" name="mobile" id="mobile" placeholder="输入手机号码" maxlength="11" class="input width_100"/>
                </p>
                <p id="mobile_error" class="error"></p>
                <p class="login-row">
                    <input type="text" name="code" id="code" placeholder="输入动态验证码" maxlength="4" class="input width_55"/>
                    <input type="button" id="getCode" value="获取验证码" class="yzm-on"/>
                </p>
                <p class="error" id="code_error"></p>
                <input type="button" id="login" class="login-btn" value="确认登录"/>
            </div>
        </div>
   </div>
	<script src="http://mpimg.cn/jquery-1.10.2.min.js" ></script>
	<script>
	var bStop = true
	$("#getCode").attr('disabled', false);
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
                _this.btn.value = '30秒';
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
                _this.btn.value = (29-iTime)+'秒';
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
        var reg = /^1[0-9]{10}$/;
        if(reg.test(mobile)){
            return true;
        }else{
            return false;
        }
    }
	
	//发送验证码
    function sendCode(mobile){
		var mobile = $("#mobile").val();
		if (!checkMobile(mobile)) {
			$("#mobile_error").html('请输入正确的手机号码');
			return false;
		} else {
            $.ajax({
                type:'POST',
                url:'/active/oneyuanpurchase/send-code',
                data:{mobile:mobile},
                dataType:'json',
                success:function(data){
                    if(data.res == 100){
                    	fnEvent('getCode');
                        $("#code_error").html('验证发送成功');
                    }else if (data.res == 101) {
                    	$("#mobile_error").html('请输入正确的手机号码');
                    }
                }
            });
        }
    }
		
	 //验证登录
    function verifyMobileCode(mobile,code){	
        var jumpfrom = "{{$jumpfrom}}";
		if(!checkMobile(mobile)){
            $('#mobile_error').html('请输入正确的手机号码');
            return false;
        } else if(!code){
            $("#code_error").html('验证码不能为空');
            return false ;
        }else{
            $.ajax({
                type:'POST',
                url:'/active/oneyuanpurchase/verify-mobile-code',
                data:{mobile:mobile, code:code, jumpfrom:jumpfrom,tuid:"{{$tuid}}", platform:"{{$platform}}"},
                dataType:'json',
                success:function(data){
                    if(data.res == 101){
                        $('#mobile_error').html('请输入正确的手机号码');
                    }else if(data.res == 102 || data.res == 104 || data.res==103){
                        $("#code_error").html('验证码错误');
                    }else if(data.res == 105){
                    	$("#code_error").html('登录失败');				
                    }else if(data.res == 100){
                        window.location.href = data.extra;
                        /*if( jumpfrom == "spike" ){
                        	window.location.href = '/active/comeandgrap';	
                        }else{
                        	window.location.href = '/active/oneyuanpurchase';	
                        }*/			
                    }
                }
            })
        }
    }
	
	$(function(){
		$("#getCode").click(function(){
			var mobile = $.trim($("#mobile").val());
			sendCode(mobile);
		});
		
		$("#login").click(function(){
			var mobile = $.trim($("#mobile").val());
			var code   = $.trim($("#code").val());
			verifyMobileCode(mobile,code);
    	});
	
	});
	</script>
</body>
</html>
