<script type="text/javascript" src="/js/autocomplete/autocomplete.js" charset="utf-8"></script>
<form action="{{$_CONF.ACTION_FORM}}" method="post">
<div class="addshopPopup">
    <h2>新增店铺<a class="close" href="javascript:void(0)" id="addShopClose">&times;</a></h2>
    <div class="upFileBox">
    <p class="inputBox"><label class="label">店铺名称：</label><input type="text" class="text backTitInput" name="sname" id="sname" value="" placeholder="请输入店铺名称" /></p>
    <p class="error"><span id="sname_error"></span></p>
    <p class="selectBox clearfix">
    <label class="label">店铺地址：</label>
    <select name="region_id" id="region_id"></select>
    <select name="circle_id" id="circle_id" class="s2"><option value="">请选择商圈</option></select>
    </p>
    <p class="error"><span class="h2" id="region_id_error"></span><span class="h2" id="circle_id_error"></span></p>
    <p class="inputBox"><input type="text" class="text mInput" name="address" id="address" value="" placeholder="请填写详细地址，如XX路XX号" /></p>
    <p class="error"><span id="address_error"></span></p>
    <p class="inputBox"><label class="label">所属品牌：</label><input type="text" class="text backTitInput" name="bname" id="bname" value="" placeholder="请输入品牌名称" /></p>
    <p class="error"><span id="bname_error"></span></p>
    <p class="selectBox clearfix">
    <label class="label">所属分类：</label>
    <select name="store_id" id="store_id">
        <option value="">请选择分类</option>
        {{foreach from=$storeArray key=key item=item}}
        <option value="{{$key}}">{{$item}}</option>
        {{/foreach}}
    </select>
    </p>
    <p class="error"><span class="h1" id="store_id_error"></span></p>
    <div class="textareabox clearfix">
    <label>店铺公告：</label>
    <div class="textarea textarea-02">
    <textarea class="noticeTextarea" name="notice" id="notice" placeholder="可发布优惠信息等公告内容，100字内"></textarea>
    </div>    
    </div>
    <p class="error"><span class="h1" id="notice_error"></span></p>
    <p class="error"><span class="h1" id="addShopError"></span></p>
    <p class="submitBox" ><input type="button"  class="submit" value="确认提交" id="addShopSubmit"></p>
    </div>
</div>
</form>
<script type="text/javascript">
var isBrandChecked = false;
$(function(){	
	$('#addShopClose').on('click',function(){
		$('#addShopPopup').remove();
		$('.acResults').remove();
	});
	
	$('#bname').autocomplete({ url:"/home/suser/get-brand", onItemSelect:
        function (item) {
            var text = item.value; //文本
            var num = item.data; //数字
            $('#bname').val(text);
        }
        ,cellSeparator:"|"
	});
	$('#sname').on('blur', function(){
		snameValid();
	});
	$('#region_id').on('blur', function(){
		regionIdValid();
	});
	$('#circle_id').on('blur', function(){
		circleIdValid();
	});
	$('#address').on('blur', function(){
		addressValid();
	});
	$('#bname').on('blur', function(){
		bnameValid();
	});
	$('#store_id').on('blur', function(){
		storeValid();
	});
	$('#notice').on('blur', function(){
		noticeValid();
	});
	
	$('#region_id').bind('change', function(){
		var thatFun = arguments.callee;
		var that=this; 
		$(this).unbind("change",thatFun);
		
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('请选择商圈').val(''));
		$.post('/home/good/get-circle', {region_id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
		
		setTimeout(function(){$(that).bind("change", thatFun)},0);
	});	
	
	$('#addShopSubmit').click(function(){
		submitTure();
	});
});

function submitTure() {
	var sname_status = region_status = circle_status = address_status = bname_status = store_status = notice_status = false;
	sname_status = snameValid();
	region_status = regionIdValid();
	circle_status = circleIdValid();
	address_status = addressValid();
	bname_status = bnameValid();
	store_status = storeValid();
	notice_status = noticeValid();
	if(sname_status && region_status && circle_status && address_status && bname_status && store_status && notice_status) {
		$("#addShopSubmit").attr('value', '提交中...').attr('disabled', true);
 		$.ajax({
			url:'/home/suser/add-shop', 
			type:'POST',
			dataType:'json',
			data: {
				sname : $('#sname').val(),
				rid : $('#region_id').val(),
				cid : $('#circle_id').val(),
				ad : $('#address').val(),
				bname : $('#bname').val(),
				stid : $('#store_id').val(),
				not : $('#notice').val()
			}, 
			success: function(json){
				if(json.res == 100) {
					$('#addShopPopup').remove();
					$.dialog({
						title: '提示',
						content: json.msg,
						ok: function () {
							
							location.href = '/home/suser/my-good/sid/' + json.extra.sid;
						},
						cancel:false
					});
				} else if(json.res == 300) {
					$('#addShopError').html(json.msg);
				}
			},
			error: function(){
			}
		});
	}
}

function snameValid() {
	var _this = $('#sname');
	var _msg = '';
	if(_this.val().length == 0) {
		_msg = '请输入店铺名称';
	} else if(_this.val().length > 30) {
		_msg = '店铺名称最多30个字符，汉字算一个字符';
	}
	
	if(_msg == '') {
		$('#sname_error').html('<s class="true"></s>');
	} else {
		$('#sname_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;	
}

function regionIdValid() {
	var _this = $('#region_id');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#region_id_error').html('<s class="false"></s>请选择所在区');
			return false;
		} else {
			$('#region_id_error').html('<s class="true"></s>');
		}
	}
	return true;
}

function circleIdValid() {
	var _this = $('#circle_id');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#circle_id_error').html('<s class="false"></s>请选择商圈');
			return false;
		} else {
			$('#circle_id_error').html('<s class="true"></s>');
		}
	}
	return true;
}

function addressValid() {
	var _this = $('#address');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#address_error').html('<s class="false"></s>请输入店铺名称');
			return false;
		} else {
			$('#address_error').html('<s class="true"></s>');
		}
	}
	return true;		
}

function bnameValid() {
	if(isBrandChecked){
		return true;
	}
	var _this = $('#bname');
	if(_this.val().length == 0) {
		$('#bname_error').html('<s class="false"></s>请输入品牌名称');
		return false;
	} else {
		$.post('/home/suser/check-brand-name', {bname:_this.val()}, function(flag){
			if(flag == 'false') {
				$('#bname_error').html('<s class="false"></s>你输入的品牌不存在');
				return false;
			} else {
				$('#bname_error').html('<s class="true"></s>');
				isBrandChecked = true;
				return true;
			}
		});	
	}
}

function storeValid() {
	var _this = $('#store_id');
	if(_this.val().length == 0) {
		$('#store_id_error').html('<s class="false"></s>请选择所属分类');
		return false;
	} else {
		$('#store_id_error').html('<s class="true"></s>');
	}
	return true;	
}

function noticeValid() {
	var _this = $('#notice');
	var _msg = '';
	
	if(_this.val().length > 0) {
		if(_this.val().length > 100) {
			_msg = '店铺公告最多100个字符，汉字算一个字符';
		}
	
		if(_msg == '') {
			$('#notice_error').html('<s class="true"></s>');
		} else {
			$('#notice_error').html('<s class="false"></s>' + _msg);
			return false;
		}
	}
	return true;
}

function loadRegion() {
	var _this = $('#region_id');
	_this.append($("<option>").text('请选择所在区').val(''));
	$.post('/home/good/get-region', {}, function(obj){
		var data = eval('(' + obj + ')');
		$.each(data, function(i, s){
			_this.append($("<option>").text(s.name).val(s.id));
		});
	});
}
loadRegion();
</script>