<form action="{{$_CONF.FORM_ACTION}}/ctype/{{$coupon_type}}/sid/{{$sid}}" method="post" id="voucherForm" enctype="multipart/form-data">
<input type="hidden" name="gids" id="gids" value="" />
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="ctype" id="ctype" value="{{$coupon_type}}" />
<input type="hidden" name="content" id="content" value="" />
<input type="hidden" name="formhash" value="{{$formhash}}" />
<div class="upFileBox">
    <p class="inputBox">
    <label class="label">活动名称：</label>
    <input type="text" class="text actInput" name="activity_name" id="activity_name" value="" placeholder="请输入活动名称" ><font>*</font>
    <a class="changeact" id="changeact" onClick="document.getElementById('actPopup').style.display = 'block'">选择已有活动</a></p>
    <p class="error">
    <span id="activity_name_error"></span>
    </p>
    
    <p class="inputBox">
    <label class="label">券标题：</label>
    <input type="text" class="text shopTitInput" name="t_title" id="t_title" value="" placeholder="请输入券标题" /><font>*</font>30字以内
    </p>
    <p class="error">
    <span id="t_title_error"></span>
    </p>
    
    <p class="inputBox">
    <label class="label">券面值：</label>
    <input type="text" class="text priceInput" name="p_value" id="p_value" value="" placeholder="请输入券面值" /><font>*</font>
    <label class="label">售价：</label>
    <input type="text" class="text priceInput" name="s_value" id="s_value" value="" placeholder="请输入售价" /><font>*</font>
    </p>
    <p class="error">
    <span class="n1" id="p_value_error"></span>
    <span class="n2" id="s_value_error"></span>
    </p>
    
    <p class="inputBox">
    <label class="label">总数量：</label>
    <input type="text" class="text priceInput" name="total" id="total" value="" placeholder="请输入券数量" /><font>*</font>
    <label class="label">限购：</label>
    <input type="text" class="text priceInput" name="climit" id="climit" value="" placeholder="请输入限购数量" />
    <span> 件 / </span>
    <select name="unit" id="unit" class="session">
    	<option value="Activity">场</option>
        <option value="Hour">小时</option>
        <option value="Day">天</option>
        <option value="Week">周</option>
        <option value="Weekly">自然周</option>
        <option value="Month">月</option>
        <option value="Monthly">自然月</option>
        <option value="Minutes">分钟</option>  
    </select><font>*</font>
    </p>
    <p class="error">
    <span class="n1" id="total_error"></span>
    <span class="n2" id="climit_error"></span>
    </p>
    
    <p class="inputBox">
        <label class="label">销售有效期：</label>
        <input type="text" name="sdate" id="sdate"  class="text timeInput" value="" placeholder="开始时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" />&nbsp;至&nbsp;
        <input type="text" name="edate" id="edate" class="text timeInput"  value="" placeholder="结束时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'sdate\',{d:1})}'})" /><font>*</font>
    </p>
    <p class="error">
    <span class="h2" id="sdate_error"></span>
    <span class="h2" id="edate_error"></span>
    </p>
    
    <p class="inputBox">
        <label class="label">使用有效期：</label>
        <input type="text" name="stime" id="stime"  class="text timeInput" value="" placeholder="开始时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" />&nbsp;至&nbsp;
        <input type="text" name="etime" id="etime" class="text timeInput"  value="" placeholder="结束时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})" /><font>*</font>
    </p>
    <p class="error">
    <span class="h2" id="stime_error"></span>
    <span class="h2" id="etime_error"></span>
    </p>
    
    <div class="textareabox clearfix">
    <label>券简介：</label>
    <div class="textarea textarea-03">
    <textarea class="noticeTextarea" name="summary" id="summary" placeholder="可发布券使用规则等公告内容，70字内"></textarea>
    </div>
    <font>*</font>
    </div>
    <p class="error"><span id="summary_error"></span></p>

    <div class="uploadpic">
        <label class="label">封面图片：</label>
        <div class="upfile-img-cover">
            <input type="file" class="file" name="file_img" id="file_img" onchange="$('#loadNumFileImg>em').text($(this).val())">
        </div>
        <font>*</font>                 
        <span id="loadNumFileImg" class="exp"><em></em></span>
        <span class="exp">图片尺寸 640 * 300 </span>
    </div>
    <p class="error"><span id="file_img_error"></span></p>
        
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
    <div class="imgBox" id="postImageList" style="display:none">
        <ul class="clearfix"></ul>
        <p class="allDel" id="allAdd"><a href="javascript:void(0)">全部插入</a></p>
    </div>
    
    <!---->
    <div class="textareabox clearfix">
    <label>使用说明：</label>
    <div class="textarea">
        <textarea id="postTextarea"></textarea>
    </div>
    <font>*</font>
    </div>
    <p class="error"><span id="postTextarea_error"></span></p>
    <p class="inputBox"><label class="label">适用商品：</label><a class="setShop" id="setShop">点击设置适用商品</a></p>
    <p class="choiceBox" id="choiceBox"></p>
    <p class="submitBox" ><input type="button" class="submit" value="提交代金券" onClick="submit_check()" id="submitCheck"></p>
</div>
</form>
<div class="Popup" style="display:none"  id="selectPopup">
    <div class="selectPopup">
            <h2>设置优惠券适用商品<a class="close">&times;</a></h2>
            <div class="select-con">
                <p class="selectAll"><input type="checkbox" class="check" id="selectAll" checked="false"><label>选择所有</label></p>
                <div class="selectTable">
                </div>
                <p class="center" id="selectBox"><input type="submit" value="提交设置" class="selectBtn"/></p>
            </div>
    </div>
   <div class="shade"></div>
</div>

<div class="Popup" style="display:none" id="actPopup">
    <div class="actPopup">
        <h2>选择已有活动<a class="close" onClick="this.parentNode.parentNode.parentNode.style.display = 'none'">&times;</a></h2>
        <div class="upFileBox">
            <p class="actTit">请选择进行中的活动</p>
            <p class="selectBox clearfix">
            <select name="activity" id="activity" class="actselect">
            <option value="">活动名称</option>
            {{foreach from=$activityArray key=key item=item}}
            <option value="{{$item.activity_id}}">{{$item.activity_name}}</option>
            {{/foreach}}
            </select>
            </p>
            <p><input type="button" class="actBtn" value="确定" onClick="selectActivity()"></p>
        </div>
    </div>
     <div class="shade"></div>
</div>
<script type="text/javascript" src="/js/addImage.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/setshop.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
var validArray = ['activity_name', 't_title', 'p_value', 's_value', 'total', 'climit', 'sdate', 'edate', 'stime', 'etime', 'summary', 'file_img', 'postTextarea'];
var postImage, editor, uploadSwf, setGood;
$(function(){	
	editor = KindEditor.create('#postTextarea',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	setGood = new SelectShop();	
	postImage=new couponImage();
	uploadSwf = new SWFUpload({
        upload_url: "/home/good/upload?folder=ticket",
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
	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
});

function submit_check() {
	var len = 0;
	$('#gids').val(setGood.getCache().join(','));
	$("#content").val(editor.html());
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}
	if(len == validArray.length) {
		$("form#voucherForm").submit();
		$("form#voucherForm .submit").attr('value', '提交中。。。').attr('disabled', true);		
	}
	return false;
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'activity_name':
				if($('#' + id).val().length == 0) {
					_msg = '请输入活动名称';	
				}
			break;
		case 't_title':
				if($('#' + id).val().length == 0) {
					_msg = '请输入券标题';	
				} else if($('#' + id).val().length > 30) {
					_msg = '券标题最多30个字符，汉字算一个字符';
				}
			break;
		case 'p_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入券面值';	
				} else if(!/^[1-9][0-9]*(\.[1-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '券面值错误';
				}
			break;
		case 's_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入券售价';	
				} else if(!/^[1-9][0-9]*(\.[1-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '券售价错误';
				}
			break;
		case 'total':
				if($('#' + id).val().length == 0) {
					_msg = '请输入券数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '券数量为正整数';
				}			
			break;
		case 'climit':
				if($('#' + id).val().length == 0) {
					_msg = '请输入限购数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '限购数量为正整数';
				}
			break;
		case 'sdate':
				if($('#' + id).val().length == 0) {
					_msg = '请输入开始时间';	
				}
			break;
		case 'edate':
				if($('#' + id).val().length == 0) {
					_msg = '请输入结束时间';	
				}
			break;
		case 'stime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入开始时间';	
				}
			break;
		case 'etime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入结束时间';	
				}
			break;
		case 'summary':
				if($('#' + id).val().length == 0) {
					_msg = '请输入券简介';	
				} else if($('#' + id).val().length > 70) {
					_msg = '券简介最多70个字符，汉字算一个字符';
				}
			break;
		case 'file_img':
				if($('#' + id).val().length == 0) {
					_msg = '上传封面图片';	
				} else {
					var file = $('#' + id).val();
					if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
						_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
					}					
				}
			break;		
		case 'postTextarea':
			if(editor.html().length == 0) {
				_msg = '请输入优惠卷使用说明';	
			}
			break;
	}
	
	if(_msg == '') {
		$('#' + id + '_error').html('<s class="true"></s>');
	} else {
		$('#' + id + '_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;	
	
}

function selectActivity() {
	if($("#activity").val() != '') {
		$('#activity_name').val($("#activity").find("option:selected").text());
	}
	$('#actPopup').hide();
}
</script>