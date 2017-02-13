{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/postImage.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/validate.min.js" charset="utf-8"></script>
<script type="text/javascript">
var postImage,uploadSwf;
$(function(){
    $('#form').validate({
        errorPlacement: function(error, element){
			if(element.attr('name') == 'shop_id') {
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
        	good_name : {
                required : true  
            },
            dis_price : {
            	required : true,
				number : true
            },
			region_id : {
				required : true
			},
			circle_id : {
				required : true
			},
			shop_id : {
				required : true
			}
        },
        messages : {
        	good_name : {
                required : '请输入商品标题'
            },
            dis_price : {
                required : '请输入商品折扣价',
				number : '必须输入合法的数字(负数，小数)'
            },
			region_id : {
				required : '请选择所在的区'
			},
			circle_id :  {
				required : '请选择商圈名称'
			},
			shop_id :  {
				required : '请选择店铺名称'
			}
        }
    });
	

	//上传图片调用
	postImage=new bbsPostImage();
	uploadSwf = new SWFUpload({
		upload_url: "/admin/good/upload",
		file_size_limit : "1024 KB",
		file_types : "*.jpg;*.gif;*.png",
		file_types_description : "All Files",
		file_upload_limit : "10",
		file_queue_limit : "10",
		file_post_name : "uploadFile",
		file_dialog_start_handler : fileDialogStart,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		button_image_url : "/images/upload.png",
		button_placeholder_id : "file",
		button_width: 137,
		button_height: 26,
		button_cursor: SWFUpload.CURSOR.HAND,
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		flash_url : "/images/swfupload.swf",
		debug: false
	});
	
	$('#region_id').change(function(){
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('请选择商圈').val(''));
		$('#shop_id').empty();
		$('#shop_id').append($("<option>").text('请选择店铺').val(''));
		$.post('/admin/good/get-circle', {id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});
	
	$('#circle_id').change(function(){
		var _this = $('#shop_id');
		_this.empty();
		_this.append($("<option>").text('请选择店铺').val(''));
		$.post('/admin/good/get-shop', {region_id:$('#region_id').val(), circle_id:$('#circle_id').val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});
	
	{{if $row.good_id}}
		$('#region_id').val('{{$row.region_id}}');
		$('#circle_id').val('{{$row.circle_id}}');
		$('#shop_id').val('{{$row.shop_id}}');
	{{/if}}
	
	$('#refresh_shop').click(function(){
		var _this = $('#shop_id');
		var region_id = $('#region_id').val();
		var circle_id = $('#circle_id').val();
		if(region_id == '' || circle_id == '') {
			alert('请选择行政区和商圈');
			return false;
		}
		_this.empty();
		_this.append($("<option>").text('请选择店铺').val(''));
		$.post('/admin/good/get-shop', {region_id:region_id, circle_id:circle_id}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
			alert('已刷新');
		});	
	});
});

function checkSubmit()
{
	if($("#form").valid())
	{
		$('#img').val(postImage.getCache().toString());
		$(this).attr("disabled", true);
		$("#form").submit();
	}
}
</script>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/goodshop/list/{{$page_str}}">店铺商品列表</a></li>
    <li><span>编辑商品</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="page_str" id="page_str" value="{{$page_str}}" />
<input type="hidden" name="img" id="img" value="" />
<input type="hidden" name="gid" id="gid" value="{{$row.good_id}}" />
<table class="infoTable">
      <tr>
        <th class="paddingT15">商品标题:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="good_name" id="good_name" value="{{$row.good_name}}" style="width:400px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">商品原价:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="org_price" id="org_price" value="{{if $row.org_price gt 0}}{{$row.org_price}}{{/if}}" style="width:100px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
       <tr>
        <th class="paddingT15">商品折扣价:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="dis_price" id="dis_price" value="{{$row.dis_price}}" style="width:100px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属行政区:</th>
        <td class="paddingT15 wordSpacing5">
			<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr> 
      <tr>
        <th class="paddingT15">所属商圈:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="circle_id" id="circle_id">
            	<option value="">请选择商圈</option>
                {{foreach from=$circleArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>  
      <tr>
        <th class="paddingT15">所属店铺:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="shop_id" id="shop_id">
            	<option value="">请选择店铺</option>
                {{foreach from=$shopArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>
            <a href="/admin/shop/add" target="_blank">创建店铺</a>
            <input type="button" class="formbtn1" value="刷新店铺" id="refresh_shop" />	
            <label class="field_notice"></label>
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
                        	<input type="radio" class="ck" gid="{{$item.good_id}}" value="{{$item.good_img_id}}" name="ck" {{if $item.is_first eq 1}}checked="checked"{{/if}}></p>
                    </li>
                {{/foreach}}                
                </ul>
                {{if $row.good_id}}<p class="firstAdd" id="firstAdd"><a href="javascript:;" style="color:#FFF" >设为封面</a></p>{{/if}}
            </div>
        </td>
    </tr>
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <a href="javascript:checkSubmit()"><img src="/images/good.png"/></a>
        </td>
    </tr>
</table>
</form>
</div>
{{include file='admin/footer.php'}}