{{include file='admin/header.php'}}
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn3" href="/admin/ticket/selfpay-list/page:{{$page}}">自定义买单</a></li>
    {{if !$row.ticket_id}}<li><a class="btn3" href="/admin/ticket/user-shop/type:s/uname:{{$uname}}">店铺选择列表</a></li>{{/if}}
    <li><span>{{if $row.ticket_id}}编辑自定义买单{{else}}新建自定义买单{{/if}}</span></li>
  </ul>
</div>

<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="user_name" id="user_name" value="{{$uname}}" />
<input type="hidden" name="tid" id="tid" value="{{$tid}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="gids" id="gids" value="{{$gids}}" />
<input type="hidden" name="sids" id="sids" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">WEB</a></li>
		<li><a href="#tabs-2">WAP</a></li>
	</ul>
	<div id="tabs-1">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">所属店铺:</th>
                <td class="paddingT15 wordSpacing5">
                 {{$row.region_name}} {{$row.circle_name}} {{$row.shop_name}}
                </td>
            </tr>
            <tr>
                <th class="paddingT15">活动名称:</th>
                <td class="paddingT15 wordSpacing5">
                <select name="activity_id" id="activity_id" style="width:300px;">
                        <option value="">请选择活动</option>
                        {{foreach from=$activity key=key item=item}}
                        <option value="{{$item.activity_id}}" {{if $row.activity_id eq $item.activity_id}} selected="selected" {{/if}}>{{$item.activity_name}}</option>
                        {{/foreach}}
                    </select>  
                  <a href="javascript:add_a();" >创建活动</a>
                  <input type="button" class="formbtn2" value="刷新活动" id="refresh_act" />	
                  <label class="field_notice"></label>
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
                <a><span>{{$item.shop_name}}</span><img class="shopDel" src="/images/delete.png" data-sid = "{{$item.shop_id}}"/></a>
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
                <th class="paddingT15">券分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="ticket_class" value="1" {{if $row.ticket_class|_isset && $row.ticket_class eq 1}}checked="checked"{{/if}} /> 商场
                    <input type="radio" name="ticket_class" value="2" {{if $row.ticket_class|_isset && $row.ticket_class eq 2}}checked="checked"{{/if}} /> 品牌
                    <input type="radio" name="ticket_class" value="3" {{if $row.ticket_class|_isset && $row.ticket_class eq 3}}checked="checked"{{/if}} /> 特卖
                    <label class="field_notice"></label>
                </td>    	
            </tr>   
            
            <tr>
                <th class="paddingT15">商品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="ticket_sort" id="ticket_sort" class="querySelect">
                        <option value="">请选择商品分类</option>    	
                        {{foreach from=$storeArray key=key item=item}}
                        <option value="{{$key}}" {{if $row.ticket_sort eq $key}}selected="selected"{{/if}}>{{$item}}</option>
                        {{/foreach}}
                    </select>
                    <label class="field_notice"></label>
                </td>   	
            </tr>  
            <tr>
                <th class="paddingT15">现金券标题:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="{{$row.ticket_title}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">现金券数量:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="total" id="total" value="{{$row.total}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">限购:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="limit_count" id="limit_count" value="{{$row.limit_count}}" /> 件 / 
                  <select name="limit_unit" id="limit_unit">
                        <option value="Activity" {{if $row.limit_unit eq 'Activity'}} selected="selected" {{/if}}>场</option>
                        <option value="Hour" {{if $row.limit_unit eq 'Hour'}} selected="selected" {{/if}} >小时</option>
                        <option value="Day" {{if $row.limit_unit eq 'Day'}} selected="selected" {{/if}} >天</option>
                        <option value="Week" {{if $row.limit_unit eq 'Week'}} selected="selected" {{/if}} >周</option>
                        <option value="Weekly" {{if $row.limit_unit eq 'Weekly'}} selected="selected" {{/if}} >自然周</option>
                        <option value="Month" {{if $row.limit_unit eq 'Month'}} selected="selected" {{/if}}>月</option>
                        <option value="Monthly" {{if $row.limit_unit eq 'Monthly'}} selected="selected" {{/if}}>自然月</option>
                        <option value="Minutes" {{if $row.limit_unit eq 'Minutes'}} selected="selected" {{/if}}>分钟</option>
                    </select>  
                  <label class="field_notice"></label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">销售有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="start_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="{{if $row.start_time}}{{$row.start_time|date_format:'%Y-%m-%d %H:%M'}}{{/if}}" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="end_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'start_time\',{H:1})}'})" value="{{if $row.end_time}}{{$row.end_time|date_format:'%Y-%m-%d %H:%M'}}{{/if}}"/>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">使用有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="valid_stime" id="valid_stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="{{if $row.valid_stime}}{{$row.valid_stime|date_format:'%Y-%m-%d %H:%M'}}{{/if}}" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="valid_etime" id="valid_etime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'valid_stime\',{H:1})}'})" value="{{if $row.valid_etime}}{{$row.valid_etime|date_format:'%Y-%m-%d %H:%M'}}{{/if}}"/>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">券简介:</th>
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
                </td>
            </tr>
            <tr>
                <th class="paddingT15">WAP详情:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;">{{$row.wap_content}}</textarea>
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
    {{if $wapImgData}}
    <table class="infoTable">
        <tbody>
          <tr>
              <td width="150" class="paddingT15 wordSpacing5">排序</td>
              <td class="paddingT15 wordSpacing5">图片</td>
              <td class="paddingT15 wordSpacing5">创建时间</td>
              <td class="paddingT15 wordSpacing5">操作</td>
          </tr>
          {{foreach from=$wapImgData key=key item=item}} 
          <tr id="imgCol_{{$item.id}}">
              <td class="paddingT15 wordSpacing5"><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.id}}" required="1" class="node_name editable" action="img-ajax-col">{{$item.sequence}}</span></td>
              <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.IMG_URL}}/buy/ticketwap/{{$item.img_url}}" class="makesmall" max_width="400" /></td>
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
                {{if $tid}}
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


<div class="Popup" style="display:none"  id="selectPopup">
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
        <td class="ptb20"><input type="button" value="确定" name="Submit" class="formbtn" onClick="shopTrue()"></td>
      </tr>
	</tbody> 
</table>
</div>

<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/addImage.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/setshop.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
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
			if(element.is(':radio') || element.is(':checkbox')) {
				error.appendTo(element.parent());
			}else if(element.attr('name') == 'region_id' || element.attr('name') == 'circle_id' || element.attr('name') == 'shop_id') {
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
            activity_id : {
            	required : true
            },
			sale_code : {
				required : function(){
					return $("#sale_yes").attr("checked") == "checked";
				}
			},
			ticket_title : {
				required : true
			},
			ticket_class : {
				required : true
			},
			total : {
				required : true,
				digits : true			
			},
			limit_count : {
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
			}
        },
        messages : {
            activity_id : {
        		required : '请选择活动名称'
        	},
			sale_code : {
				required : '请输入特卖场次'
			},
			ticket_title : {
				required : '请输入自助买单券标题'
			},
			ticket_class : {
				required : '请选择券分类'
			},
			total : {
				required : '请输入现金券数量',
				digits : '必须输入整数'			
			},
			limit_count : {
				required : '请输入限购数量',
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
				required : '请输入券简介',
				maxlength : '70字内(汉字算一个字符)'
			}
        }
    });

	$("#choiceBoxShop").on('click','.shopDel',function(){
		var _this = $(this);
		$.dialog({
			title:'警告',
			content: '是否确认取消该店铺与当前游惠的关联？',
			ok: function() {
				$.ajax({
					url:"/admin/ticket/shop-del",
					dataType:"json",
					data:{"tid" : $("#tid").val(), "sid": _this.attr("data-sid")},
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
		
	$('#refresh_act').click(function(){
		var _this = $('#activity_id');
		var user_name = $('#user_name').val();
		_this.empty();
		_this.append($("<option>").text('请选择活动').val(''));
		$.post('/admin/ticket/get-act', {user_name:user_name}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.activity_name).val(s.activity_id));
			});
			alert('已刷新');
		});	
	});
	
	$("input[type=radio][name=is_sale]").change(function(){
		if(this.value == 1) {
			$("#sale_code").show();
		} else {
			$("#sale_code").hide();
		}
	});
});

$(function() {
	$( "#tabs" ).tabs();
	//$( "#tabs2" ).tabs();
});
  
function add_a() {
	$.dialog({
		title: '添加活动',
		content: '活动名称：<br/><br/>'
				 + '<input type="text" name="aname" id="aname" /><br/>',
		ok: function () {
			var aname = $('#aname').val();
			var user_name = $('#user_name').val();
			if(aname == '') {
				alert('活动名称不能为空');
				return false;
			} else {
				var url = '/' + _M + '/' + _C + '/add-activity';
				$.post(url, {aname:aname, user_name:user_name}, function(data){
					var obj = eval('(' + data + ')');
					if (obj.res == 1) {
						$.dialog({
							title : '结果',
							content : obj.msg, 
							okValue: '确定',
							ok: true
						});							
					}
				});
			}
		},
		cancel: true
	});
}

function checkSubmit()
{
	if($("#form").valid())
	{
		if (editor.html() == '') {
			$.dialog.alert('请输入券使用说明');
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
	
	$('#choiceBoxShop').html(shopTextStr);
	$('#sids').val(shopIdStr);

	$('#selectPopupShop').hide();
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
                    url:'/admin/ticket/wap-img-del',
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