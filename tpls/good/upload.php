<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/ny.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/postImage.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
<script type="text/javascript">
var postImage, uploadSwf;
$(function(){
	FnHover('allBtn','allBox');
	postImage=new bbsPostImage();
	uploadSwf = new SWFUpload({
        upload_url: "/home/good/upload",
        file_size_limit : "1024 KB",
        file_types : "*.jpg;*.gif;*.png",
        file_types_description : "All Files",
        file_upload_limit : "10",
        file_queue_limit : "10",
        file_post_name : "uploadFile",
		post_params: {"user_name" : "{{$user.user_name}}"},
        file_dialog_start_handler : fileDialogStart,
        file_queued_handler : fileQueued,
        file_queue_error_handler : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        upload_start_handler : uploadStart,
        upload_progress_handler : uploadProgress,
        upload_error_handler : uploadError,
        upload_success_handler : uploadSuccess,
        upload_complete_handler : uploadComplete,

        // Button Settings
        button_image_url : "/images/upload.png",
        button_placeholder_id : "file",
        button_width: 137,
        button_height: 26,
        button_cursor: SWFUpload.CURSOR.HAND,
        button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
        // Flash Settings
        flash_url : "/js/swfupload/swfupload.swf",
        // Debug Settings
        debug: false
    });	
	
    $('#good_name').blur(function(){
		goodNameValid();
	});
	
	$('#dis_price').blur(function(){
		disPriceValid();
	});
	
	$('#list_price').blur(function(){
		listPriceValid();
	});
	
	$('#region_id').blur(function(){
		regionIdValid();
	});
	
	$('#circle_id').blur(function(){
		circleIdValid();
	});
	
	
	$('#shop_id').live('blur', function(){
		shopIdValid();
	});	
	
	$('#shop_name').live('blur', function(){
		shopNameValid();
	});
	
	$('#address').live('blur', function(){
		addressValid();
	});
	
});

function check_submit() {	
	var good_status = dis_status = list_status = region_status = circle_status = shop_id_status = shop_name_status = img_status = false;	
	good_status = goodNameValid();
	dis_status = disPriceValid();
	list_status = listPriceValid();
	region_status = regionIdValid();
	circle_status = circleIdValid();
	shop_id_status = shopIdValid();
	shop_name_status = shopNameValid();
	addressValid();
	$('#img').val(postImage.getCache().join(','));
	img_status = imgValid();	
	if(good_status && dis_status && list_status && region_status && circle_status && shop_id_status && shop_name_status && img_status) {
		$("form#signupForm").submit();
		$("form#signupForm .submit").attr('value', '提交中...').attr('disabled', true);
	}
}

function goodNameValid() {
	var _this = $('#good_name');
	var _msg = '';
	if(_this.val().length == 0) {
		_msg = '请输入商品标题';
	} else if(_this.val().length > 30) {
		_msg = '商品标题最多30个字符，汉字算一个字符';
	}
	
	if(_msg == '') {
		$('#good_name_error').html('<s class="true"></s>');
	} else {
		$('#good_name_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;
}

function disPriceValid() {
	var _this = $('#dis_price');
	var _msg = '';
	if(_this.val().length == 0) {
		_msg = '请输入商品现价';
	} else if(!/^[1-9][0-9]*$/.test(_this.val())) {
		_msg = '商品现价必须为正整数';
	}
	
	if(_msg == '') {
		$('#dis_price_error').html('<s class="true"></s>');
	} else {
		$('#dis_price_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;
}

function listPriceValid() {
	var _this = $('#list_price');
	if(_this.val().length > 0) {
		var _msg = '';
		if(!/^[1-9][0-9]*$/.test(_this.val())) {
			_msg = '商品现价必须为正整数';
		}
		if(_msg != '') {
			$('#list_price_error').html('<s class="false"></s>' + _msg);
			return false;
		} else {
			$('#list_price_error').html('<s class="true"></s>');
		}
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

function shopIdValid() {
	var _this = $('#shop_id');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#shop_id_error').html('<s class="false"></s>请选择店铺');
			return false;
		} else {
			$('#shop_id_error').html('<s class="true"></s>');
		}
	}
	return true;
}

function shopNameValid() {
	var _this = $('#shop_name');
	if(_this.length > 0) {
		if($.trim(_this.val()) == '') {
			$('#shop_name_error').html('<s class="false"></s>请输入店铺名称');
			return false;
		} else {
			$.getJSON('/home/good/check-shop-name', {sname:_this.val()}, function(json){
				if(json.res == 100) {
					$('#shop_name_error').html('<s class="true"></s>');
				} else {
					$('#shop_name_error').html('<s class="false"></s>' + json.msg);
					return false;
				}
			});
			
		}
	}
	return true;	
}

function addressValid() {
	var _this = $('#address');
	if(_this.length > 0) {
		if($.trim(_this.val()) == '') {
			$('#address_error').html('<s class="false"></s>请输入店铺地址');
			return false;
		} else {
			$('#address_error').html('<s class="true"></s>');
		}
	}
	return true;		
}

function imgValid() {
	var _this = $('#img');
	var _msg = '';
	if(_this.val().replace(',','').length == 0) {
		_msg = '请选择上传图片';
	}
	
	if(_msg == '') {
		$('#img_error').html('<s class="true"></s>');
	} else {
		$('#img_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;
}
</script>
</head>
<body>
	  <div class="w1210">
      <!--top-->
      {{include file='top.php'}}
      </div>
      <!--nav-->
      {{include file='nav.php'}}
      <!--内页-->
      <div class="w1210">
      <div class="nyWaper">
      <!--左-->
      		<div class="nyLeft">
            	<h3 class="upFileTit">上传商品</h3>
                <form id="signupForm" action="{{$_CONF.FORM_ACTION}}" method="post">
                <input type="hidden" name="formhash" value="{{$formhash}}" />
                <div class="upFileBox">
                	<p class="inputBox">
                    <label class="label">商品标题：</label>
                    <input type="text" class="text shopTitInput" name="good_name" id="good_name" value="" placeholder="请输入商品标题" /><font>*</font>30字以内</p>
                    <p class="error"><span id="good_name_error"></span></p>
                    <p class="inputBox">
                    <label class="label">商品现价：</label>
                    <input type="text" class="text priceInput" name="dis_price" id="dis_price" value="" placeholder="请输入商品现价" /><font>*</font>
                    <label class="label w1">商品原价：</label>
                    <input type="text" class="text priceInput" name="list_price" id="list_price" value="" placeholder="请输入商品原价" /></p>
                    <p class="error"><span class="h1" id="dis_price_error"></span><span class="h1" id="list_price_error"></span></p>
                    {{if $rid && $cid && $sid}}
                    <div>
                        <div>
                        <p class="selectBox clearfix"><label class="label">所属店铺：</label>
                        <select name="region_id" id="region_id">
                            <option value="">请选择所在区</option>
                            {{foreach from=$regionArray key=key item=item}}
                            <option value="{{$key}}" {{if $rid eq $key}}selected="selected"{{/if}}>{{$item}}</option>
                            {{/foreach}}
                        </select>
                        <select name="circle_id" id="circle_id" class="s2">
                            <option value="">请选择商圈</option>
                            {{foreach from=$circleArray key=key item=item}}
                            <option value="{{$item.id}}" {{if $cid eq $item.id}}selected="selected"{{/if}}>{{$item.name}}</option>
                            {{/foreach}}
                        </select>
                        <select name="shop_id" id="shop_id" class="s2">
                            <option value="">请选择店铺</option>
                            {{foreach from=$shopArray key=key item=item}}
                            <option value="{{$item.id}}" {{if $sid eq $item.id}}selected="selected"{{/if}}>{{$item.name}}</option>
                            {{/foreach}}
                        </select>
                        </p>
                        <p class="error">
                        <span class="h2" id="region_id_error"></span><span class="h2" id="circle_id_error"></span>
                        <span class="h2" id="shop_id_error"></span>
                        </p>
                        </div>
                    </div>
                    {{else}}
                    <div id="shopAdd"></div>
                    {{/if}}
                    <div class="uploadpic">
                        <label class="label">上传图片:</label>
                        <div class="imgBtn">
                        <input type="file" class="file" id="file">
                        </div>
                        <font>*</font>
                        <span class="loadBar"><em style="width: 0%" id="loadBar"></em></span>
                        <span id="loadNum" class="exp"><em>等待上传</em></span>
                        <span class="exp">最多不超过10张，每张图片限1M</span>
                    </div>
                    <div class="imgBox" id="postImageList" style="display:none">
                        <ul class="clearfix"></ul>
                        <p class="allDel" id="allDel"><a href="javascript:;">批量删除</a></p>
                    </div>
                    <input type="hidden" name="img" id="img" value="" />
                    <p class="error"><span id="img_error"></span></p>
                    <p class="submitBox"><input type="button" class="submit" value="提交商品" onClick="check_submit()" /></p>
                </div>
                </form>
            </div>
            <!--右侧-->
            {{include file='ticket/right.php'}}
      </div>
          <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<script type="text/javascript">
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

$(function(){	
	$('#region_id').live('change', function(){
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('请选择商圈').val(''));
		if($('#shop_id')) {
			$('#shop_id').empty();
			$('#shop_id').append($("<option>").text('请选择店铺').val(''));
		}
		regionIdValid();
		$.post('/home/good/get-circle', {region_id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		})
	});
		
	$('#circle_id').live('change',function(){
		var _this = $('#shop_id');
		circleIdValid();
		if(_this.length > 0) {
			_this.empty();
			_this.append($("<option>").text('请选择店铺').val(''));			
			$.post('/home/good/get-shop', {region_id:$('#region_id').val(), circle_id:$('#circle_id').val()}, function(obj){
				var data = eval('(' + obj + ')');
				$.each(data, function(i, s){
					_this.append($("<option>").text(s.name).val(s.id));
				});
			});
		}
	});	
});

function createShop() {
	var _html = '';	
		_html += '<div><p class="inputBox"><label class="label">创建店铺：</label>';
		_html += '<input type="text" class="text shopTitInput" name="shop_name" id="shop_name" value="" placeholder="请输入店铺名称" /><a onClick="reBtn()" class="reBtn">返回</a></p>';
        _html += '<p class="error"><span id="shop_name_error"></span></p>';
        _html += '<p class="selectBox clearfix">';
        _html += '<select name="region_id" id="region_id" class="s1">';
        _html += '</select>';
        _html += '<select name="circle_id" id="circle_id" class="s2"><option value = "">请选择商圈</option></select>';
        _html += '<input type="text" class="text shopAdressInput" name="address" id="address" value="" placeholder="请输入店铺地址" />';
        _html += '</p>';
        _html += '<p class="error">';
        _html += '<span class="h2" id="region_id_error"></span>';
        _html += '<span class="h2" id="circle_id_error"></span>';
        _html += '<span class="h2" id="address_error"></span></p>';
        _html += '</div>';
		$('#shopAdd').html(_html);
		loadRegion();
}

function reBtn() {
	var _html = '';
		_html += '<div>';
       	_html += '<p class="selectBox clearfix">'
        _html += '<label class="label">所属店铺：</label>';
        _html += '<select name="region_id" id="region_id">';
      	_html += '</select>';
        _html += '<select name="circle_id" id="circle_id" class="s2"><option value = "">请选择商圈</option></select>'
        _html += '<select name="shop_id" id="shop_id" class="s2"><option value = "">请选择店铺</option></select>';
        _html += '<span><font>*</font><a  onClick="createShop()">创建店铺</a></span>';
        _html += '</p>';
        _html += '<p class="error">';
        _html += '<span class="h2" id="region_id_error"></span>';
        _html += '<span class="h2" id="circle_id_error"></span>';
        _html += '<span class="h2" id="shop_id_error"></span>';
        _html += '</p></div>';
		if($('#shopAdd').length > 0) {
			$('#shopAdd').html(_html);
			loadRegion();
		}
}
reBtn();


</script>
{{include file='footer.php'}}