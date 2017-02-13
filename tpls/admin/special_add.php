{{include file='admin/header.php'}}
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative; width:200px;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
	<p>用户管理</p>
	<ul class="subnav">
		<li><a class="btn4" href="/admin/special/list/page:{{$page}}">专题管理</a></li>
		<li><span>{{if $row.special_id}}编辑专题{{else}}新建专题{{/if}}</span></li>
	</ul>
</div>
<div class="info">
	<form method="POST" enctype="multipart/form-data" action="{{$_CONF.FORM_ACTION}}" id="form">
	    <input type="hidden" name="sid" id="sid" value="{{$row.special_id}}" />
	    <input type="hidden" name="gids" id="gids" value="" />
	    <input type="hidden" name="page" id="page" value="{{$page}}" />
		<table class="infoTable">
			        <tr>
			            <th class="paddingT15">专题标题:</th>
			            <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" type="text" name="title" id="title" value="{{$row.title}}"style="width: 300px;" /> 
			            <label class="field_notice"></label>
			            </td>
			        </tr>
			        <tr>
			            <th class="paddingT15">专题封面图:</th>
			            <td class="paddingT15 wordSpacing5">
			            	<input class="file" type="file" name="cover_img" id="cover_img" value="{{$row.cover_img}}" style="width: 400px;" /> 
			            	<label class="field_notice">图片尺寸 640 * 240</label>
			            </td>
			        </tr>
			        {{if $row.cover_img}}
		            <tr>
		                <th class="paddingT15"></th>
		                <td class="paddingT15 wordSpacing5">
		                  <img id="coverImg" src="{{$_CONF.IMG_URL}}/buy/cover/{{$row.cover_img}}" />
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
				            <div class="imgBox" id="postImageList" style="display:none">
				                <ul class="clearfix">              
				                </ul>
				            </div>
				        </td>
				    </tr>   
			        <tr>
			            <th class="paddingT15">专题内容:</th>
			            <td class="paddingT15 wordSpacing5"><textarea id="content"
			                    name="content" style="width:800px; height:400px;" >{{$row.content}}</textarea>
			                <label class="field_notice"></label></td>
			        </tr>
			        <tr>
			            <th class="paddingT15">关联商品:</th>
			            <td class="paddingT15 wordSpacing5">
			                <a href="javascript:associate()">点击选择关联商品</a>
			                <label class="field_notice"></label>
			            </td>
			        </tr> 
			        <tr>
			            <th class="paddingT15"></th>
			            <td class="paddingT15 wordSpacing5">
			               <span id="choiceBoxBrand" class="choiceBox">
					           {{foreach from=$goodArray key=key item=item}}
					           <a data-id="{{$item.id}}"><span>{{$item.name}}</span><img class="brandDel" src="/images/delete.png" data-gid = "{{$item.id}}"/></a>
			                   {{/foreach}}
					       </span>
			            </td>
			        </tr> 
			        <tr>
			            <th class="paddingT15"></th>
			            <td class="ptb20">{{if $row.special_id}} <input type="button" value="编辑"
			                name="Submit" class="formbtn1" onClick="checkSubmit()"> <input
			                type="reset" value="重置" name="reset" class="formbtn2"> {{else}} <input
			                type="button" value="提交" name="Submit" class="formbtn1"
			                onClick="checkSubmit()"> {{/if}}
			            </td>
			        </tr>      
		</table>
	</form>
</div>
<div class="selectPopupShop" style="display:none" id="selectPopupGood">
	<a class="selectPopupShop-button"
		onclick="document.getElementById('selectPopupGood').style.display = 'none'">关闭</a>
	<table width="800" cellspacing="0" class="dataTable">
		<tbody>
			<tr>
				<td class="paddingT15 wordSpacing5">商品: <input type="text"
					name="search_name" id="search_name" style="width: 150px;" /> <input
					type="button" class="formbtn" value="搜索商品" onclick="search_to()" />
				</td>
			</tr>
			<tr>
				<td class="paddingT15 wordSpacing5">
					<table class="infoTable">
						<tr>
							<th width="300">可选择商品</th>
							<th width="200"></th>
							<th width="300">已选择商品</th>
						</tr>
						<tr>
							<td><select style="height: 300px; width: 100%"
								multiple="multiple" name="moveFrom" id="moveFrom"></select></td>
							<td>
								<table border="0" cellspacing="1" cellpadding="0" width="98%">
									<tr>
										<td
											style="text-align: center; height: 60px; line-height: 60px;">
											<input type="button" value="添　加" id="add" />
										</td>
									</tr>
									<tr>
										<td
											style="text-align: center; height: 60px; line-height: 60px;">
											<input type="button" value="删　除" id="delete" />
										</td>
									</tr>
								</table>
							</td>
							<td>
                            <select style="height: 300px; width: 100%" multiple="multiple" name="moveTo" id="moveTo">

							</select>
                            </td>
						</tr>
                        <tr>
                        	<td class="ptb20"><input type="button" value="确定" name="Submit" class="formbtn" onClick="goodTrue()"></td>
                        </tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/addImage2.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<!-- <script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script> -->
<script type="text/javascript">
var postImage, editor, uploadSwf;
$(function(){
	editor = KindEditor.create('#content',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	postImage=new couponImage();
	uploadSwf = new SWFUpload({
        upload_url: "/admin/special/upload?folder=special",
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
			content: '是否确认取消该商品与专题的关联？',
			ok: function() {
				$.ajax({
					url:"/admin/special/good-del",
					dataType:"json",
					data:{"sid" : $("#sid").val(), "gid": _this.attr("data-gid")},
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
	
	attachAddButtonEvent('add', 'moveFrom', "moveTo", '请选择可选的商品!');
	attachDeleteButtonEvent('delete', 'moveFrom', "moveTo", "请选择要删除的商品");
	
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
			content : {
				required : true
			}
        },
        messages : {
			title : {
				required : '请输入折扣标题'
			},
			content : {
				required : '请输入使用说明'
			}
        }
    });
});

function checkSubmit()
{
	if($("#form").valid())
	{	
		if (editor.html() == '') {
			$.dialog.alert('请输入专题内容');
			return false;
		}
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}

function search_to() {
	var store_id = $('#store_id option:selected').val();
	var filter = $('#search_name').val();
	var _this = $('#moveFrom');
	
	_this.empty();
	$.ajax({
		url:"/admin/special/get-good-list",
		dataType:"json",
		data:{"filter":filter},
		success:function(data){
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.ticket_title).val(s.ticket_id));
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

function associate() {
	$("#moveTo").empty('');
	$("#choiceBoxBrand a").each(function(){
		$("#moveTo").append('<option value="'+$(this).data("id")+'">'+$(this).find('span').html()+'</option>');
	});
	$('#selectPopupGood').show();
}

function goodTrue(){	
	var goodIdStr = "";
	var goodTextStr = "";
	
	$("#moveTo option").each(function(){
		goodIdStr += $(this).val() + ",";
		goodTextStr += '<a data-id="'+$(this).val()+'"><span>' + $(this).text() + '</span><img class="brandDel" src="/images/delete.png" data-gid = "'+$(this).val()+'"/></a>';
	});
	
	$('#gids').val(goodIdStr);
	$("#choiceBoxBrand").html(goodTextStr);
	$('#selectPopupGood').hide();
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
                    url:'/admin/special/wap-img-del',
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
