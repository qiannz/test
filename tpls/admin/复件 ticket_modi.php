{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript">
$(function(){
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
	
	$('#refresh_act').click(function(){
		$.post('/admin/ticket/get-act', {}, function(){
			var sid = $('#sid').val();
			location.href = "/admin/ticket/add-voucher/sid:" + sid;
			alert('已刷新');
		});	
	});
});

function checkSubmit()
{
	if($("#form").valid())
	{
		$('#gids').val(setGood.getCache().join(','));
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}
</script>
<style type="text/css">
/*====================弹窗===========================*/
.center {
    text-align: center;
}
	.selectPopup,.addshopPopup{
		  border:1px solid #b0b0b0;
		  background:#fff;
		  position:fixed;
		  left:50%;
		  _position:absolute;
		  z-index:999;
		  padding-bottom:20px
	}
	.selectPopup{
		width:532px;
		margin-left:-266px;
		top:200px;
	}
	.addshopPopup{
	width:620px;
	margin-left:-310px;
	top:50px;
	}
	.Popup h2{
		background:#ff9000;
		height:40px;
		line-height:40px;
		position:relative; 
		padding-left:12px;
		color:#fff;
		font-size:18px;
		font-weight:bold;
	}
	.Popup .close,.Popup .close:hover{
		 position:absolute;
		 top:0;
		 right:5px;
		 text-decoration:none;
		 color:#fff;
		 cursor:pointer;
	}
	.selectPopup .check{
		height:13px;
		width:13px;
		overflow:hidden;
	}
	.selectTable{

		margin-bottom:29px;
		height:133px;
		overflow:auto;
	}
	
	.selectTable table{
		 margin-left:22px;
		}
	.selectAll{
		margin:20px 0 10px 29px;
		overflow:hidden;
		zoom:1;
	}
	.selectAll input,.selectAll label{
		float:left;
		_display:inline;
	}
	.selectAll input{
		margin:2px 8px 0 0;
	}
	.selectAll input{
		vertical-align:middle;
	}
	.selectTable td{
		height:32px;
		vertical-align:middle;
		text-align:center;
		border:1px solid #e3e3e3;
		}
	.selectTable .w1{
		width:30px;
		}
	.selectTable .w2{
		width:420px;
		text-align:left;
		padding-left:6px;
		}
	.selectTable font{
		color:#fd635a;
	}	
	.selectPopup .selectBtn{
		width:135px;
		height:33px;
		line-height:33px;
		color:#fff;
		font-family:"SimSun";
		text-align:center;
		background:url(/images/user/pic.png) no-repeat 0 -225px;
		font-size:14px;
		font-weight:bold;
		border:none;
		cursor:pointer;
	}
	.shade{
		background:#000;
		position:fixed;
		left:0;
		top:0;
		height:100%;
		width:100%;
		z-index:998;
		opacity:0.4;
		filter:alpha(opacity=40);
		_background:none;
		_position:absolute;
	}
	.choiceBox{
			margin-top:25px;
			width:520px;
			overflow:hidden;
			zoom:1;
		}
	.choiceBox a,.choiceBox a:hover{
			background:#f0f0ee;
			padding:3px 8px;
			color:#666;
			text-decoration:none;
			margin:0 16px 10px 0;
			float:left;
			_display:inline;
		}
	.choiceBox font{
			color:#fd635a; 
			padding:0;
		}
	
.choiceBox a, .choiceBox a:hover {
    background: none repeat scroll 0 0 #F0F0EE;
    color: #666666;
    float: left;
    margin: 0 16px 10px 0;
    padding: 3px 8px;
    text-decoration: none;
}	

</style>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/ticket/coupon-list">优惠券列表</a></li>
    {{if !$row.ticket_id}}<li><a class="btn3" href="/admin/ticket/user-shop/type:c/uname:{{$uname}}">店铺选择列表</a></li>{{/if}}
    <li><span>{{if $row.ticket_id}}编辑优惠券{{else}}新建优惠券{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="user_name" id="user_name" value="{{$uname}}" />
<input type="hidden" name="tid" id="tid" value="{{$row.ticket_id}}" />
<input type="hidden" name="gids" id="gids" value="{{$gids}}" />
<table class="infoTable">
    <tr>
        <th class="paddingT15">所属店铺:</th>
        <td class="paddingT15 wordSpacing5">
         {{$row.region_name}} {{$row.circle_name}} {{$row.shop_name}}
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
            <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
            <input type="reset" value="重置" name="reset" class="formbtn2">
        </td>
    </tr>
</table>
</form>
</div>
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
<script type="text/javascript" src="/js/admin/addImage.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/setshop.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" charset="utf-8" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
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

});

</script>
{{include file='admin/footer.php'}}