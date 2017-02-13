{{include file='active/voucher/header.php'}}
<body>
<div class="viewport">
       <div class="top-pic">
            <canvas width="186" height="186" id="canvas"></canvas>
       </div>
       <div id="content">
            <div class="wx-box">
                <p class="font_40 yellow">我是（{{$userInfo.user_name}}）</p>
                <p class="font_45">送了您一个名品街红包</p>
            </div>
            <div class="login-wrap">
                    <p class="login-col">
                    <input type="text" name="mobile" id="mobile" maxlength="11" placeholder="输入手机号码，红包自动放入账户" class="input-text width_100"></p>
                    <p class="error" id="mobile_error"></p>
                    <p class="login-col">
                        <input type="text" name="code" id="code" maxlength="4" placeholder="输入验证码" class="input-text width_55">
                        <input type="button" value="获取验证码" class="yzm-on" id="getCode">
                    </p>
                    <p class="error" id="code_error"></p>
                    <input type="button" value="打 开 红 包" class="submit" id="login">
             </div>

            <a class="rule-btn"  onclick="$('#rulePop').show()"></a>
        </div>
</div>

<div class="float-wrap">
    <div class="float"><a href="http://go.mplife.com/5"><img src="/images/active/voucher/float.png"></a></div>
</div>


<!--<div class="pop pop-bg" style="display:none" id="rulePop">
    <div class="share-pop" onClick="$(this).parent().hide()">
        <a href="http://go.mplife.com/5"><img src="/images/active/voucher/rule.png" /></a>
    </div>
</div>-->

<div class="pop pop-bg" style="display: none"  id="rulePop">
    <div class="share-pop">
        <a href="http://go.mplife.com/5"><img src="/images/active/voucher//rule_01.png" /></a>
        <a onClick="$(this).parent().parent().hide()"><img src="/images/active/voucher//rule_02.png" /></a>
    </div>
</div>
<input type="hidden" id="uid" value="{{$userInfo.user_id}}" />
<input type="hidden" id="order_no" value="{{$output.order_no}}" />
<script type="text/javascript">
    var uid = $("#uid").val();
    var order_no = $("#order_no").val();
    var bStop = true
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
            $('#mobile_error').html('请输入正确的手机号码');
			$("#getCode").attr('disabled', false);
            return false;
        }else{
            $.ajax({
                type:'POST',
                url:'/active/voucher/send-code',
                data:{uid:uid, mobile:mobile, order_no:order_no},
                dataType:'json',
                success:function(data){
                    if(data.res == 100){
                        fnEvent('getCode');
                        $("#code_error").html('验证发送成功');
                    }else if (data.res == 101) {
                        $("#code_error").html('请输入正确的手机号码！');
						$("#getCode").attr('disabled', false);
                    }else if(data.res == 102) {
                        $("#content").html('<div class="txt_02"><p>来晚啦！</p><p>红包已经抢完咯</p></div><div class="weep-cat"><p class="weep"></p></div>');
                    }
                }
            });
        }
    }
        
     //验证登录
    function verifyMobileCode(mobile,code){	
        if(!checkMobile(mobile)){
            $('#mobile_error').html('请输入正确的手机号码');
			$("#login").attr('disabled', false);
            return false;
        }else if(!code){
            $("#code_error").html('验证码不能为空');
			$("#login").attr('disabled', false);
            return false ;
        }else{
            $.ajax({
                type:'POST',
                url:'/active/voucher/verify-mobile-code',
                data:{uid:uid, mobile:mobile, order_no:order_no, code:code},
                dataType:'json',
                success:function(data){
					$("#login").attr('disabled', false);
                    if(data.res == 101){
                        $('#code_error').html('请输入正确的手机号码');
                    }else if(data.res == 102 || data.res == 104){
                        $("#code_error").html('验证码错误');
                    }else if(data.res == 100){
                        $("#content").html(data.extra);
                    }else if(data.res == 106){
                        alert(data.msg);				
                    }else if(data.res == 105){
						 $("#content").html('<div class="txt_02"><p>来晚啦！</p><p>红包已经抢完咯</p></div><div class="weep-cat"><p class="weep"></p></div>');
					}
                }
            })
        }
    }
    
    $(function(){
		$("#getCode, #login").attr('disabled', false);

        $("#getCode").click(function(){
			$(this).attr('disabled', true);
            var mobile = $.trim($("#mobile").val());
            sendCode(mobile);
        });
        
        $("#login").click(function(){
			$(this).attr('disabled', true);
            var mobile = $.trim($("#mobile").val());
            var code   = $.trim($("#code").val());
            verifyMobileCode(mobile,code);
        });
    
    });

</script>
</body>
</html>