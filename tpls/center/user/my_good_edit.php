<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>编辑商品-我的名品街-名品导购网</title>
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
	
	$('#region_id').on('blur', function(){
		regionIdValid();
	});
	
	$('#circle_id').on('blur', function(){
		circleIdValid();
	});
	
	$('#shop_id').on('blur', function(){
		shopIdValid();
	});
	
});

function check_submit() {	
	var good_status = dis_status = list_status = img_status = false;	
	good_status = goodNameValid();
	dis_status = disPriceValid();
	list_status = listPriceValid();
	$('#img').val(postImage.getCache().join(','));
	if(good_status && dis_status && list_status) {
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
		_msg = '商品现价必须为数字';
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
			_msg = '商品原价必须为数字';
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
    <!--site-->
	{{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
	<div class="persub">
	    <!--个人信息-->
	    <div class="perinfo">
	        <b class="title"></b>
	        <div class="perbox">
	            <div class="info"><div class="imgbox">
	            <a href="http://passport.mplife.com/settings/perAvatar.aspx" title="修改头像" target="_blank">
	            <img src="{{$userSync.Avatar50}}" alt="" width="60" height="60"></a></div>
	            <div class="text">
	            <p><a href="http://passport.mplife.com/settings/perManage.aspx" title="资料修改" target="_blank">{{$user.user_name}}</a></p>
	            <p> {{$userSync.GroupTitle}}</p>
	            <p>
	            <a href="http://passport.mplife.com/settings/perManage.aspx" title="修改性别" target="_blank"><img src="{{if $userSync.UserSex eq 1}}/images/user/male.png{{else}}/images/user/female.png{{/if}}" alt=""></a>
	            <a href="http://passport.mplife.com/settings/perManage.aspx" title="{{$userSync.CityTitle}}" target="_blank">{{$userSync.CityTitle}}</a>
	            </p>
	            </div>
	            </div>
	            <div class="hot-line">会员热线：021-52519666</div><div class="pertask"></div>
	        </div>
	    </div>
	    <!--个人信息end-->
	    <!--后台管理列表-->
	    <div class="back-sidebar">
	        <div class="sub-nav">
	            <ul>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=1">我上传的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=2">我收藏的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=3">我喜欢的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/bclist.aspx">我的商圈</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oMyOrder/MyMpTicket.aspx">我的优惠卷</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oMyOrder/MyOrder.aspx">我的订单</a></li>
                    <li><a href="{{$_CONF.SITE_URL}}/home/user/my-task">街友会</a></li>
	            </ul>
	        </div>
	    </div>       
	</div>
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
    		<div class="tab-title">
            	<ul>
                	<li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=1">我上传的商品</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=2">我收藏的</a></li>
                    <li><a href="{{$_CONF.MAIN_SITE_URL}}/O2oNewbuy/commoditylist.aspx?t=3">我喜欢的</a></li>
                    {{if $_CONF._A eq 'good-edit'}}
                    	<li class="sel"><a href="javascript:void(0)">编辑商品</a></li>
                    {{/if}}
                </ul>
            </div>
            <form id="signupForm" action="{{$_CONF.FORM_ACTION}}" method="post">
             <input type="hidden" name="gid" value="{{$goodInfo.good_id}}" />
             <input type="hidden" name="sid" value="{{$sid}}" />
             <input type="hidden" name="referer" value="{{$referer}}" />
            <div class="upFileBox">
                	<p class="inputBox">
                    <label class="label">商品标题：</label><input type="text" name="good_name" id="good_name" class="text shopTitInput" value="{{$goodInfo.good_name}}"><font>*</font>30字以内</p>
                    <p class="error"><span class="h1" id="good_name_error"></span></p>
                    <p class="inputBox">
                    <label class="label">商品现价：</label><input type="text" name="dis_price" id="dis_price" class="text priceInput" value="{{$goodInfo.dis_price}}"><font>*</font>
                    <label class="label w1">商品原价：</label><input type="text" name="list_price" id="list_price" class="text priceInput" value="{{if $goodInfo.org_price gt 0}}{{$goodInfo.org_price}}{{/if}}"></p>
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
                    <div class="imgBox" id="postImageList" {{if !$goodInfo.good_id}}style="display:none"{{/if}}>
                        <ul class="clearfix">
                        {{foreach from=$goodInfo.img key=key item=item}}
                            <li>
                                <p class="picBox"><img src="{{$item.img_url_small}}"></p>                                                                                     
                                <p class="setBox">
                                <a class="del" data-aid="{{$item.good_img_id}}">删除</a>
                                <a class="setCover" data-aid="{{$item.good_img_id}}">设为封面</a>
                                <input type="checkbox" data-aid="{{$item.good_img_id}}" name="ck">
                                </p>
                            </li>
                        {{/foreach}}                
                        </ul>
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