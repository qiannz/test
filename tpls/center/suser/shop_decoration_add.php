<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/jquery.form.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
<style type="text/css">
.posError {
	height: 18px;
    margin: 5px 0;
    overflow: hidden;
	color:red;
}
</style>
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
                    {{if $user.user_type eq 2}}
                        {{if $_CONF._A eq 'shop-edit'}}
                            <li class="sel" ><a href="javascript:void(0)" >编辑店铺</a></li>
                        {{else}}
                            <li><a href="/home/suser/shop-edit/sid/{{$sid}}" >编辑店铺</a></li>
                        {{/if}}
                    {{elseif $user.user_type eq 3}}
                        {{if in_array(5,$userPermission)}}
                            {{if $_CONF._A eq 'shop-edit'}}
                                <li class="sel" ><a href="javascript:void(0)" >编辑店铺</a></li>
                            {{else}}
                                <li><a href="/home/suser/shop-edit/sid/{{$sid}}" >编辑店铺</a></li>
                            {{/if}}
                        {{/if}}       
                    {{/if}}
                    
                    {{if $user.user_type eq 2 && $shopRow.is_flag eq 1}}
                        {{if $_CONF._A eq 'shop-decoration'}}
                            <li class="sel" ><a href="javascript:void(0)" >店铺推荐</a></li>
                        {{elseif $_CONF._A eq 'shop-decoration-add'}}
                            <li class="sel" ><a href="javascript:void(0)" >店铺推荐</a></li>
                        {{else}}
                        <li><a href="/home/suser/shop-decoration/sid/{{$sid}}">店铺推荐</a></li>
                        {{/if}}	
                    {{/if}}                    
                </ul>
            </div>
            <div class="tableBox">
            	<div class="tableSearch">
                	<label>当前模板：</label>
                    <select name="template" id="template" class="tableSearchSelect">
                          <option value="default">旗舰店默认模板</option>
                	</select>
                    <a class="tableSearchLink" href="/home/suser/shop-decoration/sid/{{$sid}}">推荐列表管理</a>
                    <a class="tableSearchLink" href="/home/shop/show/sid/{{$sid}}/f/1" target="_blank">店铺首页预览</a>
                </div>
                <h3 class="task-tit tableSearchMarginBottom"><span>{{if $did}}编辑{{else}}新增{{/if}}推荐</span></h3>
                <form method="post" action="{{$_CONF.FORM_ACTION}}" id="shop_recomend_add" enctype="multipart/form-data">
                <input type="hidden" name="sid" id="sid" value="{{$sid}}" />
                <input type="hidden" name="did" id="did" value="{{$row.shop_details_id}}" />
                <div class="upFileBox">
                	<p class="inputBox">
                    <label class="label">标题：</label>
                    <input type="text" class="text shopTitInput" name="title" id="title" value="{{$row.detail_title}}" />
                    </p>
                    <p class="error"><span id="title_error"></span></p>
                    
                    <p class="inputBox">
                    <label class="label">链接：</label>
                    <input type="text" class="text shopTitInput" name="url" id="url" value="{{$row.detail_url}}" /></p>
                    <p class="error"><span id="url_error"></span></p>
                    
                    <p class="inputBox">
                    <label class="label">推荐位：</label>
                    <select name="pos_id" id="pos_id" class="tableSearchSelect">
                    	<option value="">请选择...</option>
                        {{foreach from=$position key=key item=item}}	
                    	<option value="{{$item.pos_id}}" {{if $row.pos_id && $row.pos_id eq $item.pos_id}}selected="selected"{{/if}} data-width="{{$item.width}}" data-height="{{$item.height}}">{{$item.pos_name}}</option>
                        {{/foreach}}
                	</select> <span id="pos_error" class="posError"></span>
					</p>
                    <p class="error"><span id="pos_id_error"></span></p>
                    
                     <p class="inputBox">
                    	 <label class="label">图片：</label>
                         <span class="file-img">
                         	<input type="file" name="img" id="img" onChange="document.getElementById('file-name').innerHTML = this.value">
                         </span>
                         <span id="file-name"></span>
                    </p>
                    <p class="error"><span id="img_error"></span></p>
                    {{if $row.detail_img}}
                    <div class="show-file-img">
                    	<a href="{{$_CONF.IMG_URL}}/buy/shop/{{$row.detail_img}}" target="_blank"><img src="{{$_CONF.IMG_URL}}/buy/shop/{{$row.detail_img}}" class="makesmall" max_width="800" /></a>
                    </div>
                    {{/if}}
                  <p class="recommendBtnBox" ><input type="submit"  class="submit" value="{{if $did}}编  辑{{else}}提  交{{/if}}"></p>   
            </div>
            </form>
            </div>
            
            
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript">
$(function(){
 	var options = {
        beforeSubmit : showRequest,
        success : showResponse,
        resetForm: false,
		timeout: 3000
    };  	
	$('form#shop_recomend_add').submit(function(){
		 $(this).ajaxSubmit(options);  
		 return false; //阻止表单默认提交 
	});
	
    /* 缩小大图片 */
    $('.makesmall').each(function(){
        if(this.complete){
            makesmall(this, $(this).attr('max_width'), $(this).attr('max_height'));
        }else{
            $(this).load(function(){
                makesmall(this, $(this).attr('max_width'), $(this).attr('max_height'));
            });
        }
    });
	
	$('#pos_id').change(function(){
		var width = $(this).find("option:selected").attr("data-width");
		var height = $(this).find("option:selected").attr("data-height");
		if($(this).val()) {
			$('#pos_error').html( width + ' * ' + height);
		} else {
			$('#pos_error').html('');
		}
	});
	
	$("#title,#url,#pos_id,#img").blur(function(){
		if($(this).val()) {
			if(this.id == 'url') {
				if(!/.mplife.com/.test(this.value)) { 
					$('#' + this.id + '_error').html('<s class="false"></s>必须是站内链接');
				} else {
					$('#' + this.id + '_error').html('<s class="true"></s>');
				}
			} else {
				$('#' + this.id + '_error').html('<s class="true"></s>');
			}
		} else {
			if(this.id == 'title') {
				$('#' + this.id + '_error').html('<s class="false"></s>请输入标题');
			} else if(this.id == 'url') {
				$('#' + this.id + '_error').html('<s class="false"></s>请输入链接地址');
			}
		}
	});
});

function showRequest(formData, jqForm, options) {
	var flag = true;
	for (var i=0; i < formData.length; i++) {  
		switch(formData[i].name) {
			case 'title':
				if (!formData[i].value) {  
					$('#' + formData[i].name + '_error').html('<s class="false"></s>请输入标题');
					flag = false;
				}
				break;
			case 'url':
				if (!formData[i].value) {  
					$('#' + formData[i].name + '_error').html('<s class="false"></s>请输入链接地址');
					flag = false;
				} else if(!/.mplife.com/.test(formData[i].value)) {
					$('#' + formData[i].name + '_error').html('<s class="false"></s>必须是站内链接');
					flag = false;
				}
				break;
			case 'pos_id':
				if (!formData[i].value) {  
					$('#' + formData[i].name + '_error').html('<s class="false"></s>请选择推荐位');
					flag = false;
				}
				break;
			case 'img':
				if (!formData[i].value && !$("#did").val()) {  
					$('#' + formData[i].name + '_error').html('<s class="false"></s>请选择上传图片');
					flag = false;
				}
				break;	
		}
    }
	return flag;
}

function showResponse(responseText, statusText) {
	if(statusText == 'success') {
		if(responseText == 300) {
			$.dialog.alert('请上传正确尺寸的图片');
		} else if(responseText == 100) {
			if(!$("#did").val()) {
				$("#title, #url, #img").val('');
				$.dialog.alert('上传成功');
			} else {
				$.dialog.alert('编辑成功');
			}
		}		
	}
	$("#title_error,#url_error,#pos_id_error,#img_error, #file-name").html('');
}

function makesmall(obj,w,h){
    srcImage=obj;
    var srcW=srcImage.width;
    var srcH=srcImage.height;
    if (srcW==0)
    {
        obj.src=srcImage.src;
        srcImage.src=obj.src;
        var srcW=srcImage.width;
        var srcH=srcImage.height;
    }
    if (srcH==0)
    {
        obj.src=srcImage.src;
        srcImage.src=obj.src;
        var srcW=srcImage.width;
        var srcH=srcImage.height;
    }

    if(srcW>srcH){
        if(srcW>w){
            obj.width=newW=w;
            obj.height=newH=(w/srcW)*srcH;
        }else{
            obj.width=newW=srcW;
            obj.height=newH=srcH;
        }
    }else{
        if(srcH>h){
            obj.height=newH=h;
            obj.width=newW=(h/srcH)*srcW;
        }else{
            obj.width=newW=srcW;
            obj.height=newH=srcH;
        }
    }
    if(newW>w){
        obj.width=w;
        obj.height=newH*(w/newW);
    }else if(newH>h){
        obj.height=h;
        obj.width=newW*(h/newH);
    }
}
</script>
</body>
</html>