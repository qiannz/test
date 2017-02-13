{{include file='admin/header.php'}}
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/discount/list/page:{{$page}}">折扣管理</a></li>
    <li><span>{{if $row.discount_id}}编辑折扣{{else}}新建折扣{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" enctype="multipart/form-data" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="did" id="did" value="{{$row.discount_id}}" />
<input type="hidden" name="bids" id="bids" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">WEB</a></li>
		<li><a href="#tabs-2">WAP</a></li>
	</ul>
	<div id="tabs-1">
		<table class="infoTable">
		      <tr>
		        <th class="paddingT15">折扣标题:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" type="text" name="title" id="title" value="{{$row.title}}" style="width:400px;" />
		          <label class="field_notice"></label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">开始时间:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" style="width:140px;" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="{{if $row.stime}}{{$row.stime|date_format:'%Y-%m-%d %H:%I:%S'}}{{/if}}" placeholder="开始时间"/>
		          <label class="field_notice"></label>
		        </td>
		      </tr>
              
              <tr>
		        <th class="paddingT15">结束时间:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" style="width:140px;" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})" value="{{if $row.etime}}{{$row.etime|date_format:'%Y-%m-%d %H:%I:%S'}}{{/if}}" placeholder="结束时间"/>
		          <label class="field_notice"></label>
		        </td>
		      </tr>
		       <tr>
		        <th class="paddingT15">活动地点:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" name="discount_address" id="discount_address" value="{{$row.address}}" style="width:600px;" type="text">
		          <label class="field_notice"></label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">活动类型:</th>
		        <td class="paddingT15 wordSpacing5">
					<select name="type_id" id="type_id">
		            	<option value="">请选择活动类型</option>
		                {{foreach from=$sortTicketArray key=key item=item}}
		                <option value="{{$item.sort_detail_id}}" {{if $row.type_id eq $item.sort_detail_id}} selected="selected" {{/if}}>{{$item.sort_detail_name}}</option>
		                {{/foreach}}
		            </select>	
		            <label class="field_notice"></label>
		        </td>
		      </tr> 
		      <tr>
		        <th class="paddingT15">折扣分类:</th>
		        <td class="paddingT15 wordSpacing5">
					<select name="category_id" id="category_id">
		            	<option value="">请选择折扣分类</option>
		                {{foreach from=$categoryArray key=key item=item}}
		                <option value="{{$item.sort_detail_id}}" {{if $row.category_id eq $item.sort_detail_id}} selected="selected" {{/if}}>{{$item.sort_detail_name}}</option>
		                {{/foreach}}
		            </select>	
		            <label class="field_notice"></label>
		        </td>
		      </tr> 
		      <tr>
		        <th class="paddingT15">折扣力度:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" style="width:140px;" type="text" name="discount_start" id="discount_start" placeholder="最小折扣" value="{{$row.discount_start}}"/> - 
		          <input class="infoTableFile2" style="width:140px;" type="text" name="discount_end" id="discount_end" placeholder="最大折扣" value="{{$row.discount_end}}" />
		          <label class="field_notice"></label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">促销信息:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="promotion" id="promotion" value="{{$row.promotion}}"/>
				  <label class="field_notice"></label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">所属行政区:</th>
		        <td class="paddingT15 wordSpacing5">
		            <select name="region_id" id="region_id" autocomplete="off">
		            	<option value="">请选择行政区</option>
		                {{foreach from=$regionArray key=key item=item}}
		                <option value="{{$key}}" {{if $row.region_id eq $key}}selected="selected"{{/if}}>{{$item}}</option>
		                {{/foreach}}
		            </select>
		        </td>
		      </tr>  
		      <tr>
		        <th class="paddingT15">所属商圈:</th>
		        <td class="paddingT15 wordSpacing5">
		         	<select name="circle_id" id="circle_id" autocomplete="off">
		            	<option value="">请选择商圈</option>
		            	{{foreach from=$circleArray key=key item=item}}
		            	<option value="{{$item.id}}" {{if $row.circle_id eq $item.id}}selected="selected"{{/if}}>{{$item.name}}</option>
		            	{{/foreach}}
		            </select>
		            <label class="field_notice"></label>
		        </td>
		      </tr>  
		      <tr>
		        <th class="paddingT15">所属商场:</th>
		        <td class="paddingT15 wordSpacing5">
		            <select name="market_id" id="market_id" autocomplete="off">
		            	<option value="">请选择商场</option>
		            	{{foreach from=$marketArray key=key item=item}}
		            	<option value="{{$item.id}}" {{if $row.market_id eq $item.id}}selected="selected"{{/if}}>{{$item.name}}</option>
		            	{{/foreach}}
		            </select>	
		            <label class="field_notice"></label>
		        </td>
		      </tr>  
		      <tr>
		        <th class="paddingT15">所属品牌:</th>
		        <td class="paddingT15 wordSpacing5">
		            <a href="javascript:associate()">点击选择关联品牌</a>
		            <label class="field_notice"></label>
		        </td>
		      </tr> 
		      <tr>
		        <th class="paddingT15"></th>
		        <td class="paddingT15 wordSpacing5">
		           <span id="choiceBoxBrand" class="choiceBox">
		           {{foreach from=$brandArray key=key item=item}}
		           <a data-id="{{$item.brand_id}}">
		           	<span>{{$item.brand_name_zh}}{{if $item.brand_name_en}}{{$item.brand_name_en}}{{/if}}</span>
		           	<img class="brandDel" src="/images/delete.png" data-bid = "{{$item.brand_id}}"/>
		           	</a>
		           {{/foreach}}
		           </span>
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
				          <div class="imgBox" id="postImageList" style="display:none">
				                <ul class="clearfix">              
				                </ul>
				           </div>
				    </td>
				</tr>   
			    <tr>
			        <th class="paddingT15">折扣内容:</th>
			        <td class="paddingT15 wordSpacing5"><textarea id="content"
			                    name="content" style="width:800px; height:400px;" >{{$row.content}}</textarea>
			        <label class="field_notice"></label></td>
			    </tr>
		    <tr>
		        <th class="paddingT15">WAP折扣详情:</th>
		        <td class="paddingT15 wordSpacing5">
		          <textarea id="wap_content" name="wap_content" style="width:800px; height:400px;">{{$row.wap_content}}</textarea>
		          <label class="field_notice"></label>
		        </td>
		    </tr>
		    <tr>
		        <th class="paddingT15">推荐指数:</th>
		        <td class="paddingT15 wordSpacing5">
		          <select name="star" id="star" style="width:60px;">
		         	<option value="1" {{if $row.star eq '1' }} selected="selected" {{/if}}>1</option>
		         	<option value="2" {{if $row.star eq '2' }} selected="selected" {{/if}}>2</option>
		         	<option value="3" {{if $row.star eq '3' }} selected="selected" {{/if}}>3</option>
		         	<option value="4" {{if $row.star eq '4' }} selected="selected" {{/if}}>4</option>
		         	<option value="5" {{if $row.star eq '5' }} selected="selected" {{/if}}>5</option>
		          </select>
				  <label class="field_notice">数值越大则推荐度越高</label>
		        </td>
		    </tr>
		    <tr>
		        <th class="paddingT15">联系人:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="linker" id="linker" value="{{$row.linker}}"/>
				  <label class="field_notice"></label>
		        </td>
		    </tr>
		    <tr>
		        <th class="paddingT15">联系电话:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="telephone" id="telephone" value="{{$row.telephone}}"/>
				  <label class="field_notice"></label>
		        </td>
		    </tr>
		    <tr>
		    	<th></th>
		    	<td><br/></td>
		    <tr>
		</table>
	</div>
	<div id="tabs-2">
	    <table class="infoTable" id="gallery-table">
	        <tbody>
	            <tr>
	                <td class="paddingT15"><a onclick="addImg(this)" href="javascript:;">[+]</a> 上传图片</td>
	                <td class="paddingT15 wordSpacing5"><input type="file" class="file" name="img_url[]" /></td>
	            </tr>
	        </tbody>
	    </table>
	    {{if $wapImgList}}
	    <table class="infoTable">
	        <tbody>
	          <tr>
	              <td width="150" class="paddingT15 wordSpacing5">排序</td>
	              <td class="paddingT15 wordSpacing5">图片</td>
	              <td class="paddingT15 wordSpacing5">创建时间</td>
	              <td class="paddingT15 wordSpacing5">操作</td>
	          </tr>
	          {{foreach from=$wapImgList key=key item=item}} 
	          <tr id="imgCol_{{$item.id}}">
	              <td class="paddingT15 wordSpacing5"><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.id}}" required="1" class="node_name editable" action="img-ajax-col">{{$item.sequence}}</span></td>
	              <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.IMG_URL}}/buy/discount/{{$item.img_url}}" class="makesmall" max_width="400" /></td>
	              <td class="paddingT15 wordSpacing5">{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
	              <td class="paddingT15 wordSpacing5"><a href="javascript:delConfirm({{$item.id}}, '确认删除','你确定要删除这个图片吗？')">删除</a></td>
	          </tr>
	          {{/foreach}}
	   	 	</tbody>
		</table>
	    {{/if}}
    </div>
    <table class="infoTable">
    	<tbody>
        <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20">
                {{if $row.discount_id}}
                <input type="button" value="编辑" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <input type="reset" value="重置" name="reset" class="formbtn2">
                {{else}}
                <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
                {{/if}}
            </td>
        </tr>   
        </tbody> 
    </table>
</div>
</form>
</div>
<div class="selectPopupShop" style="display:none" id="selectPopupBrand">
<a class="selectPopupShop-button" onclick="document.getElementById('selectPopupBrand').style.display = 'none'">关闭</a>
<table width="800" cellspacing="0" class="dataTable">
	<tbody>  
      <tr>
        <td class="paddingT15 wordSpacing5">
            品牌: <input type="text" name="search_name" id="search_name" style="width:150px;" />
            <input type="button" class="formbtn" value="搜索品牌" onclick="search_to()" />
        </td>
      </tr>
      <tr>
          <td class="paddingT15 wordSpacing5">
          	<table class="infoTable">
            	<tr>
                    <th width="300">可选择品牌</th>
                    <th width="200"></th>
                    <th width="300">已选择品牌</th>
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
                    </select>
                    </td>
            	</tr>                
            </table>          
          </td>
      </tr> 
      <tr>
        <td class="ptb20"><input type="button" value="确定" name="Submit" class="formbtn" onClick="brandTrue()"></td>
      </tr>
	</tbody> 
</table>
</div>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/addImage2.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var postImage, editor, uploadSwf,setGood;
$(function(){
	editor = KindEditor.create('#content',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	postImage=new couponImage();
	uploadSwf = new SWFUpload({
        upload_url: "/admin/discount/upload?folder=discount",
        file_size_limit : "1024 KB",
        file_types : "*.jpg;*.gif;*.png",
        file_types_description : "All Files",
        file_upload_limit : "10",
        file_queue_limit : "10",
        file_post_name : "uploadFile",
		post_params: {"primary_id" : "{{$row.special_id}}"},
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
	$("#choiceBoxBrand").on('click','.brandDel',function(){
		var _this = $(this);
		$.dialog({
			title:'警告',
			content: '是否确认取消该品牌与折扣的关联？',
			ok: function() {
				$.ajax({
					url:"/admin/discount/brand-del",
					dataType:"json",
					data:{"did" : $("#did").val(), "bid": _this.attr("data-bid")},
					success:function(data){
						if(data.status == 'ok') {
							_this.parent("a").remove();
						}
					},
					error:function(){
					}
				});	
			},
			cancel: true
		});
	});
	
	$( "#tabs" ).tabs();
	
	attachAddButtonEvent('add', 'moveFrom', "moveTo", '请选择可选的品牌!');
	attachDeleteButtonEvent('delete', 'moveFrom', "moveTo", "请选择要删除的品牌");

	$('#region_id').change(function(){
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('请选择商圈').val(''));
		$.post('/admin/good/get-circle', {id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});

	$('#circle_id').change(function(){
		var _this = $('#market_id');
		_this.empty();
		_this.append($("<option>").text('请选择商场').val(''));
		$.post('/admin/shop/get-market', {region_id:$('#region_id').val(), circle_id:$('#circle_id').val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});
	
	
	$('#form').validate({
        errorPlacement: function(error, element){
			$(element).next('.field_notice').hide();
			$(element).after(error); 
        },
		success : function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup : false, 
        rules : { 
			title : {
				required : true
			},
			stime : {
				required : true
			},
			etime : {
				required : true
			},
			discount_address : {
				required : true
			}
        },
        messages : {
			title : {
				required : '请输入折扣标题'
			},
			stime : {
				required : '请输入活动开始时间',
			},
			etime : {
				required : '请输入活动结束时间',			
			},
			discount_address : {
				required : '请输入活动地址'
			}
        }
    });
});

function checkSubmit()
{
	if($("#form").valid())
	{
		if($("#did").val() == 0) {
			if($("input[type=file]").val().length == 0) {
				alert('至少上传一张折扣图片');
				return false;
			}
		}
		if (editor.html() == '') {
			$.dialog.alert('请输入折扣内容');
			return false;
		}
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}

function associate() {
	$("#moveTo").empty('');
	$("#choiceBoxBrand a").each(function(){
		$("#moveTo").append('<option value="'+$(this).data("id")+'">'+$(this).find('span').html()+'</option>');
	});
	$('#selectPopupBrand').show();
}

function search_to() {
	var store_id = $('#store_id option:selected').val();
	var filter = $('#search_name').val();
	var _this = $('#moveFrom');
	_this.empty();
	$.ajax({
		url:"/admin/discount/get-brand-list",
		dataType:"json",
		data:{"filter":filter},
		success:function(data){
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.brand_name_zh+((s.brand_name_zh&&s.brand_name_en)?"-":"")+s.brand_name_en).val(s.brand_id));
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

function brandTrue(){	
	var brandIdStr = "";
	var brandTextStr = "";
	
	$("#moveTo option").each(function(){
		brandIdStr += $(this).val() + ",";
		brandTextStr += '<a data-id="'+$(this).val()+'">\
           	<span>'+$(this).text()+'</span>\
           	<img class="brandDel" src="/images/delete.png" data-bid = "'+$(this).val()+'"/>\
           	</a>';
	});
	
	$('#bids').val(brandIdStr);
	$("#choiceBoxBrand").html(brandTextStr);
	$('#selectPopupBrand').hide();
}

var Browser = new Object();
Browser.isIE = window.ActiveXObject ? true : false;

function addImg(obj)
{
    var _html = $("#gallery-table tr:first").html();
    _html = "<tr>" + _html.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-") + "</tr>";
    $("#gallery-table tbody").append(_html);

}

function removeImg(obj)
{
    var row = rowindex(obj.parentNode.parentNode);
    var tbl = document.getElementById('gallery-table');

    tbl.deleteRow(row);
}

function rowindex(tr)
{
    if (Browser.isIE)
    {
        return tr.rowIndex;
    }
    else
    {
        table = tr.parentNode.parentNode;
        for (i = 0; i < table.rows.length; i ++ )
        {
            if (table.rows[i] == tr)
            {
                return i;
            }
        }
    }
}

//单项删除确认框
function delConfirm(id,title,message){
        message = message?message:'你确定要删除这条数据吗？';
        $.dialog({
            title: title,
            okValue:'确认',
            cancelValue:'取消',
            width: 230,
            height: 100,
            fixed: true,
            content: message,
            ok: function () {
                $.ajax({
                    type:'GET',
                    url:'/admin/discount/wap-img-del',
                    dataType:'json',
                    data:'id=' + id,
                    success : function(data) {
                        if(data.status == 'ok') {
                            $("#imgCol_" + id).remove();
                        }
                    }
                })
            },
            cancel: function () {
                return true;
            }
        });
} 
</script>
{{include file='admin/footer.php'}}
