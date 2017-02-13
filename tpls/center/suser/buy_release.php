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
<script type="text/javascript" src="/js/addImage.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/select.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/sku20141120.js?t={{$_CONF.WEB_VERSION}}"></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
<script type="text/javascript">
var validArray = ['activity_name', 'ticket_title', 'ticket_class', 'p_value', 's_value', 'total', 'sdate', 'edate', 'stime', 'etime', 'ticket_summary', 'file_img_large', 'file_img_small', 'postTextarea'];
var postImage, editor, uploadSwf;
$(function(){
	editor = KindEditor.create('#postTextarea',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
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

	

	changeShop({
		btn:'.change-shop-pic',
		txt:'.allList',
		val:'.change-shop'
	});
	
	$('.shopBox a').click(function(){
		var cid = $(this).attr('data-cid');
		var _html = '';
		$(".sku-dis").html('');
		$(".right-list").html('');
		$("#sku_buffer").html('加载中。。。');
		$.ajax({
		   type: "POST",
		   url:  "/home/suser/get-sku-list",
		   data: {cid:cid},
		   dataType:"json",
		   success: function(data){
			   var len = data.length;
			   for(var i=0; i < len; i++) {
					_html += '<div class="inputBox">';
					_html += '<label class="sku-label" data-cid="'+data[i]['CategoryId']+'" data-pid="'+data[i]['PropId']+'">'+data[i]['PropName']+'：</label>';
					_html += '<div class="color-list">';
					_html += '<ul>';
					var vlen = data[i].Values.length;
					for(var j=0; j < vlen; j++) {
						_html += '<li><input type="checkbox" class="color-checkbox" value="'+data[i]['Values'][j]['ValueId']+'" initvalue="'+data[i]['Values'][j]['ValueName']+'" data-alias="'+data[i]['AllowAlias']+'">';
						_html += '<span class="color-val">'+data[i]['Values'][j]['ValueName']+'</span><input type="text" class="color-input" /></li>';
					}
					_html += '</ul>';
					_html += '</div>';
					_html += '</div>';
			   }
			   
			   if(_html != '') {
			   	  $(".sku-dis").html(_html);
				  $("#sku_buffer").html('');			 
				  sku();
			   }
		   }
		});

	});

});



function getSkuToStr() {
	var dataStr = '';
	var data_cid;
	$("div .sku-dis .inputBox").each(function(index, element) {
		var data_pid;
        data_cid = $(this).children("label").attr('data-cid');
		data_pid = $(this).children("label").attr('data-pid');
		var skuStr = "";
		$.each($(this).find("li"), function(){
			var ckStr = "";
			if($(this).find(".color-checkbox").attr("checked") == "checked") {
				ckStr = $(this).find(".color-checkbox").val() + ',' + $(this).find(".color-val").html();
			}
			if(ckStr != "") {
				skuStr += data_pid + ';' + ckStr + '^^';
			}
		});
		
		if(skuStr != "") {
			skuStr = skuStr.substr(0, skuStr.length - 2);
			dataStr +=  skuStr + '&&';
		}
    });
	if(dataStr != "") {
		dataStr = data_cid + ',' + $("span.change-shop").html() + '&&' + dataStr.substr(0, dataStr.length - 2);
		$("#dataStr").val(dataStr);
	}
}

function submit_check() {
	getSkuToStr();
	var len = 0;
	$("#content").val(editor.html());
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}
	if(len == validArray.length) {
		$("form#buyForm").submit();
		$("form#buyForm .submit").attr('value', '提交中。。。').attr('disabled', true);		
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
		case 'ticket_class':
				if($('#' + id).val().length == 0) {
					_msg = '请选择商品分类';	
				}
			break;
		case 'ticket_title':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品名称';	
				} else if($('#' + id).val().length > 30) {
					_msg = '商品名称最多30个字符，汉字算一个字符';
				}
			break;
		case 's_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品现价';	
				} else if(!/^[1-9][0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品现价错误';
				} else if(Number($("#MaxNumber").val()) > 0 && Number($("#MinNumber").val()) > 0) {
					if(Number($('#' + id).val()) < Number($("#MinNumber").val()) || Number($('#' + id).val()) > Number($("#MaxNumber").val())) {
						_msg = '商品现价只能在：' + $("#MinNumber").val() + '-' + $("#MaxNumber").val() + '之间';
					}
				}
			break;
		case 'p_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品原价';	
				} else if(!/^[1-9][0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品原价错误';
				}
			break;
		case 'total':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '商品数量为正整数';
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
		case 'ticket_summary':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品简介';	
				} else if($('#' + id).val().length > 70) {
					_msg = '商品简介最多70个字符，汉字算一个字符';
				}
			break;
		case 'file_img_large':
				if($('#' + id).val().length == 0) {
					_msg = '上传商品大图';	
				} else {
					var file = $('#' + id).val();
					if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
						_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
					}					
				}
			break;	
		case 'file_img_small':
				if($('#' + id).val().length == 0) {
					_msg = '上传商品小图';	
				} else {
					var file = $('#' + id).val();
					if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
						_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
					}					
				}
			break;	
		case 'postTextarea':
			if(editor.html().length == 0) {
				_msg = '请输入商品使用说明';	
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

function resetRelated(obj, stype) {
	if(stype == 1) {
		obj.append($("<option>").text('请选择商圈').val(''));
	} else if(stype == 2) {
		obj.append($("<option>").text('请选择商场').val(''));
	} else if(stype == 3) {
		obj.append($("<option>").text('请选择品牌').val(''));
	}			
}

$("input[name=shop_type]").click(function(){
	var _this;
	$('#region_id').val('');
	_this = $('#related_id');
	_this.empty();			
	resetRelated(_this, this.value);
});

$(function(){
	$('#region_id').val('');
	var stype = $("input[name=shop_type]:checked").val();
	resetRelated($('#related_id'), stype);
	
	//$('#related_id').append($("<option>").text('请选择商圈').val(''));
	attachAddButtonEvent('add', 'moveFrom', "moveTo", '请选择可选店铺!');
	attachDeleteButtonEvent('delete', 'moveFrom', "moveTo", "请选择要删除的店铺");
	
	$('#region_id').change(function(){
			var stype = $("input[name=shop_type]:checked").val();
			var _this = $('#related_id');
			_this.attr("disabled", false);
			_this.empty();
			resetRelated(_this, stype);
			if(this.value) {
				$.post('/home/suser/get-sel-list', {region_id:$(this).val(), stype : stype}, function(obj){
					var data = eval('(' + obj + ')');
					$.each(data, function(i, s){
						_this.append($("<option>").text(s.name).val(s.id));
					});
				});	
			}
	});			
});


function search_to() {
	var stype = $("input[name=shop_type]:checked").val();
	var related_id = $('#related_id').val();
	var region_id = $('#region_id').val();
	var sname = $('#search_name').val();
	var _this = $('#moveFrom');
	_this.empty();
	
	$(".store-search").html('···').attr('href', 'javascript:void(0)');
	$.ajax({
		url:"/home/suser/get-shop-list",
		dataType:"json",
		data:{"stype":stype,"related_id":related_id, "region_id":region_id, "sname":sname},
		success:function(data){
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
			$(".store-search").html('搜索').attr('href', 'javascript:search_to()');									
		},
		error:function(){
		}
	})
}

/*attachAddButtonEvent：给add按钮添加事件*/
 function attachAddButtonEvent(addButtonId, candidateListId, selectedListId, msg) {
	$(function() {
		$("#" + addButtonId).click(function() {
			if ($("#" + candidateListId + " option:selected").length > 0)
			{
				$("#" + candidateListId + " option:selected").each(function() {
					$("#" + selectedListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
					//$(this).remove();
				})
			}
			else
			{
				alert(msg);
			}
		})
	})
}
/*attachDeleteButtonEvent：给delet按钮添加事件*/
function attachDeleteButtonEvent(deleteButtonId, candidateListId, selectedListId, msg) {
	$(function() {
		$("#" + deleteButtonId).click(function() {
			if ($("#" + selectedListId + " option:selected").length > 0)
			{
				$("#" + selectedListId + " option:selected").each(function() {
					//$("#" + candidateListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
					$(this).remove();
				})
			}
			else
			{
				alert(msg);
			}
		})
	})
}

function shopTrue(){	
	var shopIdStr = "";
	var shopTextStr = "";
	
	$("#moveTo option").each(function(){
		shopIdStr += $(this).val() + ",";
		shopTextStr += '<a>' + $(this).text() + "</a>";
	});
	
	$('#choiceBoxShop').html(shopTextStr);
	$('#sids').val(shopIdStr);

	$('#selectPopupShop').hide();
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
                    {{if $_CONF._A eq 'buy-good'}}
                    	<li class="sel" ><a href="javascript:void(0)">团购商品</a></li>
                    {{else}}
                    	<li><a href="/home/suser/buy-good/sid/{{$sid}}">商品管理</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'sold-orders'}}
                        <li class="sel"><a href="javascript:void(0)">售出订单</a></li>
                    {{else}}
                        <li><a href="/home/suser/sold-orders/sid/{{$sid}}">售出订单</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'buy-release'}}
                        <li class="sel"><a href="javascript:void(0)">发起团购</a></li>
                    {{else}}
                        <li><a href="/home/suser/buy-release/sid/{{$sid}}">发起团购</a></li>
                    {{/if}}
                    
                    {{if $_CONF._A eq 'veriy'}}
                        <li class="sel"><a href="javascript:void(0)">团购验证</a></li>
                    {{else}}
                        <li><a href="/home/suser/veriy/sid/{{$sid}}">团购验证</a></li>
                    {{/if}}
                </ul>
            </div>
            <form action="{{$_CONF.FORM_ACTION}}/sid/{{$sid}}" method="post" id="buyForm" enctype="multipart/form-data">
            <input type="hidden" name="sids" id="sids" value="" />
            <input type="hidden" name="sid" id="sid" value="{{$sid}}" />
            <input type="hidden" name="content" id="content" value="" />
            <input type="hidden" name="dataStr" id="dataStr" value="" />
            <input type="hidden" name="dataRetStr" id="dataRetStr" value="" />
            <input type="hidden" name="formhash" value="{{$formhash}}" />
            <input type="hidden" id="MaxNumber" value="" />
            <input type="hidden" id="MinNumber" value="" />
            <input type="hidden" id="MaxAppNumber" value="" />
            <input type="hidden" id="MinAppNumber" value="" />
               <div class="upFileBox">
                	<p class="inputBox">
                    	<label class="label">活动名称：</label>
                        <input type="text" class="text actInput" name="activity_name" id="activity_name" value="" placeholder="请输入活动名称" /><font>*</font>
                        <a class="changeact" id="changeact" onClick="document.getElementById('actPopup').style.display = 'block'">选择已有活动</a></p>
                    <p class="error">
                    	<span id="activity_name_error"></span>
                    </p>
                    
                    <p class="inputBox">
                    	<label class="label">商品名称：</label>
                        <input type="text" class="text shopTitInput" name="ticket_title" id="ticket_title" value="" placeholder="请输入商品名称" /><font>*</font>30字以内
                    </p>
                    <p class="error">
                    	<span id="ticket_title_error"></span>
                    </p>
                    
                    <p class="inputBox">
                        <label class="label">商品分类：</label>
                        <select name="ticket_class" id="ticket_class" class="select">
                 			<option value="">请选择商品分类</option>    	
                        	{{foreach from=$storeArray key=key item=item}}
                            <option value="{{$key}}">{{$item}}</option>
                            {{/foreach}}
                        </select>
                    </p>
                    <p class="error">
                    	<span id="ticket_class_error"></span>
                    </p>
                    
                    <p class="inputBox">
                    	<label class="label">关联店铺：</label>
                        <a class="setShop" onClick="$('#selectPopupShop').css('display','block')">点击选择关联店铺</a>
                    </p>
                    <div class="inputBox"><p class="store-list" id="choiceBoxShop"></p></div>
                    
                    <p class="inputBox">
                    	<label class="label">销售有效期：</label>
                        <input type="text" class="text timeInput" name="sdate" id="sdate" value="" placeholder="开始时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" />&nbsp;至&nbsp;
                        <input type="text" class="text timeInput" name="edate" id="edate" value="" placeholder="结束时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'sdate\',{d:1})}'})" />
                    </p>
                    <p class="error">
                    	<span class="h2" id="sdate_error"></span>
                        <span class="h2" id="edate_error"></span>
                    </p>
                    
                    <p class="inputBox">
                    	<label class="label">使用有效期：</label>
                        <input type="text" class="text timeInput" name="stime" id="stime" value="" placeholder="开始时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" />&nbsp;至&nbsp;
                        <input type="text" class="text timeInput" name="etime" id="etime" value="" placeholder="结束时间" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})" />
                    </p>
                    <p class="error">
                    	<span class="h2" id="stime_error"></span>
                        <span class="h2" id="etime_error"></span>
                    </p>
                  
                  
                  
                  <div class="inputBox" style="position:relative; z-index:995">
                    	<label class="label">SKU商品分类：</label><span class="change-shop">----选择----</span><input  type="hidden"  value="" ><span class="change-shop-pic"><s class="change-shop-pic-s"></s></span>
                        
                      <div class="allList" id="allBox">
                            <div class="listBox">
                                    <ul>
                                    	{{foreach from=$skuArray key=key item=item}}
                                        <li class="listBox-col">
                                        	<a data-cid="{{$item.CategoryId}}">{{$item.CategoryName}}</a>
                                            {{if $item.child}}
                                            <div class="shopBox">
                                                <ul>
                                                	{{foreach from=$item.child key=ckey item=citem}}
                                                    <li><a data-cid="{{$citem.CategoryId}}">{{$citem.CategoryName}}</a></li>
                                                    {{/foreach}}
                                                </ul>
                                            </div>
                                            {{/if}}
                                        </li>
                                        {{/foreach}}
                                    </ul>
                            </div>
                       </div>
                    </div>
                    <p class="error" id="sku_buffer"></p>
                    
                    <!--sku-->
                    <div class="sku-dis"></div>
                      
                    <div class="inputBox" id="ske-table-box">
                    	<label class="label">SKU设置：</label>
                        <div class="right-list"></div>
                    </div>
                    <p class="error"></p> 
                     
                     <p class="inputBox">
                     	<label class="label">数量：</label>
                        <input type="text" class="text priceInput" name="total" id="total" value="" placeholder="请输入数量" /><font>*</font>
                     </p>
                     <p class="error">
                        <span class="n1" id="total_error"></span>
                     </p>
                    
                    <p class="inputBox">
                    	<label class="label">现价：</label>
                        <input type="text" class="text priceInput" name="s_value" id="s_value" value="" placeholder="请输入现价" /><font>*</font>
                        <label class="label">原价：</label>
                        <input type="text" class="text priceInput" name="p_value" id="p_value" value="" placeholder="请输入原价" /><font>*</font>
                    </p>
                    <p class="error">
                        <span class="n2" id="s_value_error"></span>
                        <span class="n1" id="p_value_error"></span>
                    </p>                   
                   <!--sku 结束-->
                     
                     
                     
                    <div class="textareabox clearfix">
                    <label>商品简介：</label>
                    <div class="textarea textarea-03">
                    <textarea class="noticeTextarea" name="ticket_summary" id="ticket_summary" placeholder="可发布使用规则等公告内容，70字内"></textarea>
                    </div>
                    <font>*</font>
                    </div>
                    <p class="error"><span id="ticket_summary_error"></span></p>
                    
                    
                    <div class="uploadpic">
                        <label class="label">商品大图：</label>
                        <div class="upfile-img-cover">
                            <input type="file" class="file" name="file_img_large" id="file_img_large" onchange="$('#loadNumFileLargeImg>em').text($(this).val())">
                        </div>
                        <font>*</font>                 
                        <span id="loadNumFileLargeImg" class="exp"><em></em></span>
                        <span class="exp">图片尺寸 {{$recommendArray.buygood_img_large.width}} * {{$recommendArray.buygood_img_large.height}} </span>
                    </div>
                    <p class="error"><span id="file_img_large_error"></span></p>
                    
                    <div class="uploadpic">
                        <label class="label">商品小图：</label>
                        <div class="upfile-img-cover">
                            <input type="file" class="file" name="file_img_small" id="file_img_small" onchange="$('#loadNumFileSmallImg>em').text($(this).val())">
                        </div>
                        <font>*</font>                 
                        <span id="loadNumFileSmallImg" class="exp"><em></em></span>
                        <span class="exp">图片尺寸 {{$recommendArray.buygood_img_small.width}} *{{$recommendArray.buygood_img_small.width}} </span>
                    </div>
                    <p class="error"><span id="file_img_small_error"></span></p>
                    
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
             
                	<div class="textareabox clearfix">
                    <label>使用说明：</label>
                    <div class="textarea">
                        <textarea id="postTextarea"></textarea>
                    </div>
                    <font>*</font>
                    </div>
                    <p class="error"><span id="postTextarea_error"></span></p>
                    
                    <p class="submitBox" ><input type="button" class="submit" value="提交团购" onClick="submit_check()"  id="submitCheck"></p>
            </div>
            </form>
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
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
 <!--关联店铺-->    
<div class="Popup" style="display:none" id="selectPopupShop">
	 <div class="store-popup">
     	<h2>关联店铺<a class="close" onClick="this.parentNode.parentNode.parentNode.style.display = 'none'">&times;</a></h2>
        <div class="store-con">
        		<div class="store-col">
                	<input type="radio" name="shop_type" value="1" checked="checked" class="store-radio"><label class="store-label">商圈</label>
                    <input type="radio" name="shop_type" value="2" class="store-radio"><label class="store-label">商场</label>
                    <input type="radio" name="shop_type" value="3" class="store-radio"><label class="store-label">品牌</label>
                </div>
                <div class="store-col">
                            <select name="region_id" id="region_id" class="store-selcet">
                                <option value="">请选择所在区</option>
                                {{foreach from=$regionArray key=key item=item}}
                                <option value="{{$key}}">{{$item}}</option>
                                {{/foreach}}
                            </select>
                            <select name="related_id" id="related_id" class="store-selcet"></select>
                            <label class="store-label">店铺</label>
                            <input type="text" class="store-text" name="search_name" id="search_name">
                            <a class="store-search" href="javascript:search_to()">搜索</a>
                </div>
                <div class="addstore">
                	<div class="addstore-l">
                    	<h3 class="addstore-title">可选择店铺</h3>
                        <select multiple="multiple" name="moveFrom" id="moveFrom"></select>
                    </div>
                	<a class="addstore-add-btn" id="add">添加</a>
                    <a class="addstore-del-btn" id="delete">删除</a>
                	<div class="addstore-r">
                    	<h3 class="addstore-title">已选择店铺</h3>
                        <select multiple="multiple" name="moveTo" id="moveTo">
                        {{foreach from=$ticketRelationShopArray key=key item=item}}
                        <option value="{{$item.shop_id}}">{{$item.shop_name}}</option>
                        {{/foreach}}
                        </select>
                    </div>	
                </div>
                <div class="store-col">
                	<input type="button" class="store-submit" value="确定" onClick="shopTrue()" />
                </div>
        	
        </div>
     </div>	
	 <div class="shade"></div>
</div>
</body>
</html>