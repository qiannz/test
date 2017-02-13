{{include file='admin/header.php'}}
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/ticket/coupon-list">优惠券列表</a></li>
    {{if !$row.ticket_id}}<li><a class="btn3" href="/admin/ticket/user-shop/type:c/uname:{{$uname}}">店铺选择列表</a></li>{{/if}}
    <li><span>{{if $row.ticket_id}}编辑优惠券{{else}}新建优惠券{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="user_name" id="user_name" value="{{$uname}}" />
<input type="hidden" name="tid" id="tid" value="{{$tid}}" />
<input type="hidden" name="gids" id="gids" value="{{$gids}}" />
<input type="hidden" name="sids" id="sids" value="" />
<table class="infoTable">
    <tr>
        <th class="paddingT15">所属店铺:</th>
        <td class="paddingT15 wordSpacing5">
         {{$row.region_name}} {{$row.circle_name}} {{$row.shop_name}}
        </td>
    </tr>
    <tr>
        <th class="paddingT15">关联店铺:</th>
        <td class="paddingT15 wordSpacing5">
         	<a href="javascript:associate()">点击选择关联店铺</a>
        </td>
    </tr>
    <tr>
        <th class="paddingT15"></th>
        <td class="paddingT15 wordSpacing5">
        <span id="choiceBoxShop" class="choiceBox">
        {{foreach from=$ticketRelationShopArray key=key item=item}}
        <a>{{$item.shop_name}}</a>
        {{/foreach}}
        </span>
        </td>
    </tr>
    <tr>
		<th class="paddingT15">是否特卖:</th>
        <td class="paddingT15 wordSpacing5">
         	<input type="radio" name="is_sale" id="sale_no" value="0" {{if $row.is_sale eq 0}}checked="checked"{{/if}} /> 否
            <input type="radio" name="is_sale" id="sale_yes" value="1" {{if $row.is_sale eq 1}}checked="checked"{{/if}} /> 是
            <input class="infoTableFile2" type="text" name="sale_code" id="sale_code" value="{{$row.sale_code}}" placeholder="场次编号" {{if $row.is_sale eq 0}}style="display:none"{{/if}} />
        </td>    	
    </tr>     
    <tr>
        <th class="paddingT15">优惠券标题:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="{{$row.ticket_title}}" />
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">优惠券面值:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="par_value" id="par_value" value="{{if $row.par_value}}{{$row.par_value|string_format:"%d"}}{{/if}}" />
          <label class="field_notice"></label>
        </td>
    </tr>

    <tr>
        <th class="paddingT15">优惠券数量:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="total" id="total" value="{{$row.total}}" />
          <label class="field_notice"></label>
        </td>
    </tr>
    
    <tr>
        <th class="paddingT15">领取有效期:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="start_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" value="{{if $row.start_time}}{{$row.start_time|date_format:'%Y-%m-%d %H:%I'}}{{/if}}" /> - 
          <input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="end_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'start_time\',{d:1})}'})" value="{{if $row.end_time}}{{$row.end_time|date_format:'%Y-%m-%d %H:%I'}}{{/if}}"/>
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">使用有效期:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile2" style="width:140px;" type="text" name="valid_stime" id="valid_stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" value="{{if $row.valid_stime}}{{$row.valid_stime|date_format:'%Y-%m-%d %H:%I'}}{{/if}}" /> - 
          <input class="infoTableFile2" style="width:140px;" type="text" name="valid_etime" id="valid_etime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'valid_stime\',{d:1})}'})" value="{{if $row.valid_etime}}{{$row.valid_etime|date_format:'%Y-%m-%d %H:%I'}}{{/if}}"/>
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">优惠券简介:</th>
        <td class="paddingT15 wordSpacing5">
          <textarea id="ticket_summary" name="ticket_summary">{{$row.ticket_summary}}</textarea>
          <label class="field_notice">70字内(汉字算一个字符)</label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">封面图片:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="file" class="file" name="file_img" id="file_img" />
          <label class="field_notice">图片尺寸 640 * 300</label>
        </td>
    </tr>
    {{if $row.cover_img}}
    <tr>
        <th class="paddingT15"></th>
        <td class="paddingT15 wordSpacing5">
          <img src="{{$_CONF.IMG_URL}}/buy/cover/{{$row.cover_img}}" />
        </td>
    </tr>
    {{/if}}
    <tr>
    	<th class="paddingT15">上传图片</th>
        <td class="paddingT15 wordSpacing5">
        	<div class="list-box">
                <div class="imgBtn">
                    上传图片
                    <input type="file" class="file" id="file">
                </div>
                <div id="loadBox"></div>
                <span class="loadBar"><em style="width: 0%" id="loadBar"></em></span>
                <span id="loadNum" class="exp"><em>等待上传</em></span>
                <span class="exp">最多不超过10张，每张图片限1M</span>
            </div>
            <div class="imgBox" id="postImageList" {{if !$row.good_id}}style="display:none"{{/if}}>
                <ul class="clearfix">
            	{{foreach from=$imgList key=key item=item}}
                	<li>
                        <p class="img"><img src="{{$_CONF.SITE_URL}}/data/good/small/{{$item.img_url}}"></p>
                        <p><a data-aid="{{$item.good_img_id}}" class="del" href="javascript:;">删除</a>
                    </li>
                {{/foreach}}                
                </ul>
            </div>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">使用说明:</th>
        <td class="paddingT15 wordSpacing5">
          <textarea id="content" name="content" style="width:800px; height:400px;">{{$row.content}}</textarea>
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">上下架:</th>
        <td class="paddingT15 wordSpacing5" >
            <input type="radio" name="is_auth" value="1" {{if $row.is_auth|_isset && $row.is_auth eq 1}}checked="checked"{{else}}checked="checked"{{/if}} />上架
            <input type="radio" name="is_auth" value="0" {{if $row.is_auth|_isset && $row.is_auth eq 0}}checked="checked"{{/if}} />下架
            <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">是否显示:</th>
        <td class="paddingT15 wordSpacing5" >
            <input type="radio" name="is_show" value="0" {{if $row.is_show|_isset && $row.is_show eq 0}}checked="checked"{{else}}checked="checked"{{/if}} />否
            <input type="radio" name="is_show" value="1" {{if $row.is_show|_isset && $row.is_show eq 1}}checked="checked"{{/if}} />是
            <label class="field_notice"></label>
        </td>
    </tr>    
    <tr>
        <th class="paddingT15">适用商品:</th>
        <td class="paddingT15 wordSpacing5"><a id="setShop" class="setShop" href="javascript:void(0)">点击设置适用商品</a></td>
    </tr>
        <tr>
        <th class="paddingT15"></th>
        <td class="paddingT15 wordSpacing5">
         <p class="choiceBox" id="choiceBox"></p>
        </td>
    </tr>
    <tr>
    	<th></th>
        <td><p class="choiceBox" id="choiceBox"></p></td>
    </tr>
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            {{if $tid}}
            <input type="button" value="编辑" name="Submit" class="formbtn1" onClick="checkSubmit()">
            <input type="reset" value="重置" name="reset" class="formbtn2">
            {{else}}
            <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
            {{/if}}
        </td>
    </tr>
</table>
</form>
</div>
<div class="Popup" style="display:none" id="selectPopup">
    <div class="selectPopup">
            <h2>设置券适用商品<a class="close">&times;</a></h2>
            <div class="select-con">
                <p class="selectAll"><input type="checkbox" class="check" id="selectAll" checked="false"><label>选择所有</label></p>
                <div class="selectTable">
                </div>
                <p class="center" id="selectBox"><input type="submit" value="提交设置" class="selectBtn"/></p>
            </div>
    </div>
          <div class="shade"></div>
</div>

<div class="selectPopupShop" style="display:none" id="selectPopupShop">
<a class="selectPopupShop-button" onclick="document.getElementById('selectPopupShop').style.display = 'none'">关闭</a>
<table width="800" cellspacing="0" class="dataTable">
	<tbody>
      <tr>
        <td class="paddingT15 wordSpacing5">
          <p>
            <label><input type="radio" value="1" name="shop_type" checked="checked" /> 商圈</label>
            <label><input type="radio" value="2" name="shop_type" /> 商场</label>
            <label><input type="radio" value="3" name="shop_type" /> 品牌</label>
          </p>
        </td>
      </tr>    
      <tr>
        <td class="paddingT15 wordSpacing5">
        	<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>
            <select name="related_id" id="related_id"></select>
            店铺: <input type="text" name="search_name" id="search_name" style="width:150px;" />
            <input type="button" class="formbtn" value="搜索店铺" onclick="search_to()" />
        </td>
      </tr>
      <tr>
          <td class="paddingT15 wordSpacing5">
          	<table class="infoTable">
            	<tr>
                    <th width="300">可选择店铺</th>
                    <th width="200"></th>
                    <th width="300">已选择店铺</th>
                </tr>
                <tr>
                    <td>
                        <select style="height:300px; width:100%" multiple="multiple" name="moveFrom" id="moveFrom"></select>
                    </td>
                    <td>
                      <table border="0" cellspacing="1" cellpadding="0" width="98%">
                            <tr><td style="text-align:center; height:60px; line-height:60px;">
                            <input type="button" value="添　加" id="add"/></td></tr>
                            <tr><td style="text-align:center; height:60px; line-height:60px;">
                            <input type="button" value="删　除" id="delete"/></td></tr>
                      </table>
                    </td>
                    <td>
                    <select style="height:300px; width:100%" multiple="multiple" name="moveTo" id="moveTo">
                    {{foreach from=$ticketRelationShopArray key=key item=item}}
                    <option value="{{$item.shop_id}}">{{$item.shop_name}}</option>
                    {{/foreach}}
                    </select>
                    </td>
            	</tr>                
            </table>          
          </td>
      </tr> 
      <tr>
        <td class="ptb20"><input type="submit" value="确定" name="Submit" class="formbtn" onClick="shopTrue()"></td>
      </tr>
	</tbody> 
</table>
</div>

<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/addImage.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/setshop.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript">
var postImage, editor, uploadSwf, setGood;
$(function(){	
	editor = KindEditor.create('#content',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	setGood = new SelectShop();	
	postImage=new couponImage();
	uploadSwf = new SWFUpload({
        upload_url: "/admin/good/upload?folder=ticket",
        file_size_limit : "1024 KB",
        file_types : "*.jpg;*.gif;*.png",
        file_types_description : "All Files",
        file_upload_limit : "10",
        file_queue_limit : "10",
        file_post_name : "uploadFile",
		post_params: {"user_name" : "{{$uname}}"},
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

    $('#form').validate({
        errorPlacement: function(error, element){
			if(element.attr('name') == 'region_id' || element.attr('name') == 'circle_id' || element.attr('name') == 'shop_id') {
				$(element).parent().find('.field_notice').html(error);
			} else if (element.attr('name') == 'ticket_type' ) {
				$(element).parent().find('.field_notice').html(error);
			} else {
				$(element).next('.field_notice').hide();
				$(element).after(error); 
			}
        },
		success : function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup : false, 
        rules : { 
			ticket_title : {
				required : true
			},
			par_value : {
				required : true,
				number : true
			},
			total : {
				required : true,
				digits : true			
			},
			start_time : {
				required : true
			},
			end_time : {
				required : true
			},
			valid_stime : {
				required : true
			},
			valid_etime : {
				required : true
			},
			ticket_summary : {
				required : true,
				maxlength : 70
			},
			content : {
				required : true
			}
        },
        messages : {
			ticket_title : {
				required : '请输入优惠券标题'
			},
			par_value : {
				required : '请输入优惠券面值',
				number : '必须输入合法的数字(负数，小数)'
			},
			total : {
				required : '请输入优惠券数量',
				digits : '必须输入整数'			
			},
			start_time : {
				required : '券销售开始时间'
			},
			end_time : {
				required : '券销售结束时间'
			},
			valid_stime : {
				required : '券使用开始时间'
			},
			valid_etime : {
				required : '券使用结束时间'
			},
			ticket_summary : {
				required : '请输入优惠券简介',
				maxlength : '70字内(汉字算一个字符)'
			},
			content : {
				required : '请输入使用说明'
			}
        }
    });
	
	$("input[type=radio][name=is_sale]").change(function(){
		if(this.value == 1) {
			$("#sale_code").show();
		} else {
			$("#sale_code").hide();
		}
	});
});


function checkSubmit()
{
	if($("#form").valid())
	{
		if (editor.html() == '') {
			$.dialog.alert('请输入优惠券使用说明');
			return false;
		}
		$('#gids').val(setGood.getCache().join(','));
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}

function associate() {
	$('#selectPopupShop').show();
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
				$.post('/admin/user/get-sel-list', {region_id:$(this).val(), stype : stype}, function(obj){
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
	$.ajax({
		url:"/admin/user/get-shop-list",
		dataType:"json",
		data:{"stype":stype,"related_id":related_id, "region_id":region_id, "sname":sname},
		success:function(data){
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});											
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
	if(!!shopTextStr) {
		$('#choiceBoxShop').html(shopTextStr);
		$('#sids').val(shopIdStr);
	}
	$('#selectPopupShop').hide();
}
</script>
{{include file='admin/footer.php'}}