<script type="text/javascript" src="/js/ajaxfileupload.js?t={{$_CONF.WEB_VERSION}}"></script>
<div class="getBtnPopup">
    <h3>{{if $type eq 1}}认领店铺{{elseif $type eq 2}}商户申请{{/if}}<a class="close" id="close">&times;</a></h3>
    <div class="inputList" id="inputList">
        <form method="POST" enctype="multipart/form-data" id="myupload">
        <p><label class="label">姓名：</label><input name="full_name" id="full_name" type="text"  class="txt" value=""/></p>
        <p><label class="label">手机号码：</label><input name="phone_number" id="phone_number" type="text" class="txt"/></p>
        <p><label class="label fileLabel">身份证照片：</label><span class="filePic"><input name="fileToUpload" id="id_img"  type="file" class="file" onChange="document.getElementById('idcard').innerHTML=this.value"/></span></p>
        <p class="fileName"><span id="idcard"></span></p>
        <p><label class="label fileLabel">营业执照：</label><span class="filePic"><input name="fileToUpload" id="bus_img" type="file" class="file" onChange="document.getElementById('business').innerHTML=this.value"/></span></p>
        <p class="fileName"><span id="business"></span></p>
        <p class="error" id="ts"></p>
        <p class="submit"><input type="button" value="提交申请"  onClick="return fnInput();"></p>
        </form>
    </div> 
</div>
<script type="text/javascript">
var ty = {{$type}}, sid = {{$sid}};
$('#close').on('click',function(){
	$('#popup_bus').remove();
});

function fnInput() {
	var oInputList = document.getElementById('inputList'),
	sHtml = oInputList.innerHTML;
	var full_name =  $('#full_name').val();
	var phone_number = $('#phone_number').val();
	var id_img = $('#id_img').val(); 
	var bus_img = $('#bus_img').val(); 
	var reg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
	if (full_name == '') {
	$('#ts').html('请填写姓名');
		return false;
	}

	if (phone_number == '') {
		$('#ts').html('请填手机号码');
		return false;
	}
	
	if (!reg.test(phone_number)){
		$('#ts').html('请填写正确的手机号码');
		return false;
	}
	
	if (id_img == '') {
		$('#ts').html('请选择个人身份证照片');
		return false;
	}
	
	if (bus_img == '') {
		$('#ts').html('请选择营业执照照片');
		return false;
	}
	$.ajaxFileUpload
	(
		{
			url:'/home/shop/add-veriy',
			secureuri:false,
			fileElementId:'fileToUpload',
			dataType: 'json',
			data:{full_name:full_name, phone_number:phone_number, ty:ty, sid:sid},
			success: function (json) {
				if (json.status == 100){
					oInputList.innerHTML = '<div class="loginTxt">'+json.msg+'</div>';
					setTimeout(function(){
						document.getElementById('popup_bus').remove();
						oInputList.innerHTML = sHtml;
					},1500)
				} else {
					oInputList.innerHTML = '<div class="loginTxt">'+json.msg+'</div>';
				}
			}
		}
	)
}
</script>