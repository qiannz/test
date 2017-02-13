<div class="shop-enter">
      	<h2 class="shop-enter-tit">商户入驻</h2>
        <!--菜单-->
        <div class="shop-enter-menu">
        	<ul>
            	<li><a>1.注册成为名品导购网会员</a></li>
                <li class="selbg"><a>2.填写商户信息</a></li>
                <li><a>3.经过审核，通过认证</a></li>
                <li><a>4.补全资料，完成入驻</a></li>
            </ul>
        </div>
        <!--内容-->
        <div class="shop-enter-con">
        	<h2 class="register-step-tit">2.填写商户申请</h2>
            <form action="{{$_CONF.FORM_ACTION}}" method="post" enctype="multipart/form-data" id="stepTwoForm">
            <input type="hidden" name="step" value="{{$step}}" />
            <input type="hidden" name="sid" value="{{$sid}}" />
            <div class="shop-enter-form">
                	<p>
                    <label class="shop-enter-label">姓名</label>
                    <input type="text" class="shop-enter-text" name="real_name" id="real_name" value="{{$memberRow.real_name}}" />
                    <label class="shop-enter-label">手机号码</label>
                    <input type="text" class="shop-enter-text" name="mobile" id="mobile" value="{{$memberRow.mobile}}" />
                    </p>
                    <p class="shop-enter-error">
                    <span id="real_name_error"></span>
                    <span id="mobile_error"></span>
                    </p>
                    
                    <p>
                    <label class="shop-enter-label">店铺名称</label>
                    <input type="text" class="shop-enter-longtext" name="shop_name" id="shop_name" value="{{if $sid}}{{$shopRow.shop_name}}{{else}}{{$memberRow.shop_name}}{{/if}}" />
                    </p>   
                    <p class="shop-enter-error"><span id="shop_name_error"></span></p>
                    
                    <p>
                    <label class="shop-enter-label">店铺地址</label>
                    <input type="text" class="shop-enter-longtext" name="shop_address" id="shop_address" value="{{if $sid}}{{$shopRow.shop_address}}{{else}}{{$memberRow.shop_address}}{{/if}}" />
                    </p>
                    <p class="shop-enter-error"><span id="shop_address_error"></span></p>
                    
                    <p>
                    <label class="shop-enter-label">身份证照片</label>
                    <span class="shop-enter-filepic">
                    <input  type="file" name="id_img" id="id_img" class="shop-enter-file" onChange="$('#idcard').html($(this).val())"></span>
                    <span class="shop-enter-prompt"  id="idcard"></span>
                    </p>
                    <p class="shop-enter-error"><span id="id_img_error"></span></p>
                    
                    <p>
                    <label class="shop-enter-label">营业执照</label>
                    <span class="shop-enter-filepic">
                    <input  type="file" name="bus_img" id="bus_img" class="shop-enter-file" onChange="$('#license').html($(this).val())"></span>
                    <span class="shop-enter-prompt" id="license"></span>
                    </p>
                    <p class="shop-enter-error"><span id="bus_img_error"></span></p>
            </div>
            <div class="shop-enter-agreement">
                <h4>名品街商户合作协议：</h4>
                <p>1、请仔细核对你的选购商品信息，一旦成功不能更改</p>
                <p>2、超级购商品活动期间你未兑换，活动结束后七个工作日将自动退款</p>
                <p>3、秒杀商品购买时请仔细选购，一经付款不退不换</p>
            </div>
            <input type="button" class="shop-enter-agree" value="同意本合作协议并提交申请" onClick="submit_check()"/>
            </form>
        </div>
      </div>
<script type="text/javascript">
var validArray = ['real_name', 'mobile', 'shop_name', 'shop_address', 'id_img', 'bus_img'];
$("form#stepTwoForm .shop-enter-agree").attr('disabled', false); 
$(function(){
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
});


function submit_check() {
	var len = 0;
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}
	if(len == validArray.length) {
		$("form#stepTwoForm").submit();
		$("form#stepTwoForm .shop-enter-agree").attr('value', '申请提交中...').attr('disabled', true);		
	}
	return false;
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'real_name':
				if($('#' + id).val().length == 0) {
					_msg = '请输入真实姓名';	
				}
			break;
		case 'mobile':
				if($('#' + id).val().length == 0) {
					_msg = '请输入手机号码';	
				} else if(!/1[0-9]{10}$/.test($('#' + id).val())) {
					_msg = '请输入正确的手机号码';
				}
			break;
		case 'shop_name':
				if($('#' + id).val().length == 0) {
					_msg = '请输入店铺名称';	
				}			
			break;
		case 'shop_address':
				if($('#' + id).val().length == 0) {
					_msg = '请输入店铺地址';	
				}
			break;
		case 'id_img':
				if($('#' + id).val().length == 0) {
					_msg = '请上传身份证照片';	
				}
			break;
		case 'bus_img':
				if($('#' + id).val().length == 0) {
					_msg = '请上传营业执照';	
				}
			break;
	}
	if(_msg == '') {
		$('#' + id + '_error').html('');
	} else {
		$('#' + id + '_error').html(_msg);
		return false;
	}
	return true;	
}

</script>