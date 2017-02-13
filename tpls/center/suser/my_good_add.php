<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
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
	
});

function check_submit() {	
	var good_status = dis_status = list_status = img_status = false;	
	good_status = goodNameValid();
	dis_status = disPriceValid();
	list_status = listPriceValid();
	$('#img').val(postImage.getCache().join(','));
	img_status = imgValid();	
	if(good_status && dis_status && list_status && img_status) {
		$("form#signupForm").submit();
		$("form#signupForm .submit").attr('value', '提交中。。。').attr('disabled', true);
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
	} else if(isNaN(_this.val())) {
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
		if(isNaN(_this.val())) {
			_msg = '商品原价必须为正整数';
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

function imgValid() {
	var _this = $('#img');
	var _msg = '';
	if(_this.val().length == 0) {
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
    <!--site-->
	{{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
	{{include file='center/left.php'}}
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
			<div class="tab-title">
                <ul>
                    {{if $_CONF._A eq 'my-good'}}
                    	<li class="sel" ><a href="javascript:void(0)">商品管理</a></li>
                    {{else}}
                    	<li><a href="/home/suser/my-good/sid/{{$sid}}">商品管理</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'add'}}
                        <li class="sel"><a href="javascript:void(0)">上传商品</a></li>
                    {{else}}
                        <li><a href="/home/suser/add/sid/{{$sid}}">上传商品</a></li>
                    {{/if}}
                </ul>
            </div>
            <form id="signupForm" action="{{$_CONF.FORM_ACTION}}" method="post">
             <input type="hidden" name="shop_id" value="{{$sid}}" />
             <input type="hidden" name="formhash" value="{{$formhash}}" />
            <div class="upFileBox">
                	<p class="inputBox">
                    <label class="label">商品标题：</label><input type="text" name="good_name" id="good_name" class="text shopTitInput" value=""><font>*</font>30字以内</p>
                    <p class="error"><span class="h1" id="good_name_error"></span></p>
                    <p class="inputBox">
                    <label class="label">商品现价：</label><input type="text" name="dis_price" id="dis_price" class="text priceInput" value=""><font>*</font>
                    <label class="label w1">商品原价：</label><input type="text" name="list_price" id="list_price" class="text priceInput" value=""></p>
                    <p class="error"><span class="h1" id="dis_price_error"></span><span class="h1" id="list_price_error"></span></p>
                    <div class="uploadpic">
                        <label class="label">上传图片：</label>
                        <div class="imgBtn">
                        <input type="file" class="file" id="file">
                        </div>
                        <font>*</font>
                        <span class="loadBar"><em style="width: 0%" id="loadBar"></em></span>
                        <span id="loadNum" class="exp"><em>等待上传</em></span>
                        <span class="exp">最多不超过10张，每张图片限1M</span>
                    </div>
                    <p class="error"><span id="img_error"></span></p>
                    <div class="imgBox" id="postImageList" style="display:none">
                        <ul class="clearfix"></ul>
                        <p class="allDel" id="allDel"><a id="DelBtn">批量删除</a></p>
                    </div>
                    <input type="hidden" name="img" id="img" value="" />
                    <p class="submitBox" ><input type="button"  class="submit" value="提交商品" onClick="check_submit()"></p>
            </div>
            </form>
    </div>
    </div>
{{include file='center/footer.php'}}
</body>
</html>
