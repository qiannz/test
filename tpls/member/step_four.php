<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css?t={{$_CONF.WEB_VERSION}}"  />
<script type="text/javascript" src="/js/autocomplete/autocomplete.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<div class="shop-enter">
      	<h2 class="shop-enter-tit">商户入驻</h2>
        <!--菜单-->
        <div class="shop-enter-menu">
        	<ul>
            	<li><a>1.注册成为名品导购网会员</a></li>
                <li><a>2.填写商户信息</a></li>
                <li><a>3.经过审核，通过认证</a></li>
                <li class="selbg"><a>4.补全资料，完成入驻</a></li>
            </ul>
        </div>
        <!--内容-->
        <div class="shop-enter-con">
        	<h2 class="register-step-tit">4.补全资料，完成入驻</h2>
            <form action="{{$_CONF.FORM_ACTION}}" method="post" enctype="multipart/form-data" id="stepFourForm">
            <input type="hidden" name="step" value="{{$step}}" />
            <input type="hidden" name="sid" value="{{$sid}}" />
            <div class="shop-enter-form">
                	<p><label class="shop-enter-label">所属分类</label>
                    	<select name="store_id" id="store_id" class="shop-enter-select">
                			 <option value="">请选择分类</option>
                            {{foreach from=$storeArray key=key item=item}}
                            <option value="{{$key}}" {{if $memberRow && $memberRow.store_id eq $key}}selected="selected"{{/if}}>{{$item}}</option>
                            {{/foreach}}
                        </select></p>
                    <p class="shop-enter-error"><span id="store_id_error"></span></p>
                    
                    <p><label class="shop-enter-label">主营品牌</label><input type="text" class="shop-enter-text" name="bname" id="bname" value="{{$memberRow.brand_name}}" /></p>
                    <p class="shop-enter-error"><span id="bname_error"></span></p>
                    
                    <p>
                    <label class="shop-enter-label">品牌授权书</label>
                    <span class="shop-enter-filepic"><input type="file" name="brand_img" id="brand_img" class="shop-enter-file" onChange="$('#idcard').html($(this).val())"></span>
                    <span class="shop-enter-prompt" id="idcard"></span>
                    </p>
                    <p class="shop-enter-error"><span id="brand_img_error"></span></p>
                    
                    <div><label class="shop-enter-label">选择套餐</label>
                    	<ul class="radio-list">
                        {{foreach from=$packArray key=key item=item}}
                        <li>
                        <input type="radio" name="pack_id" value="{{$item.pack_id}}" class="shop-enter-radio" {{if $memberRow && $memberRow.pack_id eq $item.pack_id}} checked="checked"{{elseif $item.is_default eq 1}}checked="checked"{{/if}} />
                        <label class="shop-radio-label">{{$item.pack_name}}</label></li>
                        {{/foreach}}
                        </ul>
                    </div>
                    
                    <p>
                    <label class="shop-enter-label">结算账户</label><input type="text" name="alipay_acount" id="alipay_acount" class="shop-enter-text" value="{{$memberRow.alipay_acount}}" placeholder="请输入支付宝账户名" />
                    <label class="shop-enter-label">账户户主姓名</label><input type="text" name="alipay_name" id="alipay_name" class="shop-enter-text" value="{{$memberRow.alipay_name}}" placeholder="请输入该账户户主姓名" />
                    </p>
                     <p class="shop-enter-error"><span id="alipay_acount_error"></span><span id="alipay_name_error"></span></p>
            </div>
            </form>
            <div class="shop-enter-agreement">
                <h4>名品街商户合作协议：</h4>
                <p>1、请仔细核对你的选购商品信息，一旦成功不能更改</p>
                <p>2、超级购商品活动期间你未兑换，活动结束后七个工作日将自动退款</p>
                <p>3、秒杀商品购买时请仔细选购，一经付款不退不换</p>
            </div>
            <input type="button" class="shop-enter-agree" value="同意本合作协议并提交申请" onClick="submit_check()"/>
        </div>
      </div>

<script type="text/javascript">
var validArray = ['store_id', 'bname', 'brand_img', 'alipay_acount', 'alipay_name'];
var isBrandChecked = 0;
$("form#stepFourForm .shop-enter-agree").attr('disabled', false); 
$(function(){
	$('#bname').autocomplete({ url:"/home/member/get-brand", onItemSelect:
        function (item) {
            var text = item.value; //文本
            var num = item.data; //数字
            $('#bname').val(text);
        }
        ,cellSeparator:"|"
	});
	
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
		$("form#stepFourForm").submit();
		$("form#stepFourForm .shop-enter-agree").attr('value', '申请提交中...').attr('disabled', true);		
	}
	return false;
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'store_id':
				if($('#' + id).val().length == 0) {
					_msg = '请选择所属分类';	
				}
			break;
		case 'bname':
				bnameValid();
			break;
		case 'brand_img':
				if($('#' + id).val().length == 0) {
					_msg = '请上传品牌授权书';	
				}			
			break;
		case 'alipay_acount':
				if($('#' + id).val().length == 0) {
					_msg = '请输入支付宝账户名';	
				}
			break;
		case 'alipay_name':
				if($('#' + id).val().length == 0) {
					_msg = '请输入账户户主姓名';	
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

function bnameValid() {
	if(isBrandChecked == 1){
		return true;
	}
	var _this = $('#bname');
	if(_this.val().length == 0) {
		$('#bname_error').html('请输入品牌名称');
		return false;
	} else {
		$.post('/home/member/check-brand-name', {bname:_this.val()}, function(flag){
			if(flag == 'false') {
				$('#bname_error').html('你输入的品牌不存在');
				return false;
			} else {
				$('#bname_error').html('');
				isBrandChecked = 1;
				return true;
			}
		});	
	}
}
</script>